<?php

namespace App\Controller;

use App\Entity\Sites;
use App\Form\SitesType;
use App\Repository\SitesRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/sites')]
class SitesController extends AbstractController
{
    #[Route('/', name: 'app_sites_index', methods: ['GET'])]
    public function index(SitesRepository $sitesRepository): Response
    {
        $sites = $sitesRepository->findAll();
        foreach ($sites as $site) {
            $site->lastStatus = $site->getStatus()->last() ? ($site->getStatus()->last()->getStatus() == 200 ? "healthy" : "down") : "noData";
        }

        return $this->render('sites/index.html.twig', [
            'sites' => $sites,
        ]);
    }

    #[Route('/new', name: 'app_sites_new', methods: ['GET', 'POST'])]
    public function new(Request $request, SitesRepository $sitesRepository): Response
    {
        $site = new Sites();
        $form = $this->createForm(SitesType::class, $site);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $sitesRepository->save($site, true);

            return $this->redirectToRoute('app_sites_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('sites/new.html.twig', [
            'site' => $site,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_sites_show', methods: ['GET'])]
    public function show(Sites $site): Response
    {
        return $this->render('sites/show.html.twig', [
            'site' => $site,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_sites_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Sites $site, SitesRepository $sitesRepository): Response
    {
        $form = $this->createForm(SitesType::class, $site);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $sitesRepository->save($site, true);

            return $this->redirectToRoute('app_sites_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('sites/edit.html.twig', [
            'site' => $site,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_sites_delete', methods: ['POST'])]
    public function delete(Request $request, Sites $site, SitesRepository $sitesRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$site->getId(), $request->request->get('_token'))) {
            $sitesRepository->remove($site, true);
        }

        return $this->redirectToRoute('app_sites_index', [], Response::HTTP_SEE_OTHER);
    }
}
