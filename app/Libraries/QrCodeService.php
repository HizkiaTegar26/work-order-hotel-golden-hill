<?php

namespace App\Libraries;

use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;

class QrCodeService
{
    protected string $uploadDir;

    public function __construct()
    {
        $this->uploadDir = FCPATH . 'uploads/qrcodes/';
        if (!is_dir($this->uploadDir)) {
            @mkdir($this->uploadDir, 0777, true);
        }
    }

    public function generateForAsset(string $kodeAset): string
    {
        $cleanCode = str_replace(['/', '\\', ' '], '_', strtolower($kodeAset));
        $filename = 'qr_' . $cleanCode . '.png';
        $fullPath = $this->uploadDir . $filename;

        $qr = new QrCode($kodeAset);
        $writer = new PngWriter();
        $result = $writer->write($qr);
        $result->saveToFile($fullPath);

        return 'uploads/qrcodes/' . $filename;
    }

    public function getBase64Qr(string $content): string
    {
        $qr = new QrCode($content);
        $writer = new PngWriter();
        $result = $writer->write($qr);
        return $result->getDataUri();
    }
}
