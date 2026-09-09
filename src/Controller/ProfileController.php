<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\AdvertisementRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class ProfileController extends AbstractController
{
    #[Route('/profile/{id}', name: 'app_profile')]
    public function show(User $user,AdvertisementRepository $advertisements,Request $request): Response
    {
        $search = trim((string) $request->query->get('q', ''));
        $location = trim((string) $request->query->get('location', ''));
        $sort = $request->query->get('sort', 'newest');
        $ads = $advertisements->findAllByAuthor($user, $search ?: null, $location ?: null, $sort);
        return $this->render('profile/show.html.twig', [
            'user'=>$user,
            'advertisements' => $ads,
            'q'              => $search,
            'location'       => $location,
            'sort'           => $sort,
            'total'          => count($ads),
            
        ]);
    }
}
