<?php
namespace App\Service;

use Intervention\Image\ImageManagerStatic;
use App\Entity\Image;

/**
 * @todo check for mbstring and exif and error if not presant
 * @todo refactor out ImageManagerStatic
 *
 * Please make sure your PHP installation has memory_limit
 * set appropriately high for high resolution files!
 */
class ImageProcessor
{
    /** @var string */
    private $targetDirectory;

    /** @var ImageMaxSizes */
    private $imageMaxSizes;

    public function __construct(string $targetDirectory, ImageMaxSizes $imageMaxSizes)
    {
        $this->targetDirectory = $targetDirectory;
        $this->imageMaxSizes = $imageMaxSizes;
    }

    /**
     * Will only rotate the image if the user has elected to force a rotation
     * i.e. $image->getRotationForced() is true
     * Will store smaller versions of the image leaving the original file untouched
     */
    public function resizeAndRotate(Image $image)
    {
        $id = $image->getId();
        if (!$id) {
            return false;
        }

        $rotation = ($image->getRotationForced()) ? $image->getRotation() : 0;
        $ext = $image->getCalculatedExtension();
        if (!$ext) {
            throw new \InvalidArgumentException('No extension');
        }

        $originalPath = implode(DIRECTORY_SEPARATOR, [
            $this->targetDirectory,
            $image->getId(),
            'original.' . $ext
        ]);

        foreach ($this->imageMaxSizes as $type => $imageMaxSize) {
            $this->rotateAndSizeVariant(
                $originalPath,
                $type,
                $imageMaxSize,
                $id,
                $rotation,
                $ext);
        }
    }

    /**
     * Given a specific image varient binary to create
     * will do the rotation and sizing
     */
    private function rotateAndSizeVariant(string $originalPath,
                                          string $type,
                                          array  $imageMaxSize,
                                          int    $id,
                                          int    $rotation,
                                          string $ext)
    {
        $img = ImageManagerStatic::make($originalPath);
        if ($rotation) {
            // rotates 0 - $rotation thus correcting the rotation
            $img->rotate($rotation);

        }

        $img->resize($imageMaxSize['width'], null, function ($constraint) {
            $constraint->aspectRatio();
        });

        $img->resize(null, $imageMaxSize['height'], function ($constraint) {
            $constraint->aspectRatio();
        });

        $img->save($this->targetDirectory . '/' . $id . '/' . $type . '.' . $ext);
    }
}
