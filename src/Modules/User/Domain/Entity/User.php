<?php

namespace Src\Modules\User\Domain\Entity;

use Core\Database\BaseModel;
use DateTime;
use Src\Modules\Shared\Infrastructure\Schema\HelperSchemaMysql;
use Src\Modules\User\Infrastructure\Schema\RoleSchemaMysql;
use Src\Modules\User\Infrastructure\Schema\UserSchemaMysql;

class User extends BaseModel
{
    public function __construct(

        // Obligatoires
        private string $_email,
        private bool $_isActive = true,

        // Optionnels
        private ?int $_id = null,
        private ?string $_passwordHashed = null,
        private ?DateTime $_createdAt = null,
        private ?DateTime $_lastLoginAt = null,
        private ?DateTime $_emailVerifiedAt = null,
        private ?DateTime $_deletedAt = null,

        // Listes
        /** @var Role[] */
        private array $_roles = [],

    ) {}

    public string $email {
        get => $this->_email;
        set(string $value) => $this->_email = $value;
    }

    public bool $isActive {
        get => $this->_isActive;
        set(bool $value) => $this->_isActive = $value;
    }

    public ?int $id {
        get => $this->_id;
        set(?int $value) => $this->_id = $value;
    }

    public ?string $passwordHashed {
        get => $this->_passwordHashed;
        set(?string $value) => $this->_passwordHashed = $value;
    }

    public function hashAndSetPassword(string $plainPassword): void
    {
        $this->_passwordHashed = password_hash($plainPassword, PASSWORD_DEFAULT);
    }

    public ?DateTime $createdAt {
        get => $this->_createdAt;
        set(?DateTime $value) => $this->_createdAt = $value;
    }

    public ?DateTime $lastLoginAt {
        get => $this->_lastLoginAt;
        set(?DateTime $value) => $this->_lastLoginAt = $value;
    }

    public ?DateTime $emailVerifiedAt {
        get => $this->_emailVerifiedAt;
        set(?DateTime $value) => $this->_emailVerifiedAt = $value;
    }

    public ?DateTime $deletedAt {
        get => $this->_deletedAt;
        set(?DateTime $value) => $this->_deletedAt = $value;
    }

    /** @var Role[] */
    public array $roles {
        get => $this->_roles;
        set(array $value) => $this->_roles = $value;
    }

    public function addRole(Role $role): void
    {
        $this->_roles[] = $role;
    }

    public static function fromArray(array $row): self
    {
        return new self(

            // Obligatoires
            _email: self::getString($row, UserSchemaMysql::EMAIL),
            _isActive: self::getBoolOrFalse($row, UserSchemaMysql::IS_ACTIVE),

            // Optionnelles (nullable)
            _id: self::getIntOrNull($row, UserSchemaMysql::ID),
            _passwordHashed: self::getStringOrNull($row, UserSchemaMysql::PASSWORD_HASH),
            _createdAt: self::getDateOrNull($row, UserSchemaMysql::CREATED_AT),
            _lastLoginAt: self::getDateOrNull($row, UserSchemaMysql::LAST_LOGIN_AT),
            _emailVerifiedAt: self::getDateOrNull($row, UserSchemaMysql::EMAIL_VERIFIED_AT),
            _deletedAt: self::getDateOrNull($row, UserSchemaMysql::DELETED_AT),

            // Listes ([])
            _roles: self::getMappedOrEmpty($row, HelperSchemaMysql::fieldTable(RoleSchemaMysql::TABLE), [Role::class, 'fromArray']),
        );
    }
}
