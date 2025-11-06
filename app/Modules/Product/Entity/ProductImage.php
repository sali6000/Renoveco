<?php

namespace App\Modules\Product\Entity;

class ProductImage
{
    private ?int $_id;

    private string $_path;

    private ?string $_alt;

    private ?bool $_isMain;

    public function __construct(
        string $path,
        ?string $alt = null,
        ?bool $isMain = false,
        ?int $id = null
    ) {
        $this->id = $id;
        $this->path = $path;
        $this->alt = $alt;
        $this->isMain = $isMain;
    }

    // ==========================================================
    // = GETTERS / SETTERS (Hook)
    // ==========================================================

    public ?int $id {
        get => $this->_id;
        set(?int $value) {
            $this->_id = $value;
        }
    }

    public string $path {
        get => $this->_path;
        set(string $value) {
            $this->_path = $value;
        }
    }

    public ?string $alt {
        get => $this->_alt;
        set(?string $value) {
            $this->_alt = $value;
        }
    }

    public ?bool $isMain {
        get => $this->_isMain;
        set(?bool $value) {
            $this->_isMain = $value;
        }
    }

    public static function fromArray(array $data): self
    {
        // Ex: new ProductCategory(5, 'Informatique', 'informatique');
        return new self(
            $data['file_path'] ?? '',        // <-- path en premier
            $data['alt_text'] ?? null,       // alt
            $data['is_main'] ?? false,       // isMain
            $data['id'] ?? null              // id
        );
    }
}
