<?php

namespace App\Controller;

use App\Entity\Advertisement;
use App\Entity\AdvertisementImage;
use App\Form\AdvertisementType;
use App\Repository\AdvertisementRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;

use Symfony\Component\HttpFoundation\File\Exception\FileException;
// use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\String\Slugger\SluggerInterface;

final class AdvertisementController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function home():Response{
        return $this->redirectToRoute('app_advertisement');
    }


    #[Route('/advertisements', name: 'app_advertisement')]
    public function index(AdvertisementRepository $advertisements,EntityManagerInterface $em,Request $request): Response
    {
        $page = max(1, $request->query->getInt('page', 1));
        $search = trim((string) $request->query->get('q', ''));
        $location = trim((string) $request->query->get('location', ''));
        $sort = $request->query->get('sort', 'newest');
        $perPage = 10;
        $paginator = $advertisements->findAllPaginated($page, $perPage,$search ?: null, $location ?: null, $sort);
        return $this->render('advertisement/index.html.twig', [
            'advertisements' => $paginator,
            'page'  => $page,
            'pages' => (int) ceil(count($paginator) / $perPage),
            'total' => count($paginator),
            'q'=> $search,
            'location'=> $location,
            'sort'=> $sort,
        ]);
    }

    #[Route('/advertisements/topLiked', name: 'app_advertisement_topliked')]
    public function topLiked(AdvertisementRepository $advertisements,EntityManagerInterface $em): Response
    {
        return $this->render('advertisement/top_liked.html.twig', [
            'advertisements' => $advertisements->findAllWithMinFavorites(2),
        ]);
    }

    

    

    #[Route('/advertisements/{advertisement}', name: 'app_advertisement_show')]
    #[IsGranted(Advertisement::VIEW, 'advertisement')]
    public function showOne(Advertisement $advertisement):Response {
        return $this->render('advertisement/show.html.twig', [
            'advertisement' => $advertisement,
        ]);
    }

    #[Route('/advertisements/add', name: 'app_advertisement_add',priority:2)]
    #[IsGranted('ROLE_VERIFIED')]
    public function add(Request $request,EntityManagerInterface $em,SluggerInterface $slugger) : Response {
        
        $form = $this->createForm(AdvertisementType::class, new Advertisement()); 

        $form->handleRequest($request);

        if($form->isSubmitted()&& $form->isValid()){
            $advertisement = $form->getData();
            $advertisement->setAuthor($this->getUser());
            $this->handleImageUploads($form, $advertisement, $slugger);
            $em->persist($advertisement);
            $em->flush();
            $this->addFlash('success','Your advertisement has been added');
            return $this->redirectToRoute('app_advertisement');
        }
        

        return $this->render('advertisement/add.html.twig',
        [
            'form'=>$form
        ]);
    }

    #[Route('/advertisements/{advertisement}/edit', name: 'app_advertisement_edit')]
    #[IsGranted(Advertisement::EDIT,'advertisement')]
    public function edit(Advertisement $advertisement, Request $request,EntityManagerInterface $em,SluggerInterface $slugger) : Response {

        $form = $this->createForm(AdvertisementType::class,$advertisement, [
            'require_images' => false,
        ]);

        $form->handleRequest($request);

        if($form->isSubmitted()&& $form->isValid()){
            $advertisement = $form->getData();
            $em->persist($advertisement);
            $this->handleImageUploads($form, $advertisement, $slugger);
            $em->flush();
            $this->addFlash('success','Your advertisement has been updated');
            return $this->redirectToRoute('app_advertisement');
        }
        

        return $this->render('advertisement/edit.html.twig',
        [
            'form'=>$form
        ]);
    }
    private function handleImageUploads(
        FormInterface $form,
        Advertisement $advertisement,
        SluggerInterface $slugger,
        ): void {
        $files = $form->get('imageFiles')->getData();

        if (!$files) {
            return;
        }

        $position = count($advertisement->getImages());

        foreach ($files as $file) {
            if (count($advertisement->getImages()) >= 10){
                $this->addFlash('error', 'Maximum 10 photos reached.');
                break;
            }
            $original = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
            $newName = $slugger->slug($original) . '-' . uniqid() . '.' . $file->guessExtension();

            try {
                $file->move($this->getParameter('ads_directory'), $newName);
            } catch (FileException $e) {
                $this->addFlash('error', 'Could not save one of the photos.');
                continue;
            }

            $image = new AdvertisementImage();
            $image->setFilename($newName);
            $image->setPosition($position++);
            $advertisement->addImage($image);
        }
    }

    #[Route('/advertisements/{advertisement}/delete', name: 'app_advertisement_delete', methods: ['POST'])]
    #[IsGranted(Advertisement::EDIT,'advertisement')]
    public function delete(Advertisement $advertisement, Request $request,EntityManagerInterface $em) : Response {
        if (!$this->isCsrfTokenValid('delete' . $advertisement->getId(), $request->request->get('_token'))) {
            throw $this->createAccessDeniedException('Invalid token');
        }
        foreach ($advertisement->getImages() as $image) {
            $path = $this->getParameter('ads_directory') . '/' . $image->getFilename();
            if (is_file($path)) {
                unlink($path);
            }
        }
        $em->remove($advertisement);
        $em->flush();
        $this->addFlash('success', 'Your advertisement has been deleted');
        return $this->redirectToRoute('app_advertisement');
    }
}
