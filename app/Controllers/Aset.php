<?php

namespace App\Controllers;

use App\Models\AsetHotelModel;
use App\Models\DepartemenModel;
use App\Libraries\QrCodeService;
use App\Libraries\AuditService;

class Aset extends BaseController
{
    protected AsetHotelModel $asetModel;
    protected DepartemenModel $deptModel;
    protected QrCodeService $qrService;
    protected AuditService $audit;

    public function __construct()
    {
        $this->asetModel = new AsetHotelModel();
        $this->deptModel = new DepartemenModel();
        $this->qrService = new QrCodeService();
        $this->audit = new AuditService();
    }

    public function index()
    {
        $assets = $this->asetModel->getAssetsWithDept();
        return view('aset/index', [
            'title'      => 'Manajemen Aset Hotel',
            'assets'     => $assets,
            'departemen' => $this->deptModel->getAllCached(),
        ]);
    }

    public function create()
    {
        return view('aset/create', [
            'title'      => 'Tambah Aset Hotel Baru',
            'departemen' => $this->deptModel->getAllCached(),
        ]);
    }

    public function store()
    {
        $kodeAset = trim($this->request->getPost('kode_aset') ?? '');
        $namaAset = trim($this->request->getPost('nama_aset') ?? '');
        $idDept   = (int) $this->request->getPost('id_departemen');
        $lokasi   = trim($this->request->getPost('lokasi_spesifik') ?? '');
        $kondisi  = $this->request->getPost('kondisi') ?? 'Baik';

        if (empty($kodeAset) || empty($namaAset) || empty($idDept)) {
            return redirect()->back()->withInput()->with('error', 'Semua kolom wajib diisi.');
        }

        // Check unique code
        if ($this->asetModel->where('kode_aset', $kodeAset)->first()) {
            return redirect()->back()->withInput()->with('error', "Kode aset {$kodeAset} sudah terdaftar.");
        }

        // Generate QR code
        $qrPath = $this->qrService->generateForAsset($kodeAset);

        $insertedId = $this->asetModel->insert([
            'kode_aset'       => $kodeAset,
            'nama_aset'       => $namaAset,
            'id_departemen'   => $idDept,
            'lokasi_spesifik' => $lokasi,
            'qr_code_path'    => $qrPath,
            'kondisi'         => $kondisi,
            'created_at'      => date('Y-m-d H:i:s'),
        ]);

        $this->audit->log("Menambahkan aset hotel baru: {$namaAset} ({$kodeAset})", 'Manajemen Aset');
        return redirect()->to(base_url('aset/detail/' . $insertedId))->with('success', "Aset {$namaAset} berhasil ditambahkan dan QR Code telah digenerate.");
    }

    public function detail($id)
    {
        $asset = $this->asetModel->getAssetsWithDept($id);
        if (!$asset) {
            return redirect()->to(base_url('aset'))->with('error', 'Aset tidak ditemukan.');
        }

        $history = $this->asetModel->getRepairHistory($id);

        return view('aset/detail', [
            'title'   => 'Rincian Aset: ' . $asset['nama_aset'],
            'asset'   => $asset,
            'history' => $history,
        ]);
    }

    public function edit($id)
    {
        $asset = $this->asetModel->find($id);
        if (!$asset) return redirect()->to(base_url('aset'))->with('error', 'Aset tidak ditemukan.');

        return view('aset/edit', [
            'title'      => 'Edit Aset: ' . $asset['nama_aset'],
            'asset'      => $asset,
            'departemen' => $this->deptModel->getAllCached(),
        ]);
    }

    public function update($id)
    {
        $asset = $this->asetModel->find($id);
        if (!$asset) return redirect()->to(base_url('aset'))->with('error', 'Aset tidak ditemukan.');

        $namaAset = trim($this->request->getPost('nama_aset') ?? '');
        $idDept   = (int) $this->request->getPost('id_departemen');
        $lokasi   = trim($this->request->getPost('lokasi_spesifik') ?? '');
        $kondisi  = $this->request->getPost('kondisi') ?? 'Baik';

        $this->asetModel->update($id, [
            'nama_aset'       => $namaAset,
            'id_departemen'   => $idDept,
            'lokasi_spesifik' => $lokasi,
            'kondisi'         => $kondisi,
        ]);

        $this->audit->log("Memperbarui data aset {$asset['kode_aset']} - {$namaAset}", 'Manajemen Aset');
        return redirect()->to(base_url('aset/detail/' . $id))->with('success', 'Data aset berhasil diperbarui.');
    }

    public function delete($id)
    {
        $asset = $this->asetModel->find($id);
        if (!$asset) return redirect()->to(base_url('aset'))->with('error', 'Aset tidak ditemukan.');

        // Delete QR code file if exists
        if (!empty($asset['qr_code_path']) && file_exists(FCPATH . $asset['qr_code_path'])) {
            @unlink(FCPATH . $asset['qr_code_path']);
        }

        $this->asetModel->delete($id);
        $this->audit->log("Menghapus aset hotel {$asset['kode_aset']} - {$asset['nama_aset']}", 'Manajemen Aset');

        return redirect()->to(base_url('aset'))->with('success', "Aset {$asset['nama_aset']} berhasil dihapus.");
    }

    public function printQr($id = null)
    {
        if ($id !== null) {
            $assets = [$this->asetModel->getAssetsWithDept($id)];
        } else {
            $assets = $this->asetModel->getAssetsWithDept();
        }

        return view('aset/print_qr', [
            'title'  => 'Cetak Label QR Code Aset',
            'assets' => $assets,
        ]);
    }
}
