<?php

namespace App\Controller;

use App\Entity\Article;
use App\Entity\Section;
use App\Repository\ArticleRepository;
use App\Repository\SectionRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

class ArticleDetailController extends AbstractController
{
    #[Route('/article/{id}', name: 'app_article_detail')]
    public function show(Article $article,SectionRepository $sections): Response
    {
        if (!$article) {
            throw new NotFoundHttpException('Article non trouvé');
        }

        return $this->render('template.articleDetail.html.twig', [
            'article' => $article,
            'sections' => $sections,
        ]);
    }
}