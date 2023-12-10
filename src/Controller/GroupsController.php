<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class GroupsController extends AbstractController {
    #[Route('/groups', name: 'app_groups')]
    public function groups(): Response {
        return $this->render('groups.html.twig');
    }
}
