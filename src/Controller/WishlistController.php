<?php

namespace App\Controller;

use App\Entity\Game;
use App\Entity\WishlistItem;
use App\Repository\WishlistItemRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class WishlistController extends AbstractController
{
    #[Route('/wishlist', name: 'app_wishlist')]
    #[IsGranted('ROLE_USER')]
    public function list(WishlistItemRepository $wishlistItemRepository): Response
    {
        $wishlistItems = $wishlistItemRepository->findBy(['user' => $this->getUser()]);

        return $this->render('wishlist/list.html.twig', [
            'wishlistItems' => $wishlistItems,
        ]);
    }

    #[Route('/wishlist/{id}/toggle', name: 'app_game_wishlist_toggle', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function wishlistToggle(
        Game $game,
        Request $request,
        EntityManagerInterface $entityManager,
        WishlistItemRepository $wishlistItemRepository
    ): Response {
        if (!$this->isCsrfTokenValid('wishlist' . $game->getId(), $request->request->get('_token'))) {
            $this->addFlash('error', 'Invalid CSRF token.');
            return $this->redirectToRoute('app_game_show', ['id' => $game->getId()]);
        }

        $user = $this->getUser();
        $wishlist = $wishlistItemRepository->findOneBy(['game' => $game, 'user' => $user]);

        if ($wishlist) {
            $entityManager->remove($wishlist);
            $this->addFlash('success', 'Removed from wishlist.');
        } else {
            $wishlist = new WishlistItem();
            $wishlist->setUser($user);
            $wishlist->setGame($game);
            $entityManager->persist($wishlist);
            $this->addFlash('success', 'Added to wishlist!');
        }

        $entityManager->flush();

        return $this->redirectToRoute('app_game_show', ['id' => $game->getId()]);
    }
}
