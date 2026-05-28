<?php
// src/Exception/UniqueConstraintException.php
namespace Src\Exception\Domain;

class UniqueConstraintException extends \Exception implements DomainExceptionInterface
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
