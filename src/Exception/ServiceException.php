<?php
// src/Exception/ServiceException.php
namespace Src\Exception;

// ServiceException.php
class ServiceException extends \Exception
{
    private string $errorId;

    public function __construct(string $message, int $code = 0, ?\Throwable $previous = null, string $errorId = '')
    {
        parent::__construct($message, $code, $previous);
        $this->errorId = $errorId;
    }

    public function getErrorId(): string
    {
        return $this->errorId;
    }
}
