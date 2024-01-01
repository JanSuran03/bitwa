<?php

namespace App\Api\Controller;

use App\Service\UserService;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations\Delete;
use FOS\RestBundle\Controller\Annotations\View;

class UserApiController extends AbstractFOSRestController
{
    private UserService $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    #[View]
    #[Delete("/users/{id}")]
    public function delete(int $id): void
    {
        $this->userService->deleteById($id);
    }
}