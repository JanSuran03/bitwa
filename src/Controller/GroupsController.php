<?php

namespace App\Controller;

use App\Repository\GroupRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_ADMIN')]
class GroupsController extends AbstractController {
    private GroupRepository $groupRepository;

    public function __construct(GroupRepository $groupRepository) {
        $this->groupRepository = $groupRepository;
    }

    #[Route('/groups', name: 'app_groups')]
    #[IsGranted('ROLE_USER')]
    public function groups(): Response {
        return $this->render('groups.html.twig',
            ['groups' => $this->groupRepository->findAll()]
        );
    }

    #[Route('/groups/{id}', name: 'app_group')]
    public function group(Request $request, int $id): Response {
        $group = $this->groupRepository->find($id);

        if ($group == null) {
            $this->addFlash('error', 'Skupina s identifikátorem ' . $id . ' nebyla nalezena.');
            return $this->redirectToRoute('app_groups');
        }

        return $this->render('group.html.twig',
            ['group' => $group]
        );
    }
}
