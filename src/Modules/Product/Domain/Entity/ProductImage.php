<?php

namespace Src\Modules\Product\Domain\Entity;

use Core\Database\BaseModel;
use Src\Modules\Product\Infrastructure\Schema\ProductImageSchemaMysql;

class ProductImage extends BaseModel
{
    public function __construct(
        private string $_filePath,
        private bool $_isMain = false,
        private ?int $_id = null,
        private ?string $_alt = null
    ) {}

    // ==========================================================
    // = GETTERS / SETTERS (Hook)
    // ==========================================================
    public string $filePath {
        get => $this->_filePath;
        set(string $value) {
            $this->_filePath = $value;
        }
    }

    public bool $isMain {
        get => $this->_isMain;
        set(bool $value) {
            $this->_isMain = $value;
        }
    }

    public ?int $id {
        get => $this->_id;
        set(?int $value) {
            $this->_id = $value;
        }
    }

    public ?string $alt {
        get => $this->_alt;
        set(?string $value) {
            $this->_alt = $value;
        }
    }

    public static function fromArray(array $row): self
    {
        return new self(
            _filePath: self::getString($row, ProductImageSchemaMysql::FILE_PATH),
            _alt: self::getStringOrNull($row, ProductImageSchemaMysql::ALT_TEXT),
            _id: self::getIntOrNull($row, ProductImageSchemaMysql::ID),
            _isMain: self::getBoolOrFalse($row, ProductImageSchemaMysql::IS_MAIN),
        );
    }
}
