<?php

namespace Src\Modules\User\Domain\Entity;

use Core\Database\BaseModel;
use Src\Database\SchemaMysql;

class Role extends BaseModel
{

    public function __construct(

        // Obligatoires
        private string $_name,

        // Optionnelles
        private ?int $_id = null,
        private ?bool $_isActive = null,

        // List
        private array $_users = [],
    ) {}

    public string $name {
        get => $this->_name;
        set(string $value) => $this->_name = $value;
    }

    public ?int $id {
        get => $this->_id;
        set(?int $value) => $this->_id = $value;
    }

    public ?bool $isActive {
        get => $this->_isActive;
        set(?bool $value) => $this->_isActive = $value;
    }

    /** @var User[] */
    public array $users {
        get => $this->_users;
        set(array $value) => $this->_users = $value;
    }

    public function addUser(User $user): void
    {
        foreach ($this->_users as $u) {

            // éviter doublon
            if ($u->id === $user->id) return;
        }
        $this->_users[] = $user;
    }

    public function removeUser(User $user): void
    {
        $this->_users = array_filter(
            $this->_users,
            fn($r) => $r->id !== $user->id
        );
    }

    public static function fromArray(array $row): self
    {
        return new self(

            // Obligatoires
            _name: self::getString($row, SchemaMysql::ROLE_NAME),

            // Optionnelles (nullable)
            _id: self::getInt($row, SchemaMysql::ROLE_ID),
            _isActive: self::getBoolOrFalse($row, SchemaMysql::ROLE_IS_ACTIVE),

            // Listes ([])
            _users: self::getMappedOrEmpty($row, 'users', [User::class, 'fromArray']),
        );
    }
}
