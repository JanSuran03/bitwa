<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('IS_AUTHENTICATED')]
class ManagedReservationsController extends AbstractController {
    #[Route('/reservations/managed', name: 'app_managed_reservations')]
    public function register(): Response {
        return $this->render('managed-reservations.html.twig');
    }
}
