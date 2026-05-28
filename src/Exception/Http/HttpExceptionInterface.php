<?php
// Exception/Http/HttpExceptionInterface.php

namespace Src\Exception\Http;

/**
 * @see ValidationException saisie invalide
 * @see CsrfException sécurité HTTP
 */
interface HttpExceptionInterface
{
    public function getMessage(): string;
    public function getErrorCode(): string;
}
