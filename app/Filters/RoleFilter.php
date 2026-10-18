<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class RoleFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $session = session();
        if (!$session->has('isLoggedIn')) {
            return redirect()->to(base_url('login'))->with('error', 'Silakan login terlebih dahulu.');
        }

        if (empty($arguments)) {
            return;
        }

        $userRole = strtolower(trim($session->get('nama_role') ?? ''));
        $roleId = (int) ($session->get('id_role') ?? 0);

        // Normalize argument list
        // 'admin' => id_role 1, 'kadep' => id_role 2, 'staf' => id_role 3
        $allowed = false;
        foreach ($arguments as $arg) {
            $normalized = strtolower(trim($arg));
            if ($normalized === 'admin' && ($roleId === 1 || str_contains($userRole, 'admin'))) {
                $allowed = true;
                break;
            }
            if (($normalized === 'kadep' || $normalized === 'kepala departemen') && ($roleId === 2 || str_contains($userRole, 'kepala'))) {
                $allowed = true;
                break;
            }
            if ($normalized === 'staf' && ($roleId === 3 || str_contains($userRole, 'staf'))) {
                $allowed = true;
                break;
            }
        }

        if (!$allowed) {
            return redirect()->to(base_url('dashboard'))->with('error', 'Akses ditolak: Anda tidak memiliki izin untuk mengakses halaman tersebut.');
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Do nothing
    }
}
