<?php
namespace App\Service;

use Symfony\Component\HttpFoundation\File\UploadedFile;
use Intervention\Image\ImageManagerStatic;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Finder\Finder;

use App\Entity\Image;

class Uploader
{
    private $targetDirectory;

    private $imageMaxSizes;

    private $filesystem;

    public function __construct(Filesystem $filesystem, $targetDirectory, array $imageMaxSizes)
    {
        if (!$this->validateImageMaxSizes($imageMaxSizes)) {
            throw new \InvalidArgumentException('Invalid imageMaxSizes');
        }

        $this->filesystem = $filesystem;
        $this->imageMaxSizes = $imageMaxSizes;
        $this->targetDirectory = $targetDirectory;
    }

    /**
     * @throws \Symfony\Component\HttpFoundation\File\Exception\FileException
     */
    public function moveToImagesDirWithTempFileName(UploadedFile $file, Image $entity)
    {
        $originalNewName = sprintf('original.%s', $file->guessExtension());
        $originalNewPathPrefix = sprintf('temp_%s_%s',
            md5(uniqid()),
            $entity->getOriginalFilename()->getClientOriginalName()
        );

        $file->move(
            $this->targetDirectory . '/' . $originalNewPathPrefix,
            $originalNewName
        );

        list($width, $height) = getimagesize(
            $this->targetDirectory
            . '/' . $originalNewPathPrefix . '/'
            . $originalNewName
        );

        $entity->setWidth($width);
        $entity->setHeight($height);

        $entity->setOriginalFilename($originalNewPathPrefix);

        return $originalNewPathPrefix;
    }

    public function moveImageToIdAndMakeSizes(Image $entity)
    {
        $id = $entity->getId();
        if (!$id) {
            return false;
        }

        $originalFilename = $entity->getOriginalFilename();
        $imagesForEntityTempDir = $this->targetDirectory .
            '/' . $originalFilename;

        if (!$originalFilename || !$imagesForEntityTempDir) {
            return false;
        }

        if (!$this->filesystem->exists($imagesForEntityTempDir)) {
            return false;
        }

        $imagesForEntityDir = $this->targetDirectory . '/' . $id;
        
        $this->filesystem->rename($imagesForEntityTempDir, $imagesForEntityDir);

        $finder = new Finder();
        $finder->files()->in($imagesForEntityDir);

        if ($finder->hasResults()) {
            $originalPath = null;
            $i = 0;
            foreach ($finder as $file) {
                if (substr($file->getFilename(), 0, 8) == 'original') {
                    $originalPath = $file->getRealPath();
                }
                $i++;
            }

            if ($i == 1 && $originalPath) {
                foreach ($this->imageMaxSizes as $type => $imageMaxSize) {
                    $this->resize($originalPath, $type, $imageMaxSize, $id);
                }
            }
        }
        
        return true;
    }

    private function validateImageMaxSizes($imageMaxSizes)
    {
        foreach ($imageMaxSizes as $type => $imageMaxSize) {
            if (!isset ($imageMaxSize['width'])) {
                return false;
            }

            if (!is_int($imageMaxSize['width'])) {
                return false;
            }

            if (!$imageMaxSize['width']) {
                return false;
            }

            if (!isset ($imageMaxSize['height'])) {
                return false;
            }

            if (!is_int($imageMaxSize['height'])) {
                return false;
            }

            if (!$imageMaxSize['height']) {
                return false;
            }
        }

        return true;
    }
    
    private function resize(string $originalPath, string $type, array $imageMaxSize, int $id)
    {
        $originalPathPartsDot = explode('.', $originalPath);
        $ext = array_pop($originalPathPartsDot) ?? 'jpeg';

        $img = ImageManagerStatic::make($originalPath);
        
        $img->resize($imageMaxSize['width'], null, function ($constraint) {
            $constraint->aspectRatio();
        });

        $img->resize(null, $imageMaxSize['height'], function ($constraint) {
            $constraint->aspectRatio();
        });

        $img->save($this->targetDirectory . '/' . $id . '/' . $type . '.' . $ext);

        return true;
    }
}
