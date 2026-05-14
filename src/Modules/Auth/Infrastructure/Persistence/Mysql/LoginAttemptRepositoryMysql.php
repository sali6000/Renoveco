<?php

namespace Src\Modules\Auth\Infrastructure\Persistence\Mysql;

use Core\Database\QueryBuilderInterface;
use Src\Database\SchemaMysql;
use Core\Database\RepositoryMysql;
use Src\Modules\Auth\Domain\Repository\LoginAttemptRepositoryInterface;

class LoginAttemptRepositoryMysql extends RepositoryMysql implements LoginAttemptRepositoryInterface
{
    public function __construct(\PDO $pdo, private QueryBuilderInterface $queryBuilder)
    {
        parent::__construct($pdo); // CRUD auto sur base du model
    }

    public function countRecent(string $ip, int $minutes): int
    {
        $sql = "SELECT COUNT(*) AS attempt_count 
            FROM user_login_attempts 
            WHERE ip_address = :ip 
            AND success = 0 
            AND attempted_at >= DATE_SUB(NOW(), INTERVAL :minutes MINUTE)";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['ip' => $ip, 'minutes' => $minutes]);

        return (int) ($stmt->fetch(\PDO::FETCH_ASSOC)['attempt_count'] ?? 0);
    }

    public function record(string $ip, string $email, ?int $userId, bool $success): void
    {
        $this->queryBuilder->insert(SchemaMysql::TABLE_LOGIN_ATTEMPTS, [
            SchemaMysql::LOGIN_ATTEMPT_IP      => $ip,
            SchemaMysql::LOGIN_ATTEMPT_EMAIL   => $email,
            SchemaMysql::LOGIN_ATTEMPT_USER_ID => $userId,
            SchemaMysql::LOGIN_ATTEMPT_SUCCESS => (int) $success,
        ]);
    }
}
