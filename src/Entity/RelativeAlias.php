<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\RelativeAliasRepository")
 */
class RelativeAlias
{
    /**
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="relativeAliases")
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    /**
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="aliasesForMe")
     * @ORM\JoinColumn(nullable=false)
     */
    private $relativeUser;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private $alias;

    public function __construct(User $user = null, User $relativeUser = null)
    {
        if ($user) {
            $this->user = $user;
        }

        if ($relativeUser) {
            $this->relativeUser = $relativeUser;
        }
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getRelativeUser(): ?User
    {
        return $this->relativeUser;
    }

    public function setRelativeUser(?User $relativeUser): self
    {
        $this->relativeUser = $relativeUser;

        return $this;
    }

    public function getAlias(): ?string
    {
        return $this->alias;
    }

    public function setAlias(string $alias): self
    {
        $this->alias = $alias;

        return $this;
    }

    public function __toString()
    {
        return $this->getAlias();
    }
}
