<?php

namespace App\Controller;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

#[IsGranted('IS_AUTHENTICATED')]
class UsersController extends AbstractController {
    public UserRepository $userRepository;

    public function __construct(UserRepository $userRepository) {
        $this->userRepository = $userRepository;
    }

    #[Route('/users', name: 'app_users')]
    public function login(): Response {
        return $this->render('users.html.twig',
            ['users' => $this->userRepository->findAll()]);
    }

    #[Route('/users/create-by-admin', name: 'app_admin_create_user', methods: ['POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function createUserByAdmin(Request $request): Response {
        $username = $request->request->get('_username');
        $email = $request->request->get('_email');
        $password = $request->request->get('_password');
        $confirmPassword = $request->request->get('_confirm_password');

        $fail = false;
        if ($password != $confirmPassword && $fail = true) {
            $this->addFlash('error', 'Hesla se neshodují.');
        } else if ($this->userRepository->findByEmail($email) != null && $fail = true) {
            $this->addFlash('error', 'Zadaný email již používá jiný uživatel.');
        }

        if ($fail) {
            return $this->render('users.html.twig',
                [
                    'previous_input' => [
                        'username' => $username,
                        'email' => $email,
                        'password' => $password,
                        'confirm_password' => $confirmPassword
                    ],
                    'users' => $this->userRepository->findAll()
                ]);
        } else {
            $this->addFlash('success', 'Uživatel ' . $username . ' úspěšně vytvořen.');
            return $this->redirectToRoute('app_users');
        }
    }
}
