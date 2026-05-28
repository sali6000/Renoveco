<?php

namespace Src\Exception\Application;

use Src\Exception\Application\ApplicationExceptionInterface;

class AuthentificationException extends \Exception implements ApplicationExceptionInterface
{
    public function __construct(
        string $message,
        private readonly string $errorCode = ''
    ) {
        parent::__construct($message);
    }

    public function getErrorCode(): string
    {
        return $this->errorCode;
    }
}
