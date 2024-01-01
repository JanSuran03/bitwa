<?php

namespace App\Api\Controller;

use App\Api\DTO\UserResponse;
use App\Entity\User;
use App\Service\UserService;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations\Delete;
use FOS\RestBundle\Controller\Annotations\Get;
use FOS\RestBundle\Controller\Annotations\View;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Request;

class UserApiController extends AbstractFOSRestController
{
    private UserService $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    #[View]
    #[Get("/users")]
    public function list(Request $request): array
    {
        $users = $this->userService->getAllByApiQueries($request->query->all());
        return array_map(
            fn(User $user) => UserResponse::fromEntity($user),
            $users
        );
    }

    #[View]
    #[ParamConverter('user', class: 'App\Entity\User')]
    #[Get("/users/{id}")]
    public function detail(User $user): UserResponse
    {
        return UserResponse::fromEntity($user);
    }

    #[View]
    #[Delete("/users/{id}")]
    public function delete(int $id): void
    {
        $this->userService->deleteById($id);
    }
}