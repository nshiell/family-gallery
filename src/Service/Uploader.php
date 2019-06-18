<?php
namespace App\Service;

use Symfony\Component\Filesystem\Filesystem;
use App\Service\ImageOrientationResolver;
use App\Service\ImageProcessor;
use App\Entity\Image;

/**
 * Call self->moveToImagesDirWithTempFileName on upload
 * to move the image out of the OS temp dir and associate with the $image entity
 * (maybe on a Doctrine postPersist)?
 *
 * Call self->moveImageToIdAndMakeSizes to associate created image variants binaries
 */
class Uploader
{
    const TEMP_FILENAME     = 'temp';
    const ORIGINAL_FILENAME = 'original';

    /** @var string */
    private $targetDirectory;

    /** @var Filesystem */
    private $filesystem;

    /** @var ImageOrientationResolver */
    private $imageOrientationResolver;

    /** @var ImageProcessor */
    private $imageProcessor;

    public function __construct(ImageOrientationResolver $imageOrientationResolver,
                                Filesystem               $filesystem,
                                ImageProcessor           $imageProcessor,
                                string                   $targetDirectory)
    {
        $this->imageOrientationResolver = $imageOrientationResolver;
        $this->filesystem = $filesystem;
        $this->targetDirectory = $targetDirectory;
        $this->imageProcessor = $imageProcessor;
    }

    /**
     * Call this before this entity has a database ID
     * (i.e. before persist flush has occured)
     *
     * Moves the upload away from the OS temp dir
     * the image will be renamed original.$extension
     * and placed in a dir with a random name in our images dir
     * the random name will be stored in the entity
     * also gets dimetions and if the camera was at an angle when the photo was taken
     *
     * @throws \Symfony\Component\HttpFoundation\File\Exception\FileException
     */
    public function moveToImagesDirWithTempFileName(Image $entity)
    {
        $file = $entity->getFile();
        $rand = uniqid();

        $entity->setTempFilename($rand);
        $entity->setCalculatedExtension($file->guessExtension());
        $entity->setOriginalFilename($file->getClientOriginalName());

        $newTempDir = $this->targetDirectory
            . DIRECTORY_SEPARATOR
            . $rand;

        $newTempPath = $newTempDir
            . DIRECTORY_SEPARATOR
            . self::TEMP_FILENAME;

            $fileAfterMove = $file->move(
            $newTempDir,
            self::TEMP_FILENAME
        );

        list($width, $height) = getimagesize($newTempPath);

        $entity->setWidth($width);
        $entity->setHeight($height);
        $entity->setRotation(
            $this->imageOrientationResolver->getDegrees($fileAfterMove)
        );
    }

    /**
     * Call this immediately after the database persist has occured
     * so that the image entity now has a database
     * renames the temporary directory to the primary key of the image
     * executes all resizes and rotations on the image
     * leaving the original untouched - preserving exif metadata

     * @return bool true is all ok
     */
    public function moveImageToIdAndMakeSizes(Image $entity): bool
    {
        $id = $entity->getId();

        if (!$id) {
            return false;
        }

        $tempFilename = $entity->getTempFilename();
        $imagesForEntityTempDir = $this->targetDirectory .
            DIRECTORY_SEPARATOR . $tempFilename;

        if (!$tempFilename) {
            return false;
        }

        if (!$this->filesystem->exists($imagesForEntityTempDir)) {
            return false;
        }

        $imagesForEntityDir = $this->targetDirectory
            . DIRECTORY_SEPARATOR
            . $id;
        
        $this->filesystem->rename(
            $imagesForEntityTempDir,
            $imagesForEntityDir
        );

        $this->filesystem->rename(
            $imagesForEntityDir
                . DIRECTORY_SEPARATOR
                . self::TEMP_FILENAME,

            $imagesForEntityDir
                . DIRECTORY_SEPARATOR
                . self::ORIGINAL_FILENAME
                . '.' . $entity->getCalculatedExtension()
        );

        $this->imageProcessor->resizeAndRotate($entity);
        return true;
    }
}
