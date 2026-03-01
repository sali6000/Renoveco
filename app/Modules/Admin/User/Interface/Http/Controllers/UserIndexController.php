<?php

namespace App\Modules\Admin\User\Interface\Http\Controllers;

if (!defined('SECURE_CHECK')) {
    die('Direct access not permitted');
}

use Core\BaseController;
use App\Modules\User\Domain\Service\UserService;
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
