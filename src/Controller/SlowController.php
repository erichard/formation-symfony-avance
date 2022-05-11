<?php

namespace App\Controller;

use App\Repository\ArticleRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SlowController extends AbstractController
{
    #[Route('/slow', name: 'app_slow')]
    public function index(ArticleRepository $articleRepository): Response
    {
        $articles = $articleRepository->findAllObjects(100);

        return $this->render('slow/index.html.twig', [
            'articles' => $articles,
        ]);
    }
}
