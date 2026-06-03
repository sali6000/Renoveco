<?php

namespace Src\Modules\Stock\Entity;

use Core\Database\BaseModel;

class StockLocation extends BaseModel
{
    public function __construct(

        // Obligatoire
        private string $_name,

        // Optionnelle
        private ?int $_id = null,
        private ?string $_description = null,
    ) {}

    public string $name {
        get => $this->_name;
        set(string $value) => $this->_name = $value;
    }

    public ?int $id {
        get => $this->_id;
        set(?int $value) => $this->_id = $value;
    }

    public ?string $description {
        get => $this->_description;
        set(?string $value) => $this->_description = $value;
    }
}
