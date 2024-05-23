<?php

namespace App\Controller;

use App\Entity\Game;
use App\Form\GameType;
use App\Repository\GameRepository;
use App\Repository\OrderItemRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Entity\Review;
use App\Form\ReviewType;
use App\Repository\BlogRepository;
use App\Repository\CategorieRepository;
use App\Repository\ReviewRepository;
use Doctrine\ORM\Mapping\Id;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\SecurityBundle\Security;

class GameController extends AbstractController
{
    #[Route('/game', name: 'app_game')]
    public function index(GameRepository $gameRepository,
    PaginatorInterface $paginator , 
    Request $request ,
    OrderItemRepository $orderItemRepository,
    Security $security
    ): Response
    {
        $user = $security->getUser();
        $orderItems = null;
        if ($user != null){
            $orderItems = $orderItemRepository->getUserItemNotOrdered($user);
        }
        $data = $gameRepository->findAll();
        
        $games = $paginator->paginate(
            $data,
            $request->query->getInt('page' , 1),
            3
        );
        return $this->render('game/game.html.twig', [
            'games' => $games ,
            'orderItems' => $orderItems
        ]);
    }
    #[Route('/game/popular', name: 'app_game_popular')]
    public function popular(GameRepository $gameRepository,
        OrderItemRepository $orderItemRepository,
        Security $security
    ): Response {
        $user = $security->getUser();
        $orderItems = null;
        if ($user != null){
            $orderItems = $orderItemRepository->getUserItemNotOrdered($user);
        }
        return $this->render('game/game.html.twig', [
            'games' => $gameRepository->lastTree() ,
            'orderItems' => $orderItems
        ]);
    }
    #[Route('/game/free', name: 'app_game_free')]
    public function free(GameRepository $gameRepository,
        PaginatorInterface $paginator , 
        Request $request ,
        OrderItemRepository $orderItemRepository,
        Security $security
    ): Response
    {
        $user = $security->getUser();
        $orderItems = null;
        if ($user != null){
            $orderItems = $orderItemRepository->getUserItemNotOrdered($user);
        }
        $data = $gameRepository->getFree();

        $games = $paginator->paginate(
            $data,
            $request->query->getInt('page' , 1),
            3
        );
        return $this->render('game/game.html.twig', [
            'games' => $games ,
            'orderItems' => $orderItems
        ]);
    }
    #[Route('/game/solde', name: 'app_game_solde')]
    public function solde(GameRepository $gameRepository,
        PaginatorInterface $paginator , 
        Request $request ,
        OrderItemRepository $orderItemRepository,
        Security $security
    ): Response {
        $user = $security->getUser();
        $orderItems = null;
        if ($user != null){
            $orderItems = $orderItemRepository->getUserItemNotOrdered($user);
        }
        $data = $gameRepository->getSolde();

        $games = $paginator->paginate(
            $data,
            $request->query->getInt('page' , 1),
            3
        );
        return $this->render('game/game.html.twig', [
            'games' => $games ,
            'orderItems' => $orderItems
        ]);
    }
    #[Route('/single_game/{id}', name: 'app_single_game')]
    public function single_blog($id , GameRepository $gameRepository ,
        ReviewRepository $reviewRepository ,
        Request $request ,
        EntityManagerInterface $entityManager,
        OrderItemRepository $orderItemRepository,
        Security $security
    ): Response {
        $user = $security->getUser();
        $orderItems = null;
        if ($user != null){
            $orderItems = $orderItemRepository->getUserItemNotOrdered($user);
        }
        $game = $gameRepository->find($id);
        $review = new Review();
        $form = $this->createForm(ReviewType::class, $review);
        $form->handleRequest($request);
        $reviewRate = $request->request->get('review-rate');
        $existingReview = $reviewRepository->getReview($id,$user);
        if ($form->isSubmitted() && $form->isValid()) {
            if ($user) {
                $review->setAuthor($user);
                $review->setGame($game);
                $review->setCreateAt(new \DateTime());
                $review->setWidthStar($reviewRate * 20);
                $game->setWidthStar(($game->getWidthStar() + ($reviewRate * 20)) / 2);
                $entityManager->persist($game);
                $entityManager->persist($review);
                $entityManager->flush();

                return $this->redirectToRoute('app_single_game', ['id' => $id], Response::HTTP_SEE_OTHER);
            }else{
                return $this->redirectToRoute('app_login', [], Response::HTTP_SEE_OTHER);
            }
        }

        return $this->render('game/single_game.html.twig' , [
            'game' => $game,
            'count' => $reviewRepository->countReviewsByGameId($game->getId()),
            'lastgame' => $gameRepository->lastTreeDiffId($id) ,
            'review' => $review,
            'form' => $form,
            'existingReview' => $existingReview ,
            'orderItems' => $orderItems
        ]);
        
    }
    #[Route('/single_game/{id}/edit', name: 'app_single_game_edit')]
    public function single_blog_edit($id , GameRepository $gameRepository ,
        ReviewRepository $reviewRepository ,
        Request $request ,
        EntityManagerInterface $entityManager,
        Review $review,
        OrderItemRepository $orderItemRepository,
        Security $security
    ): Response {
        $user = $security->getUser();
        $orderItems = null;
        if ($user != null){
            $orderItems = $orderItemRepository->getUserItemNotOrdered($user);
        }
        $game = $gameRepository->find($id);
        $editReviewId = $request->query->get('edit_review_id');
        $review = $reviewRepository->find($editReviewId);
        $form = $this->createForm(ReviewType::class, $review);
        $form->handleRequest($request);
        $reviewRate = $request->request->get('review-rate');
        $existingReview = null;

        if ($form->isSubmitted() && $form->isValid()) {
            if ($user) {
                $game->setWidthStar(($game->getWidthStar() - $review->getWidthStar() + ($reviewRate * 20)) / 2);
                $review->setWidthStar($reviewRate * 20);
                $entityManager->persist($game);
                $entityManager->persist($review);
                $entityManager->flush();
                $existingReview = $reviewRepository->getReview($id,$user);

                return $this->redirectToRoute('app_single_game', ['id' => $id], Response::HTTP_SEE_OTHER);
            }else{
                return $this->redirectToRoute('app_login', [], Response::HTTP_SEE_OTHER);
            }
        }

        return $this->render('game/single_game.html.twig' , [
            'game' => $game,
            'count' => $reviewRepository->countReviewsByGameId($game->getId()),
            'lastgame' => $gameRepository->lastTreeDiffId($id) ,
            'review' => $review,
            'form' => $form,
            'existingReview' => $existingReview ,
            'orderItems' => $orderItems
        ]);
        
    }
    #[Route('/single_game/{gameid}/{id}', name: 'app_single_game_delete', methods: ['POST'])]
    public function deleteReview($gameid, Request $request, Review $review, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$review->getId(), $request->getPayload()->get('_token'))) {
            $entityManager->remove($review);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_single_game', ['id' => $gameid], Response::HTTP_SEE_OTHER);
    }
    #[Route('/admin/games/', name: 'app_games_index', methods: ['GET'])]
    public function indexAdmin(GameRepository $gameRepository): Response
    {
        return $this->render('admin/game/index.html.twig', [
            'games' => $gameRepository->findAll(),
        ]);
    }

    #[Route('/admin/games/new', name: 'app_games_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $game = new Game();
        $form = $this->createForm(GameType::class, $game);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($game);
            $entityManager->flush();

            return $this->redirectToRoute('app_games_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/game/new.html.twig', [
            'game' => $game,
            'form' => $form,
        ]);
    }

    #[Route('/admin/games/{id}', name: 'app_games_show', methods: ['GET'])]
    public function show(Game $game): Response
    {
        return $this->render('admin/game/show.html.twig', [
            'game' => $game,
        ]);
    }

    #[Route('/admin/games/{id}/edit', name: 'app_games_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Game $game, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(GameType::class, $game);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_games_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/game/edit.html.twig', [
            'game' => $game,
            'form' => $form,
        ]);
    }

    #[Route('/admin/games/{id}', name: 'app_games_delete', methods: ['POST'])]
    public function delete(Request $request, Game $game, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$game->getId(), $request->getPayload()->get('_token'))) {
            $entityManager->remove($game);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_games_index', [], Response::HTTP_SEE_OTHER);
    }
}
