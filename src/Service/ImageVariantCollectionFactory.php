<?php
namespace App\Service;

use App\Collections\ImageVariantCollection;
use App\Entity\Image;

/**
 * Maybe use this in a Doctrine onload event images an ImageVariantCollection?
 */
class ImageVariantCollectionFactory
{
    private $targetDirectory;
    private $imageMaxSizes;

    public function __construct(string $targetDirectory, ImageMaxSizes $imageMaxSizes)
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

        return new ImageVariantCollection(
            $id,
            $image->getCalculatedExtension(),
            $this->imageMaxSizes,
            $this->targetDirectory
        );
    }
}
