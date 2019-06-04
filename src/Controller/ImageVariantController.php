<?php

namespace App\Controller;

use App\Entity\Image;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

/**
 * @Route("/pics")
 */
class ImageVariantController extends AbstractController
{
    /**
     * @Route("/{id}/{variant}/{filename}", name="image_variant_show", methods={"GET"})
     */
    public function show(Image $image, string $variant): Response
    {
        try {
            $file = $image->getVariantCollection()->getFile($variant);
            if (!$file) {
                throw new \InvalidArgumentException;
            }
        } catch (\InvalidArgumentException $e) {
            throw $this->createNotFoundException('The photo doesn\'t exist');
        }

        // getFilename getPath
        $response = new Response();
        $disposition = $response->headers->makeDisposition(ResponseHeaderBag::DISPOSITION_INLINE, $file->getFilename());
        $response->headers->set('Content-Disposition', $disposition);
        $response->headers->set('Content-Type', 'image/jpeg');
        $response->setContent(file_get_contents($file->getRealPath()));

        return $response;
    }
}
