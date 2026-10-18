<?php

namespace App\Controllers;

use App\Models\AsetHotelModel;
use App\Libraries\AuditService;

class Scan extends BaseController
{
    protected AsetHotelModel $asetModel;
    protected AuditService $audit;

    public function __construct()
    {
        $this->asetModel = new AsetHotelModel();
        $this->audit = new AuditService();
    }

    public function index()
    {
        return view('scan/index', [
            'title'  => 'Scan QR Code Aset Hotel',
            'assets' => $this->asetModel->getAssetsWithDept(),
        ]);
    }

    public function lookup($code = null)
    {
        if (empty($code)) {
            $code = $this->request->getGet('code');
        }

        $code = trim($code);
        $asset = $this->asetModel->getAssetByCode($code);

        if (!$asset) {
            return $this->response->setJSON([
                'success' => false,
                'message' => "Aset dengan kode '{$code}' tidak ditemukan dalam database.",
            ]);
        }

        $this->audit->log("Memindai QR Code untuk aset: {$asset['nama_aset']} ({$asset['kode_aset']})", 'QR Scanner');

        return $this->response->setJSON([
            'success'   => true,
            'asset'     => $asset,
            'detailUrl' => base_url('aset/detail/' . $asset['id_aset']),
            'reportUrl' => base_url('workorder/create?id_aset=' . $asset['id_aset']),
        ]);
    }
}
