<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Form\UserPasswordType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[Route('/profile')]
class ProfileController extends AbstractController
{
    #[Route('/', name: 'app_profile', methods: ['GET'])]
    public function profile(UserRepository $userRepo, Security $security): Response
    {
        $user = $security->getUser();
        return $this->render('profile/profile.html.twig', [
            'favorite' => $userRepo->countFavorites($user),
            'game' => $userRepo->countOrderedGames($user)
        ]); 
    }

    #[Route('/setting', name: 'app_setting', methods: ['GET', 'POST'])]
    public function edit(UserRepository $userRepo, Request $request, Security $security ,UserPasswordHasherInterface $userPasswordHasher,  EntityManagerInterface $entityManager): Response
    {
        $user = $security->getUser();

        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        $passwordForm = $this->createForm(UserPasswordType::class);
        $passwordForm->handleRequest($request);

        if ($passwordForm->isSubmitted() && $passwordForm->isValid()) {
            //$newPassword = $passwordForm->get('plainPassword')->getData();
            $hashedPassword = $userPasswordHasher->hashPassword($user, $newPassword);

            try {
                $userRepo->upgradePassword($user, $hashedPassword);
                $this->addFlash('success', 'Password changed successfully.');
            } catch (UnsupportedUserException $e) {
                $this->addFlash('error', 'An error occurred while updating the password.');
            }

            return $this->redirectToRoute('app_');
        }

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_profile', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('profile/setting.html.twig', [
            'user' => $user,
            'form' => $form,
            'passwordForm' => $passwordForm->createView(),
            'favorite' => $userRepo->countFavorites($user),
            'game' => $userRepo->countOrderedGames($user)
        ]);
    }
}
