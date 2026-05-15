<?php

declare(strict_types=1);

namespace Src\Modules\User\Interface\Http\Controllers;

use Src\Exception\ServiceException;
use Src\Exception\UniqueConstraintException;
use Src\Modules\User\Domain\Service\UserService;
use Core\BaseController;
use Core\Routing\Attribute\Route;
use Core\Support\SecurityHelper;
use Src\Modules\Shared\Infrastructure\Http\Security\CsrfGuard;
use Src\Modules\Shared\Infrastructure\Http\Session\SessionManager;

#[Route('/user')]
final class UserCreateController extends BaseController
{
    public function __construct(
        private readonly UserService     $userService,
        private readonly CsrfGuard       $csrf,
        private readonly SessionManager  $session,
    ) {}

    #[Route('register', methods: ['GET'])]
    public function create(): void
    {
        $referer = $_SERVER['HTTP_REFERER'] ?? '';

        if (
            empty($this->session->get('redirect_after_login'))
            && !empty($referer)
            && !str_contains($referer, '/auth/login')
        ) {
            $this->session->setIntendedRedirect($referer);
        }

        $this->render('User/create.twig', [
            'csrf_token'    => $this->csrf->generateToken(),
            'flash_error'   => $this->getFlash('error'),
            'flash_success' => $this->getFlash('success'),
        ]);
    }

    #[Route('store', methods: ['POST'])]
    public function store(): void
    {
        $this->csrf->validateOrFail();

        $email    = SecurityHelper::sanitizeEmail($_POST['email'] ?? null);
        $password = SecurityHelper::sanitizeString(
            $_POST['password'] ?? '',
            'mot de passe',
            minLength: 8,
            maxLength: 30,
            pattern: '/^(?=.*[A-Z])(?=.*\d).+$/'
        );
        $cgu      = isset($_POST['cgu']);

        if (!$cgu) {
            $this->setFlash('error', 'Vous devez accepter les conditions générales d\'utilisation.');
            $this->redirect('/user/register');
        }

        try {
            $user = $this->userService->createUser($email, $password);

            $this->setFlash('success', 'Bienvenue ' . $user->email . ' ! Vous pouvez maintenant vous connecter.');
            $this->redirect('/auth/login');
        } catch (UniqueConstraintException $e) {
            $this->setFlash('error', 'Ce champ est déjà utilisé : ' . $e->getField());
            $this->redirect('/user/register');
        } catch (ServiceException $e) {
            $this->setFlash('error', 'Erreur du service d\'enregistrement. (Code : ' . $e->getErrorId() . ')');
            $this->redirect('/user/register');
        } catch (\Throwable $e) {
            $this->handleException($e, __METHOD__ . ' → System → ');
        }
    }
}
