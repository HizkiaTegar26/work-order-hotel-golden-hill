<?php

namespace App\Controllers;

use App\Models\DepartemenModel;
use App\Libraries\AuditService;

class Departemen extends BaseController
{
    protected DepartemenModel $deptModel;
    protected AuditService $audit;

    public function __construct()
    {
        $this->deptModel = new DepartemenModel();
        $this->audit = new AuditService();
    }

    public function index()
    {
        return view('departemen/index', [
            'title'      => 'Manajemen Departemen Hotel',
            'departemen' => $this->deptModel->findAll(),
        ]);
    }

    public function store()
    {
        $nama = trim($this->request->getPost('nama_departemen') ?? '');
        $kode = strtoupper(trim($this->request->getPost('kode_dept') ?? ''));

        if (empty($nama) || empty($kode)) {
            return redirect()->back()->with('error', 'Nama dan kode departemen wajib diisi.');
        }

        $this->deptModel->insert([
            'nama_departemen' => $nama,
            'kode_dept'       => $kode,
        ]);

        $this->audit->log("Menambahkan departemen baru: {$nama} ({$kode})", 'Departemen');
        return redirect()->to(base_url('departemen'))->with('success', "Departemen {$nama} berhasil ditambahkan.");
    }

    public function update($id)
    {
        $dept = $this->deptModel->find($id);
        if (!$dept) return redirect()->to(base_url('departemen'))->with('error', 'Departemen tidak ditemukan.');

        $nama = trim($this->request->getPost('nama_departemen') ?? '');
        $kode = strtoupper(trim($this->request->getPost('kode_dept') ?? ''));

        $this->deptModel->update($id, [
            'nama_departemen' => $nama,
            'kode_dept'       => $kode,
        ]);

        $this->audit->log("Memperbarui departemen {$dept['kode_dept']} - {$nama}", 'Departemen');
        return redirect()->to(base_url('departemen'))->with('success', 'Data departemen berhasil diperbarui.');
    }

    public function delete($id)
    {
        $dept = $this->deptModel->find($id);
        if (!$dept) return redirect()->to(base_url('departemen'))->with('error', 'Departemen tidak ditemukan.');

        $this->deptModel->delete($id);
        $this->audit->log("Menghapus departemen {$dept['kode_dept']} - {$dept['nama_departemen']}", 'Departemen');
        return redirect()->to(base_url('departemen'))->with('success', "Departemen {$dept['nama_departemen']} berhasil dihapus.");
    }
}
