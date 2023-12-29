<?php

namespace App\Controller;

use App\Repository\UserRepository;
use App\Service\UserService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('IS_AUTHENTICATED')]
class UsersController extends AbstractController {
    public UserService $userService;

    public function __construct(UserService $userService) {
        $this->userService = $userService;
    }
    #[Route('/users', name: 'app_users')]
    public function login(): Response {
        return $this->render('users.html.twig',
            ['users' => $this->userService->getAll()]);
    }
}
