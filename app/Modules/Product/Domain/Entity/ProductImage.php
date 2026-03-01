<?php

namespace App\Modules\Product\Domain\Entity;

use App\Database\SchemaMysql;

class ProductImage
{
    private ?int $_id;
    private string $_filePath;
    private ?string $_alt;
    private ?bool $_isMain;

    public function __construct(
        string $filePath,
        ?string $alt = null,
        ?int $id = null,
        ?bool $isMain = false
    ) {
        $this->filePath = $filePath;
        $this->alt = $alt;
        $this->id = $id;
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

    public string $filePath {
        get => $this->_filePath;
        set(string $value) {
            $this->_filePath = $value;
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

    public static function fromArray(array $row): ?self
    {
        $productImage = new self(
            $row[SchemaMysql::fieldProperty(SchemaMysql::PRODUCT_IMAGE_FILE_PATH)] ?? '',
            $row[SchemaMysql::fieldProperty(SchemaMysql::PRODUCT_IMAGE_ALT_TEXT)] ?? null,
            $row[SchemaMysql::fieldProperty(SchemaMysql::PRODUCT_IMAGE_ID)] ?? null,
            (bool) ($row[SchemaMysql::fieldProperty(SchemaMysql::PRODUCT_IMAGE_IS_MAIN)] ?? false)
        );
        return $productImage;
    }
}
