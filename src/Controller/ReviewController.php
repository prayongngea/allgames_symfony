<?php

namespace App\Controller;

use App\Entity\Game;
use App\Entity\Review;
use App\Repository\ReviewRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class ReviewController extends AbstractController
{
    #[Route('/review/{id}/form', name: 'app_review_form', methods: ['GET', 'POST'])]
    public function reviewForm(
        Game $game,
        Request $request,
        ReviewRepository $reviewRepository,
        Security $security,
        EntityManagerInterface $em
    ): Response {
        $user = $security->getUser();

        if (!$user) {
            return $this->render('review/_form_guest.html.twig', [
                'gameId' => $game->getId(),
            ]);
        }

        $review = $reviewRepository->findOneBy(['game' => $game, 'user' => $user]);

        if ($request->isMethod('POST')) {
            $token = $request->request->get('_token');
            if (!$this->isCsrfTokenValid('review' . $game->getId(), $token)) {
                $this->addFlash('error', 'Invalid CSRF token.');
                return $this->redirectToRoute('app_game_show', ['id' => $game->getId()]);
            }

            $reviewValue = $request->request->get('review');
            if ($reviewValue !== null && isset($reviewValue['review'])) {
                if (!$review) {
                    $review = new Review();
                    $review->setGame($game);
                    $review->setUser($user);
                }
                $review->setReview((bool)(int)$reviewValue['review']);
                $review->setComment($reviewValue['comment'] ?? null);
                $em->persist($review);
                $em->flush();
                $this->addFlash('success', 'Review submitted!');
            }

            return $this->redirectToRoute('app_game_show', ['id' => $game->getId()]);
        }

        return $this->render('review/_form.html.twig', [
            'gameId' => $game->getId(),
            'review' => $review,
        ]);
    }
}
