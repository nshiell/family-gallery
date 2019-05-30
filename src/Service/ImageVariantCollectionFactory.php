<?php
namespace App\Service;

use App\Collections\ImageVariantCollection;
use App\Entity\Image;

class ImageVariantCollectionFactory
{
    private $targetDirectory;
    private $imageMaxSizes;

    public function __construct(string $targetDirectory, array $imageMaxSizes)
    {
        $this->targetDirectory = $targetDirectory;
        $this->imageMaxSizes = $imageMaxSizes;
    }

    public function create(Image $image): ImageVariantCollection
    {
        $id = $image->getId();
        if (!$id) {
            throw new \InvalidArgumentException;
        }

        $originalFilename = $image->getOriginalFilename();
        $realOriginalFilename = substr(
            $originalFilename,
            strpos(substr($originalFilename, 10), '_') + 11
        );

        if (strpos($realOriginalFilename, '.') !== false) {
            $partsOnDot = explode('.', $realOriginalFilename);
            $extension = array_pop($partsOnDot);
        } else {
            $extension = 'jpeg';
        }

        return new ImageVariantCollection(
            $id,
            $extension,
            $this->imageMaxSizes,
            $this->targetDirectory
        );
    }
}