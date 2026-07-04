<?php

namespace App;

use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\Writer\PngWriter;

class QrCode
{
    public static function treasureUrl(string $treasureId): string
    {
        return Env::get('CLIENT_BASE_URL', '') . '?treasure=' . $treasureId;
    }

    public static function treasurePng(string $treasureId): string
    {
        return Builder::create()
            ->writer(new PngWriter())
            ->data(self::treasureUrl($treasureId))
            ->errorCorrectionLevel(ErrorCorrectionLevel::Medium)
            ->size(600)
            ->margin(24)
            ->build()
            ->getString();
    }
}
