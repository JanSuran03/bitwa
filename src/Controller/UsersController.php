<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Service\UserService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
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

    #[Route('/users/create-by-admin', name: 'app_admin_create_user', methods: ['POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function createUserByAdmin(Request $request, UserPasswordHasherInterface $passwordHasher): Response {
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
            $user = new User();
            $user->setName($username);
            $user->setEmail($email);

            $hashed = $passwordHasher->hashPassword($user, $password);
            $user->setPassword($hashed);

            $this->userRepository->addUser($user);

            $this->addFlash('success', 'Uživatel ' . $username . ' úspěšně vytvořen.');
            return $this->redirectToRoute('app_users');
        }
    }
}
