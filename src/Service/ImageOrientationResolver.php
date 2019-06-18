<?php
namespace App\Service;
use Symfony\Component\HttpFoundation\File\File;

class ImageOrientationResolver
{
    private static $rotationDegreeMap = [
        1 => 0,
        8 => 90,
        3 => 180,
        6 => 270
    ];

    public function getDegrees(File $file): int
    {
        $exif = exif_read_data($file->getRealPath());

        return isset ($exif['Orientation'])
            ? self::$rotationDegreeMap[(int) $exif['Orientation']] ?? 0
            : 0;
    }
}
