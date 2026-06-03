<?php

namespace Src\Modules\Stock\Entity;

use Core\Database\BaseModel;
use DateTime;

class StockProductLocation extends BaseModel
{
    public function __construct(

        // Obligatoire
        private int $_productStockId,
        private int $_stockLocationId,
        private int $_quantity,

        // Optionnel
        private ?int $_id = null,
        private ?DateTime $_createdAt = null,
        private ?DateTime $_updatedAt = null
    ) {}

    public int $productStockId {
        get => $this->_productStockId;
        set(int $value) => $this->_productStockId = $value;
    }

    public int $stockLocationId {
        get => $this->_stockLocationId;
        set(int $value) => $this->_stockLocationId = $value;
    }

    public ?int $id {
        get => $this->_id;
        set(?int $value) => $this->_id = $value;
    }

    public ?DateTime $createdAt {
        get => $this->_createdAt;
        set(?DateTime $value) => $this->_createdAt = $value;
    }

    public ?DateTime $updatedAt {
        get => $this->_updatedAt;
        set(?DateTime $value) => $this->_updatedAt = $value;
    }
}
