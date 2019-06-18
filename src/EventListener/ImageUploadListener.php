<?php
namespace App\EventListener;

use App\Entity\Image;
use App\Service\Uploader;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Security\Core\Security;
use App\Service\ImageVariantCollectionFactory;

class ImageUploadListener
{
    /** @var Uploader */
    private $uploader;

    /** @todo remove tight coupling with Symfony here */
    private $processOwnerUserFinder;

    /** @var ImageVariantCollectionFactory */
    private $imageVariantCollectionFactory;

    public function __construct(Uploader $uploader,
                                Security $security,
                                ImageVariantCollectionFactory $imageVariantCollectionFactory)
    {
        $this->uploader = $uploader;
        $this->processOwnerUserFinder = $security;
        $this->imageVariantCollectionFactory = $imageVariantCollectionFactory;
    }

    public function postLoad(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();
        // Give an Image an imageVariantCollection instance
        if ($entity instanceof Image) {
            try {
                $entity->setVariantCollection(
                    $this->imageVariantCollectionFactory->create($entity)
                );
            } catch (\InvalidArgumentException $e) {}
        }
    }

    public function prePersist(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();

        if (!$entity instanceof Image) {
            return;
        }

        // Fish the image out of the OS temp dir
        if ($entity->getFile() instanceof UploadedFile && !$entity->getId()) {
            $this->uploadFile($entity);
        }
    }

    public function postPersist(LifecycleEventArgs $args)
    {
        $entity = $args->getObject();

        // Finalize Image upload not we have a database ID
        if ($entity instanceof Image) {
            $this->uploader->moveImageToIdAndMakeSizes($entity);
        };
    }

    private function uploadFile($entity)
    {
        if (!$entity instanceof Image) {
            return;
        }

        $entity->setDescription('');

        if (!$entity->getCreatedAt()) {
            $entity->setCreatedAt(new \DateTime);
        }

        if (!$entity->getUserId()) {
            $entity->setUserId($this->processOwnerUserFinder->getUser());
        }

        $this->uploader->moveToImagesDirWithTempFileName($entity);
    }
}
