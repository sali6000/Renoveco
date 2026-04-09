<?php
// src/Exception/ServiceException.php
namespace Core\Routing\Exception;

// ServiceException.php
class RoutingException extends \Exception
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
