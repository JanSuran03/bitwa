<?php

namespace App\Controller;

use App\Entity\User;
use App\Service\UserService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('IS_AUTHENTICATED')]
class UserController extends AbstractController {
    public UserService $userService;

    public function __construct(UserService $userService) {
        $this->userService = $userService;
    }

    private function classError() {
        $this->addFlash('error', 'Neplatný požadavek, uživatel má špatný formát.');
        return $this->redirect('/index');
    }

    #[Route('/profile', name: 'app_user_profile')]
    #[IsGranted('ROLE_USER')]
    public function profile(Request $request): Response {
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
    public function changeName(Request $request): Response {
        $newName = $request->request->get('_name');
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
    public function changeEmail(Request $request): Response {
        return $this->classError();
    }

    #[Route('/profile/change-password', name: 'app_change_user_password', methods: ['POST'])]
    public function changePassword(Request $request): Response {
        return $this->classError();
    }
}
