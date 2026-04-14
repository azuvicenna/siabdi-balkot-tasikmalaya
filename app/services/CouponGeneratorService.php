<?php

namespace App\services;

use Exception;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use Illuminate\Support\Facades\File;

class CouponGeneratorService
{
    private const TEMPLATE_PATH = 'static/images/Template-coupon.png';
    private const FONT_BOLD     = 'fonts/Inter_28pt-SemiBold.ttf';

    public function generate(string $name, string $unit_kerja, string $couponNumber)
    {
        $this->validateAssets();

        $manager = new ImageManager(new Driver());
        $image = $manager->read(public_path(self::TEMPLATE_PATH));

        $this->applyTextToImage($image, $name, $unit_kerja, $couponNumber);

        return $this->saveImage($image);
    }

    private function validateAssets(): void
    {
        if (!file_exists(public_path(self::TEMPLATE_PATH))) {
            throw new Exception('Template gambar tidak ditemukan.');
        }

        if (!file_exists(public_path(self::FONT_BOLD))) {
            throw new Exception('Font tidak ditemukan.');
        }
    }

    private function applyTextToImage($image, string $name, string $unit_kerja, string $couponNumber)
    {
        $fontBold = public_path(self::FONT_BOLD);
        $width = $image->width();
        $height = $image->height();

        // Kode Kupon
        $image->text($couponNumber, (int)($width * 0.50), (int)($height * 0.29), function($font) use ($fontBold) {
            $font->file($fontBold);
            $font->size(130);
            $font->color('#2f7fb5');
            $font->align('left');
            $font->valign('top');
        });

        $leftMarginX = (int)($width * 0.43);

        // Nama Peserta 
        $image->text(strtoupper($name), $leftMarginX, (int)($height * 0.47), function($font) use ($fontBold) {
            $font->file($fontBold);
            $font->size(48);
            $font->color('#272e38');
            $font->align('left');
            $font->valign('top');
        });

        // UnitKerja 
        $image->text($unit_kerja, $leftMarginX, (int)($height * 0.61), function($font) use ($fontBold) {
            $font->file($fontBold);
            $font->size(48);
            $font->color('#272e38');
            $font->align('left');
            $font->valign('top');
        });
    }

    private function saveImage($image): string
    {
        $tempDir = storage_path('app/public/generated-coupons');

        if (!File::isDirectory($tempDir)) {
            File::makeDirectory($tempDir, 0755, true);
        }

        $filename = 'kupon-' . uniqid() . '.jpg';
        $path = $tempDir . '/' . $filename;

        $image->save($path, 100, 'jpg');

        return $path;
    }
}