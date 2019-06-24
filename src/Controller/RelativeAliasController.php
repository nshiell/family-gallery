<?php

namespace App\Controller;

use App\Entity\RelativeAlias;
use App\Form\RelativeAliasType;
use App\Repository\RelativeAliasRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/alias")
 */
class RelativeAliasController extends AbstractController
{
    /**
     * @Route("/", name="relative_alias_index", methods={"GET"})
     */
    public function index(RelativeAliasRepository $relativeAliasRepository): Response
    {
        return $this->render('relative_alias/index.html.twig', [
            'relative_aliases' => $relativeAliasRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="relative_alias_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $relativeAlias = new RelativeAlias();
        $form = $this->createForm(RelativeAliasType::class, $relativeAlias);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($relativeAlias);
            $entityManager->flush();

            return $this->redirectToRoute('relative_alias_index');
        }

        return $this->render('relative_alias/new.html.twig', [
            'relative_alias' => $relativeAlias,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{user}", name="relative_alias_show", methods={"GET"})
     */
    public function show(RelativeAlias $relativeAlias): Response
    {
        return $this->render('relative_alias/show.html.twig', [
            'relative_alias' => $relativeAlias,
        ]);
    }

    /**
     * @Route("/{user}/edit", name="relative_alias_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, RelativeAlias $relativeAlias): Response
    {
        $form = $this->createForm(RelativeAliasType::class, $relativeAlias);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('relative_alias_index', [
                'user' => $relativeAlias->getUser(),
            ]);
        }

        return $this->render('relative_alias/edit.html.twig', [
            'relative_alias' => $relativeAlias,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{user}", name="relative_alias_delete", methods={"DELETE"})
     */
    public function delete(Request $request, RelativeAlias $relativeAlias): Response
    {
        if ($this->isCsrfTokenValid('delete'.$relativeAlias->getUser(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($relativeAlias);
            $entityManager->flush();
        }

        return $this->redirectToRoute('relative_alias_index');
    }
}
