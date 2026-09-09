<?php

namespace App\Controller;

use App\Entity\Advertisement;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

final class FavoriteController extends AbstractController
{
    #[Route('/favorite/{id}', name: 'app_favorite')]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function favorite(Advertisement $advertisement,EntityManagerInterface $em,Request $request): Response
    {
        $currentUser = $this->getUser();
        $advertisement->addFavoritedBy($currentUser);
        $em->persist($advertisement);
        $em->flush();

        return $this->redirect($request->headers->get('referer'));
    }

    #[Route('/unfavorite/{id}', name: 'app_unfavorite')]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function unfavorite(Advertisement $advertisement,EntityManagerInterface $em,Request $request): Response
    {
        $currentUser = $this->getUser();
        $advertisement->removeFavoritedBy($currentUser);
        $em->persist($advertisement);
        $em->flush();
        return $this->redirect($request->headers->get('referer'));

    }
}
