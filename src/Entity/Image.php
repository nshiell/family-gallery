<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

use App\Collections\ImageVariantCollection;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ImageRepository")
 */
class Image
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $original_filename;

    /**
     * @ORM\Column(type="integer")
     */
    private $width;

    /**
     * @ORM\Column(type="integer")
     */
    private $height;

    /**
     * @ORM\Column(type="text")
     */
    private $description;

    /**
     * @ORM\Column(type="datetime")
     */
    private $created_at;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="images")
     * @ORM\JoinColumn(nullable=false)
     */
    private $user_id;

    private $variantCollection;

    /**
     * @ORM\Column(type="integer")
     */
    private $rotation;

    /**
     * @ORM\Column(type="string", length=20)
     */
    private $calculated_extension;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $temp_filename;

    /**
     * @ORM\Column(type="boolean")
     */
    private $rotation_forced;

    /**
     * @var UploadedFile
     * @Assert\NotBlank(message="Upload a photo")
     * @Assert\File(
        maxSize = "3M",
        mimeTypes={ "image/jpeg" })
     */
    private $file;

    public function setFile(UploadedFile $file)
    {
        if ($this->getId()) {
            throw new \LogicException('Cannot upload a file to an entity that is already persistent');
        }

        $this->file = $file;
        return $this;
    }

    public function getFile(): ?UploadedFile
    {
        if ($this->getId()) {
            throw new \LogicException('Cannot upload a file to an entity that is already persistent');
        }

        return $this->file;
    }

    public function getId(): ?int
    {
        return $this->id;
    }
/*
    public function getOriginalFilename(): ?string
    {
        return $this->original_filename;
    }

    public function setOriginalFilename(string $original_filename): self
    {
        $this->original_filename = $original_filename;

        return $this;
    }
*/

    public function getOriginalFilename()
    {
        return $this->original_filename;
    }

    public function setOriginalFilename($original_filename): self
    {
        $this->original_filename = $original_filename;

        return $this;
    }

    public function getWidth(): ?int
    {
        return $this->width;
    }

    public function setWidth(int $width): self
    {
        $this->width = $width;

        return $this;
    }

    public function getHeight(): ?int
    {
        return $this->height;
    }

    public function setHeight(int $height): self
    {
        $this->height = $height;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->created_at;
    }

    public function setCreatedAt(\DateTimeInterface $created_at): self
    {
        $this->created_at = $created_at;

        return $this;
    }

    public function getUserId(): ?User
    {
        return $this->user_id;
    }

    public function setUserId(?User $user_id): self
    {
        $this->user_id = $user_id;

        return $this;
    }
    
    public function setVariantCollection(ImageVariantCollection $variantCollection): self
    {
        if (!$this->getId()) {
            throw new \LogicException('Cannot access ImageVariantCollection from an entity that isn\'t persistent');
        }
        $this->variantCollection = $variantCollection;

        return $this;
    }

    public function getVariantCollection(): ?ImageVariantCollection
    {
        if (!$this->getId()) {
            throw new \LogicException('Cannot access ImageVariantCollection from an entity that isn\'t persistent');
        }
        return $this->variantCollection;
    }

    public function getRotation(): ?int
    {
        return $this->rotation;
    }

    public function setRotation(int $rotation): self
    {
        $this->rotation = $rotation;

        return $this;
    }

    public function getCalculatedExtension(): ?string
    {
        return $this->calculated_extension;
    }

    public function setCalculatedExtension(string $calculated_extension): self
    {
        $this->calculated_extension = $calculated_extension;

        return $this;
    }

    public function getTempFilename(): ?string
    {
        return $this->temp_filename;
    }

    public function setTempFilename(?string $temp_filename): self
    {
        $this->temp_filename = $temp_filename;

        return $this;
    }

    public function getRotationForced(): ?bool
    {
        return $this->rotation_forced;
    }

    public function setRotationForced(bool $rotation_forced): self
    {
        $this->rotation_forced = $rotation_forced;

        return $this;
    }
}
