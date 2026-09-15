<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;

/**
 * Every product/category image goes through here rather than a raw
 * Storage::put() — without it, a customer uploading a 6000px phone
 * photo would ship that full file to every visitor's browser. Capping
 * width and re-encoding at a sane quality is the single biggest,
 * cheapest page-weight win available for an image-heavy storefront.
 */
class ImageOptimizer
{
    private const MAX_WIDTH = 1600;

    private const QUALITY = 78;

    public static function store(UploadedFile $file, string $folder): string
    {
        $manager = new ImageManager(new Driver);
        $image = $manager->read($file->getRealPath());

        if ($image->width() > self::MAX_WIDTH) {
            $image->scaleDown(width: self::MAX_WIDTH);
        }

        $filename = $folder.'/'.Str::random(20).'.jpg';
        $encoded = $image->toJpeg(self::QUALITY);

        Storage::disk('public')->put($filename, (string) $encoded);

        return $filename;
    }
}
