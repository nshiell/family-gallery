<?php

namespace App\ParamConverter;

use Sensio\Bundle\FrameworkExtraBundle\Request\ParamConverter\ParamConverterInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;

use App\Repository\UserRepository;
use App\Repository\RelativeAliasRepository;

use App\Entity\RelativeAlias;

class RelativeAliasConverter implements ParamConverterInterface
{
    /** @var Security */
    private $security;

    /** @var RelativeAliasRepo */
    private $relativeAliasRepo;

    /** @var UserRepository */
    private $userRepository;

    public function __construct(Security $security,
                                RelativeAliasRepository $relativeAliasRepo,
                                UserRepository $userRepository)
    {
        $this->security = $security;
        $this->relativeAliasRepo = $relativeAliasRepo;
        $this->userRepository = $userRepository;
    }

    /**
     * {@inheritdoc}
     *
     * Check, if object supported by our converter
     */
    public function supports(ParamConverter $configuration)
    {
        return $configuration->getName() == 'relativeAlias';
    }

    /**
     * {@inheritdoc}
     *
     * Applies converting
     *
     * @throws \InvalidArgumentException When route attributes are missing
     * @throws NotFoundHttpException     When object not found
     */
    public function apply(Request $request, ParamConverter $configuration)
    {
        $relativeUserId = $request->attributes->get('relativeUser');
        $user = $this->security->getUser();
        if ($relativeUserId === null) {
            throw new \InvalidArgumentException(
                'relativeUser attribute is missing'
            );
        }

        if (!$user) {
            throw new \RuntimeException('user not Login');
        }

        $relativeAlias = $this->relativeAliasRepo
            ->findOneByIdForCurrentUser($relativeUserId, $user);

        if (!$relativeAlias) {
            $relativeAlias = new RelativeAlias(
                $user,
                $this->userRepository->find($relativeUserId)
            );
        }

        $request->attributes->set($configuration->getName(), $relativeAlias);
        return true;
    }
}
