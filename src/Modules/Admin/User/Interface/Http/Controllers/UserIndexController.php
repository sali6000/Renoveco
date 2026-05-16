<?php

namespace Src\Modules\Admin\User\Interface\Http\Controllers;

use Core\BaseController;
use Src\Modules\User\Domain\Service\UserService;
use Core\Routing\Attribute\Route;

#[Route('/admin/user')]
class UserIndexController extends BaseController
{
    public function __construct(
        private UserService $userService
    ) {
        parent::__construct('Admin/User');
    }

    #[Route('', methods: ['GET'])]
    public function index(): void
    {
        $this->set('users', $this->userService->getAllUsersForAdmin());
        $this->render();
    }
}
