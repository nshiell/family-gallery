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

    public function relationName(User $relation,
                                 string $meString = 'you',
                                 string $nameFormat = '%s (%s)')
    {
        $user = $this->processOwnerUserFinder->getUser();
        if (!$user) {
            return '';
        }

        $relationUsername = $relation->getUsername();
        if ($relation === $user) {
            return sprintf($nameFormat, $meString, $relationUsername);
        }

        $alias = $this->relativeAliasRepository->findOneBy([
            'user'         => $user,
            'relativeUser' => $relation
        ]);

        return ($alias)
            ? sprintf($nameFormat, $alias, $relationUsername)
            : $relation->getUsername();
    }
}
