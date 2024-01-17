<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    #[Route('/home', name: 'app_home')]
    public function index(): Response
    {
        $configFromSymfony = $this->getParameter('app.url');

        return $this->render('index.html.twig',['configFromSymfony' => $configFromSymfony]);
    }

}
