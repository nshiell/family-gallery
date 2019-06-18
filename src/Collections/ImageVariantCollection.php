<?php
namespace App\Collections;

use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\Exception\FileNotFoundException;

Use App\Service\ImageMaxSizes;

class ImageVariantCollection implements \ArrayAccess
{
    private $id;
    private $variants;
    private $extension;
    private $targetDirectory;

    public function __construct(int           $id,
                                string        $extension,
                                ImageMaxSizes $imageMaxSizes,
                                string        $targetDirectory)
    {
        $this->id = $id;
        $variants = $imageMaxSizes->getVariantNames();
        if (!in_array('original', $variants)) {
            $variants[] = 'original';
        }

        $this->variants = array_fill_keys(array_keys($variants), null);

        if (strtolower($extension) == 'jpg') {
            $extension = 'jpeg';
        } elseif (!trim($extension)) {
            $extension = 'jpeg';
        }

        $this->extension = $extension;
        $this->targetDirectory = $targetDirectory;
    }

    public function getFile($variant): ?File
    {
        if (!in_array($variant, array_keys($this->variants))) {
            throw new \InvalidArgumentException;
        }

        if (!isset ($this->variants[$variant])) {
            try {
                $this->variants[$variant] = new File(
                    $this->targetDirectory
                    . $this->id
                    . DIRECTORY_SEPARATOR
                    . $variant . '.' . $this->extension
                );
            } catch (FileNotFoundException $e) {
                return null;
            }
        }

        return $this->variants[$variant];
    }
    
    public function offsetExists($offset): bool
    {
        return isset ($this->variants[$offset]);
    }

    public function offsetGet($offset)
    {
        return $this->getFile($offset);
    }

    public function offsetSet($offset, $value)
    {
        throw new \BadMethodCallException;
    }

    public function offsetUnset($offset)
    {
        throw new \BadMethodCallException;
    }
}
