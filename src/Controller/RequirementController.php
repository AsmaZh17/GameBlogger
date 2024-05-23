<?php

namespace App\Controller;

use App\Entity\Requirement;
use App\Form\RequirementType;
use App\Repository\RequirementRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin/requirement')]
class RequirementController extends AbstractController
{
    #[Route('/', name: 'app_requirement_index', methods: ['GET'])]
    public function index(RequirementRepository $requirementRepository): Response
    {
        return $this->render('admin/requirement/index.html.twig', [
            'requirements' => $requirementRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_requirement_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $requirement = new Requirement();
        $form = $this->createForm(RequirementType::class, $requirement);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($requirement);
            $entityManager->flush();

            return $this->redirectToRoute('app_requirement_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/requirement/new.html.twig', [
            'requirement' => $requirement,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_requirement_show', methods: ['GET'])]
    public function show(Requirement $requirement): Response
    {
        return $this->render('admin/requirement/show.html.twig', [
            'requirement' => $requirement,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_requirement_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Requirement $requirement, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(RequirementType::class, $requirement);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_requirement_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/requirement/edit.html.twig', [
            'requirement' => $requirement,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_requirement_delete', methods: ['POST'])]
    public function delete(Request $request, Requirement $requirement, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$requirement->getId(), $request->getPayload()->get('_token'))) {
            $entityManager->remove($requirement);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_requirement_index', [], Response::HTTP_SEE_OTHER);
    }
}
