<?php
/**
 * Base Model Class
 */

class Model
{
    protected Database $db;
    protected string $table;
    protected string $primaryKey = 'id';
    protected array $fillable = [];

    public function __construct()
    {
        $this->db = new Database();
    }

    /**
     * Find by ID
     */
    public function find(int $id): ?array
    {
        return $this->db->fetch(
            "SELECT * FROM {$this->table} WHERE {$this->primaryKey} = ?",
            [$id]
        );
    }

    /**
     * Find by column
     */
    public function findBy(string $column, $value): ?array
    {
        return $this->db->fetch(
            "SELECT * FROM {$this->table} WHERE {$column} = ?",
            [$value]
        );
    }

    /**
     * Get all records
     */
    public function all(string $orderBy = 'id', string $direction = 'DESC'): array
    {
        return $this->db->fetchAll(
            "SELECT * FROM {$this->table} ORDER BY {$orderBy} {$direction}"
        );
    }

    /**
     * Get records with conditions
     */
    public function where(string $column, $value, string $operator = '='): array
    {
        return $this->db->fetchAll(
            "SELECT * FROM {$this->table} WHERE {$column} {$operator} ?",
            [$value]
        );
    }

    /**
     * Create new record
     */
    public function create(array $data): int
    {
        $filtered = $this->filterFillable($data);
        $filtered['created_at'] = date('Y-m-d H:i:s');
        $filtered['updated_at'] = date('Y-m-d H:i:s');

        return $this->db->insert($this->table, $filtered);
    }

    /**
     * Update record
     */
    public function update(int $id, array $data): bool
    {
        $filtered = $this->filterFillable($data);
        $filtered['updated_at'] = date('Y-m-d H:i:s');

        return $this->db->update(
            $this->table,
            $filtered,
            "{$this->primaryKey} = ?",
            [$id]
        ) > 0;
    }

    /**
     * Delete record
     */
    public function delete(int $id): bool
    {
        return $this->db->delete(
            $this->table,
            "{$this->primaryKey} = ?",
            [$id]
        ) > 0;
    }

    /**
     * Count records
     */
    public function count(string $where = '1', array $params = []): int
    {
        return $this->db->count($this->table, $where, $params);
    }

    /**
     * Paginate records
     */
    public function paginate(int $page = 1, int $perPage = 10, string $where = '1', array $params = [], string $orderBy = 'id DESC'): array
    {
        $offset = ($page - 1) * $perPage;
        $total = $this->count($where, $params);
        $totalPages = ceil($total / $perPage);

        $records = $this->db->fetchAll(
            "SELECT * FROM {$this->table} WHERE {$where} ORDER BY {$orderBy} LIMIT {$perPage} OFFSET {$offset}",
            $params
        );

        return [
            'data' => $records,
            'current_page' => $page,
            'per_page' => $perPage,
            'total' => $total,
            'total_pages' => $totalPages,
            'has_more' => $page < $totalPages
        ];
    }

    /**
     * Filter data by fillable fields
     */
    protected function filterFillable(array $data): array
    {
        if (empty($this->fillable)) {
            return $data;
        }
        return array_intersect_key($data, array_flip($this->fillable));
    }

    /**
     * Execute raw query
     */
    protected function query(string $sql, array $params = []): array
    {
        return $this->db->fetchAll($sql, $params);
    }

    /**
     * Execute raw query and get single result
     */
    protected function queryOne(string $sql, array $params = []): ?array
    {
        return $this->db->fetch($sql, $params);
    }
}
