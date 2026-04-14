<?php

namespace Src\Modules\Stock\Entity;

use Src\Modules\Category\Entity\Category;
use Src\Database\SchemaMysql;
use Core\Database\BaseModel;
use DateTime;

class Inventory extends BaseModel
{
    // ==========================================================
    // Propriétés 11
    // ==========================================================
    //------------
    // Obligatoires 4
    //------------
    private string $_name;
    private string $_slug;
    private string $_reference;
    private bool $_isActive = true;

    //------------
    // Optionnelles 7
    //------------
    private ?int $_id = null;
    private ?string $_description = null;
    private ?string $_composition = null;
    private ?string $_useFor = null;
    private ?int $_defaultSupplierId = null;
    private ?DateTime $_createdAt = null;
    private ?DateTime $_updatedAt = null;

    // ==========================================================
    // Relationnelles (2)
    // ==========================================================
    /** @var ProductImage[] */
    private ?array $_images = [];

    /** @var Category[] */
    private ?array $_categories = [];

    // ==========================================================
    // Constructeur
    // ==========================================================
    public function __construct(
        string $name,
        string $slug,
        string $reference,
        bool $isActive,
        ?int $id = null,
        ?string $description = null,
        ?array $categories = []
    ) {
        $this->id = $id;
        $this->name = $name;
        $this->slug = $slug;
        $this->description = $description;
        $this->categories = $categories;
        $this->isActive = $isActive;
        $this->reference = $reference;
    }

    // ==========================================================
    // = GETTERS / SETTERS 11
    // ==========================================================
    //------------
    // Obligatoires 4
    //------------
    public string $name {
        get => $this->_name;
        set(string $value) => $this->_name = $value;
    }

    public string $slug {
        get => $this->_slug ?? '';
        set(string $value) => $this->_slug = $value ?? '';
    }

    public string $reference {
        get => $this->_reference ?? '';
        set(string $value) => $this->_reference = $value ?? '';
    }

    public bool $isActive {
        get => $this->_isActive;
        set(bool $value) => $this->_isActive = $value;
    }

    //------------
    // Optionnelles 7
    //------------
    public ?int $id {
        get => $this->_id;
        set(?int $value) => $this->_id = $value;
    }

    public ?string $description {
        get => $this->_description ?? '';
        set(?string $value) => $this->_description = $value ?? '';
    }

    public ?string $composition {
        get => $this->_composition ?? '';
        set(?string $value) => $this->_composition = $value ?? '';
    }

    public ?string $useFor {
        get => $this->_useFor ?? '';
        set(?string $value) => $this->_useFor = $value ?? '';
    }

    public ?int $defaultSupplierId {
        get => $this->_defaultSupplierId;
        set(?int $values) => $this->_defaultSupplierId = $values;
    }
    public ?DateTime $createdAt {
        get => $this->_createdAt;
        set(?DateTime $values) => $this->_createdAt = $values;
    }

    public ?DateTime $updatedAt {
        get => $this->_updatedAt;
        set(?DateTime $values) => $this->_updatedAt = $values;
    }

    // ==========================================================
    // = (RELATIONS) GETTERS / SETTERS (Hook)
    // ==========================================================
    public ?array $categories {
        get => $this->_categories;
        set(?array $values) => $this->_categories = $values;
    }



    // ==========================================================
    // Product <= $row <= rows[] 
    // ==========================================================

}
