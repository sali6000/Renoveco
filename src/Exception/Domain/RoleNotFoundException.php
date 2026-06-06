<?php
// src/Exception/RoleNotFoundException.php
namespace Src\Exception\Domain;

class RoleNotFoundException extends \Exception implements DomainExceptionInterface
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
