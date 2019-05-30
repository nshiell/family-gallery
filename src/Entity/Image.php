<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

use App\Collections\ImageVariantCollection;

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
     * @Assert\NotBlank(message="Upload a photo")
     * @Assert\File(mimeTypes={ "image/jpeg" })
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
    
    public function getOriginalFilenameReal()
    {
        return substr(
            $this->original_filename,
            strpos(substr($this->original_filename, 10), '_') + 11
        );
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
        $this->variantCollection = $variantCollection;

        return $this;
    }

    public function getVariantCollection(): ?ImageVariantCollection
    {
        return $this->variantCollection;
    }
}
