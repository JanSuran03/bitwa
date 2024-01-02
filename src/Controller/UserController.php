<?php

namespace App\Controller;

use App\Entity\User;
use App\Service\UserService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('IS_AUTHENTICATED')]
class UserController extends AbstractController
{
    public UserService $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    private function classError(): Response
    {
        $this->addFlash('error', 'Neplatný požadavek, uživatel má špatný formát.');
        return $this->redirect('/index');
    }

    #[Route('/users', name: 'app_users')]
    public function list(): Response
    {
        return $this->render('users.html.twig',
            ['users' => $this->userService->getAll()]);
    }

    #[Route('/profile', name: 'app_user_profile')]
    #[IsGranted('ROLE_USER')]
    public function profile(): Response
    {
        $user = $this->getUser();
        if ($user instanceof User) {
            return $this->render('profile.html.twig',
                ['user' => [
                    'name' => $user->getName(),
                    'email' => $user->getEmail()]
                ]);
        }
        return $this->classError();
    }

    #[Route('/profile/change-name', name: 'app_change_user_name', methods: ['POST'])]
    public function changeName(Request $request): Response
    {
        $newName = $request->request->getString('_name');
        if (empty($newName)) {
            $this->addFlash('error', 'Jméno nemůže být prázdné.');
            return $this->redirectToRoute('app_user_profile');
        }
        $requestUser = $this->getUser();

        if ($requestUser instanceof User) {
            $dbUser = $this->userService->findOneByEmail($requestUser->getEmail());
            $this->userService->setUserName($dbUser, $newName);
            $this->addFlash('success', 'Jméno bylo úspěšně změněno.');
            return $this->redirectToRoute('app_user_profile');
        } else {
            return $this->classError();
        }
    }

    #[Route('/profile/change-email', name: 'app_change_user_email', methods: ['POST'])]
    public function changeEmail(Request $request): Response
    {
        $newEmail = $request->request->getString('_email');
        if (empty($newEmail)) {
            $this->addFlash('error', 'Email nemůže být prázdný.');
            return $this->redirectToRoute('app_user_profile');
        }
        $requestUser = $this->getUser();

        if ($requestUser instanceof User) {
            $dbUser = $this->userService->findOneByEmail($requestUser->getEmail());
            $this->userService->setEmail($dbUser, $newEmail);
            $this->addFlash('success', 'Email byl úspěšně změněn.');
            return $this->redirectToRoute('app_user_profile');
        } else {
            return $this->classError();
        }
    }

    #[Route('/profile/change-password', name: 'app_change_user_password', methods: ['POST'])]
    public function changePassword(Request $request, UserPasswordHasherInterface $passwordHasher): Response
    {
        $oldPassword = $request->request->getString('_old_password');
        $newPassword = $request->request->getString('_new_password');
        $confirmPassword = $request->request->getString('_confirm_password');
        if (empty($oldPassword) || empty($newPassword) || empty($confirmPassword)) {
            $this->addFlash('error', 'Hesla nemohou být prázdná.');
            return $this->redirectToRoute('app_user_profile');
        } else if ($newPassword != $confirmPassword) {
            $this->addFlash('error', 'Nová hesla se neshodují.');
            return $this->redirectToRoute('app_user_profile');
        }

        $requestUser = $this->getUser();
        if ($requestUser instanceof User) {
            $dbUser = $this->userService->findOneByEmail($requestUser->getEmail());
            if (!$passwordHasher->isPasswordValid($dbUser, $oldPassword)) {
                $this->addFlash('error', 'Staré heslo se neshoduje.');
                return $this->render('profile.html.twig',
                    [
                        'user' => [
                            'name' => $dbUser->getName(),
                            'email' => $dbUser->getEmail()],
                        'previous_input' => [
                            'old_password' => $oldPassword,
                            'new_password' => $newPassword,
                            'confirm_password' => $confirmPassword
                        ]
                    ]);

            }

            $hashed = $passwordHasher->hashPassword($dbUser, $newPassword);
            $dbUser->setPassword($hashed);

            $this->userService->updateUser($dbUser);
            $this->addFlash('success', 'Heslo bylo úspěšně změněno.');
            return $this->redirectToRoute('app_user_profile');
        } else {
            return $this->classError();
        }
    }

    #[Route('/users/create-by-admin', name: 'app_admin_create_user', methods: ['POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function createUserByAdmin(Request $request, UserPasswordHasherInterface $passwordHasher): Response
    {
        $username = $request->request->get('_username');
        $email = $request->request->get('_email');
        $password = $request->request->get('_password');
        $confirmPassword = $request->request->get('_confirm_password');

        $fail = false;
        if ($password != $confirmPassword && $fail = true) {
            $this->addFlash('error', 'Hesla se neshodují.');
        } else if ($this->userService->findOneByEmail($email) != null && $fail = true) {
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
                    'users' => $this->userService->getAll()
                ]);
        } else {
            $user = new User();
            $user->setName($username);
            $user->setEmail($email);

            $hashed = $passwordHasher->hashPassword($user, $password);
            $user->setPassword($hashed);

            $this->userService->createUser($user);

            $this->addFlash('success', 'Uživatel ' . $username . ' úspěšně vytvořen.');
            return $this->redirectToRoute('app_users');
        }
    }
}
