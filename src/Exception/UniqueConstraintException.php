<?php
// src/Exception/UniqueConstraintException.php
namespace Src\Exception;

class UniqueConstraintException extends \Exception implements DomainExceptionInterface
{
    public function __construct(private readonly string $errorCode = '')
    {
        parent::__construct("Violation de clé UNIQUE");
        $this->errorCode = $errorCode;
    }

    public function getErrorCode(): string
    {
        return $this->errorCode;
    }
}
