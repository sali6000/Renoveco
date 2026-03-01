<?php
// src/Exception/UniqueConstraintException.php
namespace App\Exception;

class UniqueConstraintException extends \Exception
{
    private string $field;

    public function __construct(string $field)
    {
        parent::__construct("Violation de clé UNIQUE");
        $this->field = $field;
    }

    public function getField(): string
    {
        return $this->field;
    }
}
