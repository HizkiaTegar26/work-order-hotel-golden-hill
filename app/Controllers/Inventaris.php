<?php

namespace App\Controllers;

use App\Models\InventarisBarangModel;
use App\Libraries\AuditService;

class Inventaris extends BaseController
{
    protected InventarisBarangModel $barangModel;
    protected AuditService $audit;

    public function __construct()
    {
        $this->barangModel = new InventarisBarangModel();
        $this->audit = new AuditService();
    }

    public function index()
    {
        $items = $this->barangModel->orderBy('nama_barang', 'ASC')->findAll();

        return view('inventaris/index', [
            'title' => 'Manajemen Stok Suku Cadang',
            'items' => $items,
        ]);
    }

    public function store()
    {
        $kode   = trim($this->request->getPost('kode_barang') ?? '');
        $nama   = trim($this->request->getPost('nama_barang') ?? '');
        $stok   = (int) $this->request->getPost('stok');
        $satuan = trim($this->request->getPost('satuan') ?? 'Pcs');
        $harga  = (float) $this->request->getPost('harga_satuan');

        if (empty($kode) || empty($nama)) {
            return redirect()->back()->with('error', 'Kode dan nama barang wajib diisi.');
        }

        $this->barangModel->insert([
            'kode_barang'  => $kode,
            'nama_barang'  => $nama,
            'stok'         => $stok,
            'satuan'       => $satuan,
            'harga_satuan' => $harga,
        ]);

        $this->audit->log("Menambahkan suku cadang baru: {$nama} ({$kode}) - Stok: {$stok}", 'Inventaris');
        return redirect()->to(base_url('inventaris'))->with('success', "Barang {$nama} berhasil ditambahkan ke inventaris.");
    }

    public function update($id)
    {
        $item = $this->barangModel->find($id);
        if (!$item) return redirect()->to(base_url('inventaris'))->with('error', 'Barang tidak ditemukan.');

        $nama   = trim($this->request->getPost('nama_barang') ?? '');
        $stok   = (int) $this->request->getPost('stok');
        $satuan = trim($this->request->getPost('satuan') ?? 'Pcs');
        $harga  = (float) $this->request->getPost('harga_satuan');

        $this->barangModel->update($id, [
            'nama_barang'  => $nama,
            'stok'         => $stok,
            'satuan'       => $satuan,
            'harga_satuan' => $harga,
        ]);

        $this->audit->log("Memperbarui data inventaris {$item['kode_barang']} - {$nama} (Stok: {$stok})", 'Inventaris');
        return redirect()->to(base_url('inventaris'))->with('success', 'Data inventaris suku cadang berhasil diperbarui.');
    }

    public function delete($id)
    {
        $item = $this->barangModel->find($id);
        if (!$item) return redirect()->to(base_url('inventaris'))->with('error', 'Barang tidak ditemukan.');

        $this->barangModel->delete($id);
        $this->audit->log("Menghapus suku cadang {$item['kode_barang']} - {$item['nama_barang']}", 'Inventaris');
        return redirect()->to(base_url('inventaris'))->with('success', "Item {$item['nama_barang']} berhasil dihapus dari inventaris.");
    }
}
