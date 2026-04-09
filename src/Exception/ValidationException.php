<?php
// src/Exception/ValidationException.php
namespace Src\Exception;

class ValidationException extends \Exception
{
    private string $field;

    public function __construct(string $message, string $field = "")
    {
        parent::__construct($message);
        $this->field = $field;
    }

    public function getField(): string
    {
        return $this->field;
    }
}
