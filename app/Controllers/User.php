<?php

namespace App\Controllers;

use App\Models\UserModel;
use App\Models\RoleModel;
use App\Models\DepartemenModel;
use App\Libraries\AuditService;

class User extends BaseController
{
    protected UserModel $userModel;
    protected RoleModel $roleModel;
    protected DepartemenModel $deptModel;
    protected AuditService $audit;

    public function __construct()
    {
        $this->userModel = new UserModel();
        $this->roleModel = new RoleModel();
        $this->deptModel = new DepartemenModel();
        $this->audit = new AuditService();
    }

    public function index()
    {
        $users = $this->userModel->getUserWithRelations();
        return view('user/index', [
            'title'      => 'Manajemen Pengguna & Hak Akses',
            'users'      => $users,
            'roles'      => $this->roleModel->findAll(),
            'departemen' => $this->deptModel->getAllCached(),
        ]);
    }

    public function store()
    {
        $nama     = trim($this->request->getPost('nama_lengkap') ?? '');
        $username = trim($this->request->getPost('username') ?? '');
        $password = trim($this->request->getPost('password') ?? '');
        $email    = trim($this->request->getPost('email') ?? '');
        $wa       = trim($this->request->getPost('no_whatsapp') ?? '');
        $idRole   = (int) $this->request->getPost('id_role');
        $idDept   = (int) $this->request->getPost('id_departemen');

        if (empty($nama) || empty($username) || empty($password) || empty($email)) {
            return redirect()->back()->with('error', 'Semua kolom bertanda bintang wajib diisi.');
        }

        // Check unique username
        if ($this->userModel->where('username', $username)->first()) {
            return redirect()->back()->with('error', "Username '{$username}' sudah digunakan.");
        }

        $now = date('Y-m-d H:i:s');
        $this->userModel->insert([
            'nama_lengkap'  => $nama,
            'username'      => $username,
            'password'      => password_hash($password, PASSWORD_BCRYPT),
            'email'         => $email,
            'no_whatsapp'   => $wa,
            'id_role'       => $idRole,
            'id_departemen' => $idDept,
            'status_aktif'  => 1,
            'created_at'    => $now,
            'updated_at'    => $now,
        ]);

        $this->audit->log("Membuat akun pengguna baru: {$username} ({$nama})", 'Manajemen User');
        return redirect()->to(base_url('users'))->with('success', "Pengguna {$nama} berhasil ditambahkan.");
    }

    public function update($id)
    {
        $user = $this->userModel->find($id);
        if (!$user) return redirect()->to(base_url('users'))->with('error', 'Pengguna tidak ditemukan.');

        $nama     = trim($this->request->getPost('nama_lengkap') ?? '');
        $email    = trim($this->request->getPost('email') ?? '');
        $wa       = trim($this->request->getPost('no_whatsapp') ?? '');
        $idRole   = (int) $this->request->getPost('id_role');
        $idDept   = (int) $this->request->getPost('id_departemen');
        $password = trim($this->request->getPost('password') ?? '');

        $updateData = [
            'nama_lengkap'  => $nama,
            'email'         => $email,
            'no_whatsapp'   => $wa,
            'id_role'       => $idRole,
            'id_departemen' => $idDept,
            'updated_at'    => date('Y-m-d H:i:s'),
        ];

        if (!empty($password)) {
            $updateData['password'] = password_hash($password, PASSWORD_BCRYPT);
        }

        $this->userModel->update($id, $updateData);
        $this->audit->log("Memperbarui akun pengguna {$user['username']} ({$nama})", 'Manajemen User');

        return redirect()->to(base_url('users'))->with('success', "Data pengguna {$nama} berhasil diperbarui.");
    }

    public function toggleStatus($id)
    {
        $user = $this->userModel->find($id);
        if (!$user) return redirect()->to(base_url('users'))->with('error', 'Pengguna tidak ditemukan.');

        $newStatus = (int)$user['status_aktif'] === 1 ? 0 : 1;
        $this->userModel->update($id, ['status_aktif' => $newStatus]);

        $statusStr = $newStatus === 1 ? 'diaktifkan' : 'dinonaktifkan';
        $this->audit->log("Mengubah status akun {$user['username']} menjadi {$statusStr}", 'Manajemen User');

        return redirect()->to(base_url('users'))->with('success', "Akun {$user['nama_lengkap']} berhasil {$statusStr}.");
    }

    public function delete($id)
    {
        $user = $this->userModel->find($id);
        if (!$user) return redirect()->to(base_url('users'))->with('error', 'Pengguna tidak ditemukan.');

        if ((int)$id === (int)session()->get('user_id')) {
            return redirect()->to(base_url('users'))->with('error', 'Anda tidak dapat menghapus akun Anda sendiri.');
        }

        $this->userModel->delete($id);
        $this->audit->log("Menghapus akun pengguna {$user['username']}", 'Manajemen User');

        return redirect()->to(base_url('users'))->with('success', "Pengguna {$user['nama_lengkap']} berhasil dihapus.");
    }
}
