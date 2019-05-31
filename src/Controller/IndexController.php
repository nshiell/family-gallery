<?php

namespace App\Controller;

use App\Entity\Image;
use App\Repository\ImageRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use Symfony\Component\HttpFoundation\Session\Session;

/**
 * @Route("/")
 */
class IndexController extends AbstractController
{
    /**
     * @Route("/", name="index", methods={"GET","POST"})
     */
    public function new(Request $request, ImageRepository $imageRepository): Response
    {
        $session = new Session();
        $error = null;

        if ($session->get('captcha')) {
            $numbers = $session->get('captcha');

            if ($request->isMethod('post')) {
                $postData = $request->request->get('sum');
                if ($numbers[0] + $numbers[1] == (int) $postData) {
                    $session->set('is-human', true);
                } else {
                    $error = 'Ummm yea That\'s not right, try again please';
                }
            }
        } else {
            $numbers = [
                rand(1, 9),
                rand(1, 9)
            ];

            $session->set('captcha', $numbers);
        }

        if ($session->get('is-human') || $this->getUser()) {
            return $this->render('index/list.html.twig', [
                'images' => $imageRepository->findAll(),
            ]);
        }

        return $this->render('index/captcha.html.twig', [
            'numbers' => $numbers,
            'error'   => $error
        ]);
    }
}
