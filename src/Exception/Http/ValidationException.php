<?php
// src/Exception/Http/ValidationException.php
namespace Src\Exception\Http;

class ValidationException extends \RuntimeException implements HttpExceptionInterface
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
