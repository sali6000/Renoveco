<?php

namespace Src\Modules\Shared\Infrastructure\Persistence\Mysql;

use Core\Database\QueryBuilderInterface;
use Core\Database\RepositoryMysql;
use Override;
use Src\Database\SchemaMysql;
use Src\Modules\Shared\Domain\Repository\RateLimitRepositoryInterface;

class RateLimitRepositoryMysql  extends RepositoryMysql implements RateLimitRepositoryInterface
{
    #[Override]
    public function getTable(): string
    {
        return SchemaMysql::TABLE_RATE_LIMIT_ATTEMPT;
    }

    #[Override]
    public function fromArray(array $row): object
    {
        throw new \Exception('Not implemented');
    }

    public function __construct(\PDO $pdo, QueryBuilderInterface $qb)
    {
        parent::__construct($pdo, $qb);
    }

    private function getClientIp(): string
    {
        $ip = $_SERVER['HTTP_X_FORWARDED_FOR']
            ?? $_SERVER['HTTP_X_REAL_IP']
            ?? $_SERVER['REMOTE_ADDR']
            ?? 'unknown';

        return trim(explode(',', $ip)[0]);
    }

    /**
     * {@inheritDoc}
     */
    public function countRecent(string $type, int $minutes): int
    {
        $sql = "SELECT COUNT(*) AS attempt_count 
                FROM rate_limit_attempt
                WHERE type = :type
                AND ip_address = :ip 
                AND attempted_at >= DATE_SUB(NOW(), INTERVAL :minutes MINUTE)";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['type' => $type, 'ip' => $this->getClientIp(), 'minutes' => $minutes]);

        return (int) ($stmt->fetch(\PDO::FETCH_ASSOC)['attempt_count'] ?? 0);
    }

    public function record(string $type, ?string $identifier): void
    {
        $sql = "INSERT INTO rate_limit_attempt (type, ip_address, identifier) 
                VALUES (:type, :ip, :identifier)";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['type' => $type, 'ip' => $this->getClientIp(), 'identifier' => $identifier]);
    }
}
