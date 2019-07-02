<?php

namespace App\Twig;

use Symfony\Component\Security\Core\Security;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;
use App\Entity\User;
use App\Repository\RelativeAliasRepository;

class AppExtension extends AbstractExtension
{
    public function __construct(Security $security, RelativeAliasRepository $relativeAliasRepository)
    {
        $this->processOwnerUserFinder = $security;
        $this->relativeAliasRepository = $relativeAliasRepository;
    }

    public function getFunctions()
    {
        return [
            new TwigFunction('relationName', [$this, 'relationName']),
        ];
    }

    public function relationName(User $relatedUser, string $meAlias = 'you')
    {
        $currentUser = $this->processOwnerUserFinder->getUser();
        if (!$currentUser) {
            return '';
        }

        $relationUsername = $relatedUser->getUsername();
        if ($relatedUser === $currentUser) {
            return $meAlias;
        }

        $alias = $this->relativeAliasRepository->findOneBy([
            'user'         => $currentUser,
            'relativeUser' => $relatedUser
        ]);

        return ($alias)
            ? $alias->getAlias()
            : $relatedUser->getUsername();
    }
}
