<?php

namespace App\Controllers;

use App\Models\UserModel;
use App\Models\ResetPasswordTokenModel;
use App\Libraries\AuditService;
use App\Libraries\WhatsAppService;
use App\Libraries\NotificationService;

class Auth extends BaseController
{
    protected UserModel $userModel;
    protected ResetPasswordTokenModel $tokenModel;
    protected AuditService $audit;
    protected WhatsAppService $wa;
    protected NotificationService $notification;

    public function __construct()
    {
        $this->userModel = new UserModel();
        $this->tokenModel = new ResetPasswordTokenModel();
        $this->audit = new AuditService();
        $this->wa = new WhatsAppService();
        $this->notification = new NotificationService();
    }

    public function login()
    {
        if (session()->get('isLoggedIn')) {
            return redirect()->to(base_url('dashboard'));
        }

        if ($this->request->getMethod() === 'POST') {
            $usernameOrEmail = trim($this->request->getPost('username') ?? '');
            $password = trim($this->request->getPost('password') ?? '');

            if (empty($usernameOrEmail) || empty($password)) {
                return redirect()->back()->withInput()->with('error', lang('Auth.login_failed_cred'));
            }

            // Find user by username OR email
            $user = $this->userModel->select('users.*, roles.nama_role, departemen.nama_departemen, departemen.kode_dept')
                                    ->join('roles', 'roles.id_role = users.id_role', 'left')
                                    ->join('departemen', 'departemen.id_departemen = users.id_departemen', 'left')
                                    ->groupStart()
                                        ->where('users.username', $usernameOrEmail)
                                        ->orWhere('users.email', $usernameOrEmail)
                                    ->groupEnd()
                                    ->first();

            if (!$user) {
                return redirect()->back()->withInput()->with('error', lang('Auth.login_failed_cred'));
            }

            if ((int)$user['status_aktif'] !== 1) {
                return redirect()->back()->withInput()->with('error', lang('Auth.login_inactive'));
            }

            if (!password_verify($password, $user['password'])) {
                $this->audit->log("Percobaan login gagal untuk username: {$usernameOrEmail}", 'Autentikasi', $user['id_user']);
                return redirect()->back()->withInput()->with('error', lang('Auth.login_failed_pass'));
            }

            // Respect user's selected interface locale or load saved preference
            helper('cookie');
            $chosenLocale = session()->get('user_locale') ?: get_cookie('user_locale');
            if ($chosenLocale && in_array($chosenLocale, ['id', 'en'], true)) {
                $locale = $chosenLocale;
                if (($user['preferred_language'] ?? '') !== $locale) {
                    $this->userModel->update($user['id_user'], ['preferred_language' => $locale]);
                }
            } else {
                $locale = !empty($user['preferred_language']) ? $user['preferred_language'] : 'id';
            }
            session()->set('user_locale', $locale);
            service('request')->setLocale($locale);

            // Login successful
            session()->set([
                'isLoggedIn'      => true,
                'user_id'         => $user['id_user'],
                'username'        => $user['username'],
                'nama_lengkap'    => $user['nama_lengkap'],
                'email'           => $user['email'],
                'no_whatsapp'     => $user['no_whatsapp'],
                'id_role'         => $user['id_role'],
                'nama_role'       => $user['nama_role'],
                'id_departemen'   => $user['id_departemen'],
                'nama_departemen' => $user['nama_departemen'],
                'kode_dept'       => $user['kode_dept'],
                'user_locale'     => $locale,
            ]);

            $this->audit->log("Pengguna {$user['nama_lengkap']} ({$user['nama_role']}) berhasil login", 'Autentikasi', $user['id_user']);

            $redirectUrl = session()->get('redirect_url') ?? base_url('dashboard');
            session()->remove('redirect_url');

            return redirect()->to($redirectUrl)->with('success', lang('Auth.login_welcome', [$user['nama_lengkap']]));
        }

        return view('auth/login', ['title' => lang('Auth.login_title')]);
    }

    public function logout()
    {
        $userId = session()->get('user_id');
        $nama = session()->get('nama_lengkap');

        if ($userId) {
            $this->audit->log("Pengguna {$nama} telah keluar (logout)", 'Autentikasi', $userId);
        }

        session()->destroy();
        return redirect()->to(base_url('login'))->with('success', lang('Auth.logout_success'));
    }

