<?php

namespace App\Controller;

use App\Entity\Game;
use App\Repository\GameRepository;
use App\Repository\ReviewRepository;
use App\Repository\WishlistItemRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class GameController extends AbstractController
{
    #[Route('/game/list', name: 'app_game_list')]
    public function list(GameRepository $gameRepository): Response
    {
        $games = $gameRepository->findAll();

        return $this->render('game/list.html.twig', [
            'games' => $games,
        ]);
    }

    #[Route('/game/{id}', name: 'app_game_show')]
    public function show(
        Game $game,
        WishlistItemRepository $wishlistItemRepository,
        ReviewRepository $reviewRepository
    ): Response {
        $user = $this->getUser();
        $isInWishlist = false;
        $userReview = null;

        if ($user) {
            $isInWishlist = (bool) $wishlistItemRepository->findOneBy(['game' => $game, 'user' => $user]);
            $userReview = $reviewRepository->findOneBy(['game' => $game, 'user' => $user]);
        }

        return $this->render('game/show.html.twig', [
            'game' => $game,
            'is_in_wishlist' => $isInWishlist,
            'user_review' => $userReview,
        ]);
    }
}
