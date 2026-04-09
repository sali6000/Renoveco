<?php

namespace Src\Modules\User\Domain\Entity;

use DateTime;

class User
{
    private ?int $_id = null;
    private string $_email;
    private ?string $_passwordHashed = null;
    private ?DateTime $_createdAt = null;
    private ?DateTime $_lastLoginAt = null;
    private ?DateTime $_emailVerifiedAt = null;
    private ?DateTime $_deletedAt = null;
    private bool $_isActive = true;
    /** @var Role[] */
    private array $_roles = [];

    public function __construct(
        string $email
    ) {
        $this->email = $email;
    }

    public string $email {
        get => $this->_email;
        set(string $value) {
            $this->_email = $value;
        }
    }

    public ?int $id {
        get => $this->_id;
        set(?int $value) {
            $this->_id = $value;
        }
    }

    public ?string $passwordHashed {
        get => $this->_passwordHashed;
        set(?string $value) {
            $this->_passwordHashed = $value;
        }
    }

    public function hashAndSetPassword(string $plainPassword): void
    {
        $this->_passwordHashed = password_hash($plainPassword, PASSWORD_DEFAULT);
    }


    public ?DateTime $createdAt {
        get => $this->_createdAt;
        set(?DateTime $value) {
            $this->_createdAt = $value;
        }
    }

    public ?DateTime $lastLoginAt {
        get => $this->_lastLoginAt;
        set(?DateTime $value) {
            $this->_lastLoginAt = $value;
        }
    }

    public ?DateTime $emailVerifiedAt {
        get => $this->_emailVerifiedAt;
        set(?DateTime $value) {
            $this->_emailVerifiedAt = $value;
        }
    }

    public ?DateTime $deletedAt {
        get => $this->_deletedAt;
        set(?DateTime $value) {
            $this->_deletedAt = $value;
        }
    }

    public bool $isActive {
        get => $this->_isActive;
        set(bool $value) {
            $this->_isActive = $value;
        }
    }

    public function addRole(Role $role): void
    {
        foreach ($this->_roles as $r) {
            if ($r->id === $role->id) return; // éviter doublon
        }
        $this->_roles[] = $role;
    }

    /**
     * @return Role[]
     */
    public function getRoles(): array
    {
        return $this->_roles;
    }

    public function removeRole(Role $role): void
    {
        $this->_roles = array_filter(
            $this->_roles,
            fn($r) => $r->id !== $role->id
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'email' => $this->email,
            'role' => $this->getRoles()[0]
        ];
    }
}