    public function forgotPassword()
    {
        if ($this->request->getMethod() === 'POST') {
            $email = trim($this->request->getPost('email') ?? '');
            $user = $this->userModel->where('email', $email)->first();

            if (!$user) {
                return redirect()->back()->withInput()->with('error', lang('Auth.email_not_found'));
            }

            $token = $this->tokenModel->generateToken($email);
            $resetUrl = base_url('reset-password/' . $token);

            // Enqueue notification via Notification Queue with Email failover
            $this->notification->notifyResetPassword($user['no_whatsapp'], $user['email'], $user['nama_lengkap'], $resetUrl);
            $this->wa->notifyResetPassword($user['no_whatsapp'], $user['nama_lengkap'], $resetUrl);
            $this->audit->log("Permintaan reset password untuk {$email}", 'Autentikasi', $user['id_user']);

            // In simulation mode, display reset link in flash data for direct testing
            session()->setFlashdata('reset_link_simulation', $resetUrl);
            return redirect()->back()->with('success', lang('Auth.reset_sent_success'));
        }

        return view('auth/forgot_password', ['title' => lang('Auth.forgot_title')]);
    }

    public function resetPassword($token = null)
    {
        if (empty($token)) {
            return redirect()->to(base_url('login'))->with('error', lang('Auth.token_invalid'));
        }

        $tokenRecord = $this->tokenModel->isValidToken($token);
        if (!$tokenRecord) {
            return redirect()->to(base_url('forgot-password'))->with('error', lang('Auth.token_invalid'));
        }

        if ($this->request->getMethod() === 'POST') {
            $password = trim($this->request->getPost('password') ?? '');
            $passwordConfirm = trim($this->request->getPost('password_confirm') ?? '');

            if (strlen($password) < 6) {
                return redirect()->back()->with('error', lang('Auth.pass_min_length'));
            }

            if ($password !== $passwordConfirm) {
                return redirect()->back()->with('error', lang('Auth.pass_not_match'));
            }

            $user = $this->userModel->where('email', $tokenRecord['email'])->first();
            if ($user) {
                $this->userModel->update($user['id_user'], [
                    'password' => password_hash($password, PASSWORD_BCRYPT)
                ]);

                // Delete used token
                $this->tokenModel->where('token', $token)->delete();

                $this->audit->log("Kata sandi berhasil diatur ulang untuk {$user['username']}", 'Autentikasi', $user['id_user']);
                return redirect()->to(base_url('login'))->with('success', lang('Auth.reset_success'));
            }
        }

        return view('auth/reset_password', [
            'title' => lang('Auth.reset_title'),
            'token' => $token,
            'email' => $tokenRecord['email'],
        ]);
    }

    public function switchRole()
    {
        $targetUserId = (int) $this->request->getPost('switch_user_id');
        $user = $this->userModel->getUserWithRelations($targetUserId);

        if (!$user) {
            return redirect()->back()->with('error', 'Pengguna target switch tidak ditemukan.');
        }

        $locale = !empty($user['preferred_language']) ? $user['preferred_language'] : (session()->get('user_locale') ?: 'id');
        service('request')->setLocale($locale);

        session()->set([
            'isLoggedIn'      => true,
            'user_id'         => $user['id_user'],
            'username'        => $user['username'],
            'nama_lengkap'    => $user['nama_lengkap'],
            'email'           => $user['email'],
            'no_whatsapp'     => $user['no_whatsapp'],
            'id_role'         => $user['id_role'],
            'nama_role'       => $user['nama_role'],
            'id_departemen'   => $user['id_departemen'],
            'nama_departemen' => $user['nama_departemen'],
            'kode_dept'       => $user['kode_dept'],
            'user_locale'     => $locale,
        ]);

        $this->audit->log("Role Switch: Beralih ke akun {$user['nama_lengkap']} ({$user['nama_role']})", 'System Testing', $user['id_user']);
        return redirect()->to(base_url('dashboard'))->with('success', "Berhasil beralih role ke: {$user['nama_role']} ({$user['nama_lengkap']})");
    }
}
