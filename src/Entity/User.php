<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 */
class User implements UserInterface
{
    const ROLE_USER  = 'ROLE_USER';
    const ROLE_ADMIN = 'ROLE_ADMIN';

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=180, unique=true)
     */
    private $username;

    /**
     * @ORM\Column(type="json")
     */
    private $roles = [];

    /**
     * @var string The hashed password
     * @ORM\Column(type="string")
     */
    private $password;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Image", mappedBy="user_id")
     */
    private $images;

    /**
     * The collection of aliases that I use to address each family member
     * i.e. Mum, Dad etc, the relationship here will also link back to the actual user
     * basically a mamy-to-many with metadata
     * This allows each family member to have a personal view of people using
     * more personal names specific to each person
     *
     * @var Collection|RelativeAlias[]
     * @ORM\OneToMany(targetEntity="App\Entity\RelativeAlias", mappedBy="userId", orphanRemoval=true)
     */
    private $relativeAliases;

    /**
     * The collection of all the names that people call me by
     * with a link to who calls me by that name
     *
     * This collection will not be de-dupped
     * as you might heve sevral people calling you Mum etc
     *
     * i.e. Dad is how I'm addressed by Joe Bloggs
     *      Bro is how I'm addressed by Fred Bloggs
     *      Sonny is how I'm addressed by Old Man Bloggs
     *
     * @var Collection|RelativeAlias[]
     * @ORM\OneToMany(targetEntity="App\Entity\RelativeAlias", mappedBy="relativeUserId", orphanRemoval=true)
     */
    private $aliasesForMe;

    public function __construct()
    {
        $this->images = new ArrayCollection();
        $this->user = new ArrayCollection();
        $this->subjectUserId = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUsername(): string
    {
        return (string) $this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getPassword(): string
    {
        return (string) $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getSalt()
    {
        // not needed when using the "bcrypt" algorithm in security.yaml
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    /**
     * @return Collection|Image[]
     */
    public function getImages(): Collection
    {
        return $this->images;
    }

    public function addImage(Image $image): self
    {
        if (!$this->images->contains($image)) {
            $this->images[] = $image;
            $image->setUserId($this);
        }

        return $this;
    }

    public function removeImage(Image $image): self
    {
        if ($this->images->contains($image)) {
            $this->images->removeElement($image);
            // set the owning side to null (unless already changed)
            if ($image->getUserId() === $this) {
                $image->setUserId(null);
            }
        }

        return $this;
    }

    public function __toString()
    {
        return $this->getUsername();
    }

    /**
     * @return Collection|RelativeAlias[]
     */
    public function getRelativeAliases(): Collection
    {
        return $this->relativeAliases;
    }

    public function addRelativeAlias(RelativeAlias $relativeAlias): self
    {
        if (!$this->relativeAliases->contains($relativeAlias)) {
            $this->relativeAliases[] = $relativeAlias;
            $relativeAlias->setUser($this);
        }

        return $this;
    }

    public function removeRelativeAlias(RelativeAlias $relativeAlias): self
    {
        if ($this->relativeAliases->contains($relativeAlias)) {
            $this->relativeAliases->removeElement($relativeAlias);
            // set the owning side to null (unless already changed)
            if ($relativeAlias->getUser() === $this) {
                $user->setUser(null);
            }
        }

        return $this;
    }
}
