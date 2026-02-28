<?php

namespace App\Controller;

use App\Repository\GameRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class PageController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(GameRepository $gameRepository): Response
    {
        $games = $gameRepository->findBy([], ['releaseDate' => 'DESC'], 3);

        return $this->render('page/index.html.twig', [
            'games' => $games,
        ]);
    }

    #[Route('/about', name: 'app_about')]
    public function about(): Response
    {
        return $this->render('page/about.html.twig', []);
    }
}
