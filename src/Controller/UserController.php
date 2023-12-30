<?php

namespace App\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('IS_AUTHENTICATED')]
class UserController extends AbstractController {
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
        $this->addFlash('error', 'User cannot be parsed from request.');
        return $this->redirect('/index');
    }
}
