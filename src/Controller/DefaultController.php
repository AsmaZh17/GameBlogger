<?php

namespace App\Controller;

use App\Repository\BlogRepository;
use App\Repository\GameRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

class DefaultController extends AbstractController
{
    #[Route('/', name: 'app_')]
    public function index(BlogRepository $blogRepository, GameRepository $gameRepository): Response
    {
        return $this->render('/index.html.twig', [
            'last' => $gameRepository->lastGame(),
            'blogs' => $blogRepository->lastTree(),
            'games' => $gameRepository->lastTree()
        ]);
    }
    #[Route('/search', name: 'app_search')]
    public function search(Request $request , GameRepository $gameRepository , BlogRepository $blogRepository): Response
    {
        $searchValue = $request->query->get('search', '');
        if($searchValue != null ){
            return $this->render('include/search.html.twig', [
                'games' => $gameRepository->findByValue($searchValue),
                'blogs' => $blogRepository->findByValue($searchValue)
            ]);    
        }
        return $this->render('include/search.html.twig', [
            'games' => $gameRepository->lastFoor(),
            'blogs' => $blogRepository->lastFoor()
        ]);
    }
    public function notFound(): Response
    {
        return $this->render('include/404.html.twig');
    }
}
