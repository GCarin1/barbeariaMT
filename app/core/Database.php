<?php
/**
 * Don Barbero - Database Connection (MySQL)
 * 
 * @author Guilherme
 * @created 12/12/2025
 * @version 1.0.0
 */

declare(strict_types=1);

namespace core;

use PDO;
use PDOException;

class Database
{
    private PDO $pdo;

    public function __construct()
    {
        error_log('USANDO MYSQL PDO');
        // Força leitura correta no Windows
        $host = $_ENV['DB_HOST'] ?? '127.0.0.1';
        $port = $_ENV['DB_PORT'] ?? '3306';
        $db = $_ENV['DB_DATABASE'] ?? 'barbearia01';
        $user = $_ENV['DB_USERNAME'] ?? 'root';
        $pass = $_ENV['DB_PASSWORD'] ?? 'GUI@152152asd';

        $dsn = "mysql:host={$host};port={$port};dbname={$db};charset=utf8mb4";

        try {
            $this->pdo = new PDO(
                $dsn,
                $user,
                $pass,
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,

                    // 🔥 PHP 8.5 – correto, sem warning
                    \Pdo\Mysql::ATTR_INIT_COMMAND => "SET NAMES utf8mb4"
                ]
            );
        } catch (PDOException $e) {
            die("Erro ao conectar no MySQL: " . $e->getMessage());
        }
    }

    public function select(string $table, array $params = []): array
    {
        $sql = "SELECT * FROM {$table}";
        $where = [];
        $bindings = [];

        // WHERE (filters)
        if (!empty($params['filters']) && is_array($params['filters'])) {
            foreach ($params['filters'] as $key => $value) {
                $where[] = "{$key} = :{$key}";
                $bindings[":{$key}"] = $value;
            }
        }

        if ($where) {
            $sql .= " WHERE " . implode(" AND ", $where);
        }

        // ORDER
        if (!empty($params['order'])) {
            // Ex: start_at.desc
            [$column, $direction] = explode('.', $params['order']);
            $direction = strtoupper($direction) === 'DESC' ? 'DESC' : 'ASC';
            $sql .= " ORDER BY {$column} {$direction}";
        }

        // LIMIT
        if (!empty($params['limit'])) {
            $sql .= " LIMIT " . (int) $params['limit'];
        }

        // OFFSET
        if (!empty($params['offset'])) {
            $sql .= " OFFSET " . (int) $params['offset'];
        }

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($bindings);

        return $stmt->fetchAll();
    }


    public function insert(string $table, array $data): int
    {
        $columns = implode(',', array_keys($data));
        $placeholders = implode(',', array_map(fn($k) => ":{$k}", array_keys($data)));

        $sql = "INSERT INTO {$table} ({$columns}) VALUES ({$placeholders})";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($data);

        return (int) $this->pdo->lastInsertId();
    }

    public function update(string $table, array $data, array $filters): int
    {
        $set = implode(',', array_map(fn($k) => "{$k} = :{$k}", array_keys($data)));

        $where = [];
        $params = $data;
        foreach ($filters as $key => $value) {
            $where[] = "{$key} = :filter_{$key}";
            $params[":filter_{$key}"] = $value;
        }

        $sql = "UPDATE {$table} SET {$set} WHERE " . implode(' AND ', $where);
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);

        return $stmt->rowCount();
    }

    public function delete(string $table, array $filters): int
    {
        $where = [];
        $params = [];
        foreach ($filters as $key => $value) {
            $where[] = "{$key} = :{$key}";
            $params[":{$key}"] = $value;
        }

        $sql = "DELETE FROM {$table} WHERE " . implode(' AND ', $where);
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);

        return $stmt->rowCount();
    }

    public function findById(string $table, $id, string $idColumn = 'id'): ?array
    {
        $results = $this->select($table, [$idColumn => $id]);
        return $results[0] ?? null;
    }

    public function findOne(string $table, array $filters): ?array
    {
        $results = $this->select($table, $filters);
        return $results[0] ?? null;
    }

    public function getPDO(): PDO
    {
        return $this->pdo;
    }
}
