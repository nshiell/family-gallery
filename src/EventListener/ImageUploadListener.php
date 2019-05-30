<?php
namespace App\EventListener;

use App\Entity\Image;
use App\Service\Uploader;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Security\Core\Security;
//use App\Collections\ImageVariantCollection;
use App\Service\ImageVariantCollectionFactory;

class ImageUploadListener
{
    private $uploader;

    private $processOwnerUserFinder;

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

        // upload only works for Image entities
        if (!$entity instanceof Image) {
            return;
        }
    
        $file = $entity->getOriginalFilename();
        if ($file instanceof UploadedFile && !$entity->getWidth()) {
            $this->uploadFile($entity);
        }
    }

    public function postPersist(LifecycleEventArgs $args)
    {
        $entity = $args->getObject();

        if ($entity instanceof Image) {
            $this->uploader->moveImageToIdAndMakeSizes($entity);
        }
    }

    private function uploadFile($entity)
    {
        // upload only works for Image entities
        if (!$entity instanceof Image) {
            return;
        }

        $file = $entity->getOriginalFilename();
        $entity->setDescription('');
        
        if (!$entity->getCreatedAt()) {
            $entity->setCreatedAt(new \DateTime);
        }

        if (!$entity->getUserId()) {
            $entity->setUserId($this->processOwnerUserFinder->getUser());
        }

        if ($file instanceof UploadedFile) {
            $this->uploader->moveToImagesDirWithTempFileName($file, $entity);
        }
    }
}
