<?php

namespace App\Controller;

use App\Repository\SectionRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class HomepageController extends AbstractController
{
    #[Route('/', name: 'homepage')]
    public function index(SectionRepository $sections): Response
    {

        return $this->render('template.front.html.twig', [
            'controller_name' => 'HomepageController',
            'sections' => $sections->findAll()
        ]);
    }
    #[Route(
        # chemin vers la section avec son id
        path: '/section/{slug}',

        name: 'section',
        # si absent, donne 1 comme valeur par défaut
        defaults: ['/section/'=>1])]

    public function section(SectionRepository $sections, string $slug): Response
    {
        // récupération de la section
        $section = $sections->findOneBy(["section_slug"=>$slug]);
        return $this->render('homepage/section.html.twig', [
            'title' => 'Section '.$section->getSectionTitle(),
            'sectionDetail'=> $section->getSectionDetail(),
            'section' => $section,
            'sections' => $sections->findAll(),
        ]);
    }
}
