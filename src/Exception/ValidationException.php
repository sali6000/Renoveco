<?php
// src/Exception/ValidationException.php
namespace Src\Exception;

class ValidationException extends \RuntimeException implements DomainExceptionInterface
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
