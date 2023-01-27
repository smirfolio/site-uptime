<?php

namespace App\Controller;

use App\Entity\Status;
use App\Repository\SitesRepository;
use App\Repository\StatusRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


#[Route('/status')]
class StatusController extends AbstractController
{
    public function __construct(
        private StatusRepository $status,
        private SitesRepository $sites,
    ){

    }

    #[Route('/', name: 'app_status_index', methods: ['GET'])]
    public function index(StatusRepository $statusRepository): Response
    {
        return $this->render('status/index.html.twig', [
            'status' => $statusRepository->findAll(),
        ]);
    }

    #[Route('/site/{id}', name: 'app_status_site')]
    public function show(int $id): Response
    {
        $site = $this->sites->find($id);

        return $this->render('status/show.html.twig', [
            'status' => $site->getStatus()->getValues() ?? "",
            'site' => $site->getSiteAdress() ?? "",
            'site_id' => $id?? ""
        ]);
    }


}
