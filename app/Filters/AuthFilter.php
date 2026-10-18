<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class AuthFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $session = session();
        if (!$session->has('isLoggedIn') || !$session->get('isLoggedIn')) {
            $session->set('redirect_url', current_url());
            return redirect()->to(base_url('login'))->with('error', 'Silakan login terlebih dahulu untuk mengakses sistem.');
        }

        // Check if user is still active in database
        $userModel = new \App\Models\UserModel();
        $user = $userModel->find($session->get('user_id'));
        if (!$user || (int)$user['status_aktif'] !== 1) {
            $session->destroy();
            return redirect()->to(base_url('login'))->with('error', 'Akun Anda dinonaktifkan atau tidak ditemukan.');
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Do nothing
    }
}
