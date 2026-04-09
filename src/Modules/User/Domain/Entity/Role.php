<?php

namespace Src\Modules\User\Domain\Entity;

class Role
{
    private string $_name;
    private ?int $_id = null;
    private ?bool $_isActive = null;
    /** @var User[] */
    private array $_users = [];



    public function __construct(
        string $name
    ) {
        $this->name = $name;
    }

    public string $name {
        get => $this->_name;
        set(string $value) {
            $this->_name = $value;
        }
    }

    public ?int $id {
        get => $this->_id;
        set(?int $value) {
            $this->_id = $value;
        }
    }

    public ?bool $isActive {
        get => $this->_isActive;
        set(?bool $value) {
            $this->_isActive = $value;
        }
    }

    public function addUser(User $user): void
    {
        foreach ($this->_users as $r) {
            if ($r->id === $user->id) return; // éviter doublon
        }
        $this->_users[] = $user;
    }

    public function getUsers(): array
    {
        return $this->_users;
    }

    public function removeUser(User $user): void
    {
        $this->_users = array_filter(
            $this->_users,
            fn($r) => $r->id !== $user->id
        );
    }
}
