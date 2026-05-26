<?php
// src/Exception/ServiceException.php
namespace Src\Exception;

final class ServiceException extends \RuntimeException implements DomainExceptionInterface
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
