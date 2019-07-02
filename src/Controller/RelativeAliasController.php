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
 * @Route("/person")
 * Uses App\ParamConverter\RelativeAliasConverter
 */
class RelativeAliasController extends AbstractController
{
    /**
     * @Route("/{relativeUser}/edit", name="relative_alias_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, RelativeAlias $relativeAlias): Response
    {

        $form = $this->createForm(RelativeAliasType::class, $relativeAlias);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            if ($relativeAlias->getAlias()) {
                $entityManager->persist($relativeAlias);
            } else {
                $entityManager->remove($relativeAlias);
            }

            $entityManager->flush();
        }

        return $this->render('relative_alias/edit.html.twig', [
            'relative_alias' => $relativeAlias,
            'form'           => $form->createView(),
        ]);
    }
}
