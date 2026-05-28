<?php

declare(strict_types=1);

namespace Src\Modules\User\Interface\Http\Controllers;

use Core\BaseController;
use Core\Routing\Attribute\Route;
use Core\Support\SecurityHelper;
use Src\Exception\Application\ApplicationExceptionInterface;
use Src\Exception\Http\HttpExceptionInterface;
use Src\Modules\Shared\Infrastructure\Http\Security\CsrfException;
use Src\Modules\Shared\Infrastructure\Http\Security\CsrfGuard;
use Src\Modules\Shared\Infrastructure\Http\Session\SessionManager;
use Src\Modules\User\Application\UseCase\UserCreateUseCase;

#[Route('/user')]
final class UserCreateController extends BaseController
{
    public function __construct(
        private readonly UserCreateUseCase $createUser,
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
        try {

            // Validation du formulaire
            $this->csrf->validateOrFail();

            // Validation CGU
            if (!isset($_POST['cgu'])) {
                $this->flashAndRedirect('error', 'Vous devez accepter les conditions générales d\'utilisation.', '/user/register');
            }

            // Normalisation de l'email et du mot de passe
            $email    = SecurityHelper::sanitizeEmail($_POST['email'] ?? null);
            $password = SecurityHelper::sanitizeString($_POST['password'] ?? null);

            // Validation de l'email et du mot de passe
            $email    = SecurityHelper::validateEmail($email, 'Email');
            $password = SecurityHelper::validateString(
                $password,
                'Mot de passe',
                minLength: 8,
                maxLength: 30,
                pattern: '/^(?=.*[A-Z])(?=.*\d).+$/'
            );
        } catch (CsrfException | HttpExceptionInterface $e) {

            // Redirection en cas d'échec
            $this->flashAndRedirect('error', $e->getMessage(), '/user/register');
        }

        // Récupération de l'IP
        $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';

        try {
            // Création de l'user en base
            $result = $this->createUser->execute($email, $password, $ip);

            // Résultat en cas de réussite ou échec
            $result->isSuccess()
                ? $this->flashAndRedirect('success', 'Inscription réussie, connectez vous.', '/auth/login')
                : $this->flashAndRedirect('error', $result->getMessage(), '/auth/login');
        } catch (ApplicationExceptionInterface $e) {
            $this->flashAndRedirect('error', $e->getMessage(), '/auth/login');
        }
    }
}
