<?php

abstract class Model
{
    protected PDO $db;
    protected string $table;
    protected string $primaryKey = 'id';

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    /**
     * Busca todos os registros da tabela.
     */
    public function all(string $orderBy = 'id', string $direction = 'DESC'): array
    {
        $direction = strtoupper($direction) === 'ASC' ? 'ASC' : 'DESC';
        $stmt = $this->db->query("SELECT * FROM {$this->table} ORDER BY {$orderBy} {$direction}");
        return $stmt->fetchAll();
    }

    /**
     * Busca um registro pelo ID.
     */
    public function find(int $id): array|false
    {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE {$this->primaryKey} = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    /**
     * Busca registros por condição.
     */
    public function where(string $column, mixed $value, string $operator = '='): array
    {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE {$column} {$operator} ?");
        $stmt->execute([$value]);
        return $stmt->fetchAll();
    }

    /**
     * Busca um único registro por condição.
     */
    public function findBy(string $column, mixed $value): array|false
    {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE {$column} = ? LIMIT 1");
        $stmt->execute([$value]);
        return $stmt->fetch();
    }

    /**
     * Insere um novo registro.
     */
    public function create(array $data): int
    {
        $data = $this->addTimestamps($data, 'create');
        $columns = implode(', ', array_keys($data));
        $placeholders = implode(', ', array_fill(0, count($data), '?'));

        $stmt = $this->db->prepare("INSERT INTO {$this->table} ({$columns}) VALUES ({$placeholders})");
        $stmt->execute(array_values($data));

        return (int) $this->db->lastInsertId();
    }

    /**
     * Atualiza um registro pelo ID.
     */
    public function update(int $id, array $data): bool
    {
        $data = $this->addTimestamps($data, 'update');
        $sets = implode(', ', array_map(fn($col) => "{$col} = ?", array_keys($data)));

        $stmt = $this->db->prepare("UPDATE {$this->table} SET {$sets} WHERE {$this->primaryKey} = ?");
        return $stmt->execute([...array_values($data), $id]);
    }

    /**
     * Deleta um registro pelo ID.
     */
    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare("DELETE FROM {$this->table} WHERE {$this->primaryKey} = ?");
        return $stmt->execute([$id]);
    }

    /**
     * Conta registros (com filtro opcional).
     */
    public function count(string $column = '*', mixed $value = null): int
    {
        if ($value !== null) {
            $stmt = $this->db->prepare("SELECT COUNT(*) FROM {$this->table} WHERE {$column} = ?");
            $stmt->execute([$value]);
        } else {
            $stmt = $this->db->query("SELECT COUNT(*) FROM {$this->table}");
        }
        return (int) $stmt->fetchColumn();
    }

    /**
     * Executa query customizada e retorna todos os resultados.
     */
    public function query(string $sql, array $params = []): array
    {
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    /**
     * Executa query customizada e retorna um único resultado.
     */
    public function queryOne(string $sql, array $params = []): array|false
    {
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetch();
    }

    /**
     * Adiciona created_at e updated_at automaticamente.
     */
    private function addTimestamps(array $data, string $type): array
    {
        $now = date('Y-m-d H:i:s');
        if ($type === 'create') {
            $data['created_at'] = $now;
        }
        $data['updated_at'] = $now;
        return $data;
    }
}
