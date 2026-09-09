<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\AdvertisementRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class ProfileController extends AbstractController
{
    #[Route('/profile/{id}', name: 'app_profile')]
    public function show(User $user,AdvertisementRepository $advertisements): Response
    {
        return $this->render('profile/show.html.twig', [
            'user'=>$user,
            'advertisements'=>$advertisements->findAllByAuthor($user)
        ]);
    }
}
