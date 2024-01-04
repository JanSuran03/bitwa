<?php

namespace App\Controller;

use App\Entity\Group;
use App\Entity\User;
use App\Service\GroupService;
use App\Service\UserService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_ADMIN')]
class GroupController extends AbstractController
{
    private GroupService $groupRepository;
    private UserService $userService;

    public function __construct(GroupService $groupRepository, UserService $userService)
    {
        $this->groupRepository = $groupRepository;

        $this->userService = $userService;
    }

    #[Route('/groups', name: 'app_groups')]
    #[IsGranted('ROLE_USER')]
    public function groups(): Response
    {
        return $this->render('groups.html.twig',
            ['groups' => $this->groupRepository->findAll()]
        );
    }

    const NO_GROUP = -1;

    #[Route('/groups/{groupId}/new', name: 'app_group_new_admin_or_user')]
    #[IsGranted('ROLE_ADMIN')]
    public function newAdminOrUser(Request $request, $groupId): Response
    {
        $userId = $request->request->getInt('_user_name');
        $roleValue = $request->request->getInt('_role_option');

        if ($userId == -1 || $roleValue == -1) {
            $this->addFlash('error', 'Vyberte všechny údaje.');
            return $this->redirectToRoute('app_group', ['id' => $groupId]);
        }

        $user = $this->userService->getOneById($userId);
        $group = $this->groupRepository->findById($groupId);

        if($roleValue == 1 && $group->getManagers()->contains($user)){
            $this->addFlash('error', 'Uživatel již má roli správce.');
            return $this->redirectToRoute('app_group', ['id' => $groupId]);
        }

        if($roleValue == 2 && $group->getMembers()->contains($user)){
            $this->addFlash('error', 'Uživatel již má roli člena.');
            return $this->redirectToRoute('app_group', ['id' => $groupId]);
        }

        switch ($roleValue){
            case 1:
                $this->groupRepository->addManager($group, $user);
                break;
            case 2:
                $this->groupRepository->addMember($group, $user);
                break;
        }

        $this->addFlash('success', 'Uživatel ' . $user->getName() . ' byl přidán.');
        return $this->redirectToRoute('app_group', ['id' => $groupId]);
    }

    #[Route('/groups/new', name: 'app_groups_new')]
    #[IsGranted('ROLE_ADMIN')]
    public function newGroup(Request $request): Response
    {
        $groupName = $request->request->getString('_group_name');
        $parentId = $request->request->getInt('_parent_group');

        if (empty($groupName)) {
            $this->addFlash('error', 'Vyplňte název skupiny.');
            return $this->redirectToRoute('app_groups');
        }

        $parentGroup = $this->groupRepository->findById($parentId);
        if ($parentId != self::NO_GROUP && $parentGroup == null) {
            $this->addFlash('error', 'Nadřazená skupina s identifikátorem ' . $parentId . ' nebyla nalezena.');
            return $this->redirectToRoute('app_groups');
        }

        if ($this->groupRepository->findByName($groupName) !== null) {
            $this->addFlash('error', 'Skupina s názvem ' . $groupName . ' již existuje.');
            return $this->redirectToRoute('app_groups');
        }

        $group = new Group();
        $group->setName($groupName);
        $group->setParent($parentGroup);
        $this->groupRepository->create($group);

        $this->addFlash('success', 'Skupina ' . $groupName . ' byla vytvořena.');
        return $this->redirectToRoute('app_groups');
    }

    #[Route('/groups/{id}', name: 'app_group')]
    #[IsGranted('ROLE_USER')]
    public function group(int $id): Response
    {
        $group = $this->groupRepository->findById($id);

        if ($group == null) {
            $this->addFlash('error', 'Skupina s identifikátorem ' . $id . ' nebyla nalezena.');
            return $this->redirectToRoute('app_groups');
        }

        return $this->render('group.html.twig',
            ['group' => $group,
             'users' => $this->userService->getAll()]
        );
    }
}
