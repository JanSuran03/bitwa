<?php

namespace App\Controller;

use App\Entity\Group;
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

    const NO_GROUP = -1;

    #[Route('/groups/new', name: 'app_groups_new')]
    #[IsGranted('ROLE_ADMIN')]
    public function newGroup(Request $request): Response {
        $groupName = $request->request->getString('_group_name');
        $parentId = $request->request->getInt('_parent_group');

        if (empty($groupName)) {
            $this->addFlash('error', 'Vyplňte název skupiny.');
            return $this->redirectToRoute('app_groups');
        }

        $parentGroup = $this->groupRepository->find($parentId);
        if ($parentId != self::NO_GROUP && $parentGroup == null) {
            $this->addFlash('error', 'Nadřazená skupina s identifikátorem ' . $parentId . ' nebyla nalezena.');
            return $this->redirectToRoute('app_groups');
        }

        if ($this->groupRepository->findOneBy(['name' => $groupName]) !== null) {
            $this->addFlash('error', 'Skupina s názvem ' . $groupName . ' již existuje.');
            return $this->redirectToRoute('app_groups');
        }

        $group = new Group();
        $group->setName($groupName);
        $group->setParent($parentGroup);
        $this->groupRepository->addGroup($group);

        $this->addFlash('success', 'Skupina ' . $groupName . ' byla vytvořena.');
        return $this->redirectToRoute('app_groups');
    }

    #[Route('/groups/{id}', name: 'app_group')]
    #[IsGranted('ROLE_USER')]
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
