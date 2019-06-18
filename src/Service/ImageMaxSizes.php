<?php
namespace App\Service;

class ImageMaxSizes implements \IteratorAggregate
{
    /** @var array */
    private $imageMaxSizes;

    public function __construct(array $imageMaxSizes)
    {
        if (!$this->validateImageMaxSizes($imageMaxSizes)) {
            throw new \InvalidArgumentException('Invalid imageMaxSizes');
        }

        $this->imageMaxSizes = $imageMaxSizes;
    }

    public function getIterator()
    {
        return new \ArrayIterator($this->imageMaxSizes);
    }

    public function getVariantNames(): array
    {
        return array_keys($this->imageMaxSizes);
    }

    private function validateImageMaxSizes(array $imageMaxSizes)
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
}
