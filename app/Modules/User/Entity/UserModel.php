<?php

namespace App\Modules\User\Entity;

class UserModel
{
    private ?int $_id = null;

    public ?int $id {
        get => $this->_id;
        set(?int $value) {
            $this->_id = $value;
        }
    }
}
