<?php
class Model {
    protected $pdo;

    public function __construct() {

        $host    = 'localhost';
        $dbname  = 'mini_erp';
        $user    = 'root';
        $pass    = 'Mysql@2024';
        $charset = 'utf8mb4';

        $dsn = "mysql:host=$host;dbname=$dbname;charset=$charset";

        try {
            $this->pdo = new PDO($dsn, $user, $pass, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            ]);
        } catch (PDOException $e) {
            die('Erro ao conectar ao banco de dados: ' . $e->getMessage());
        }
    }

    public function query($sql, $params = []) {
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }

    public function all($table) {
        $stmt = $this->pdo->query("SELECT * FROM {$table}");
        return $stmt->fetchAll();
    }

    public function find($table, $id) {
        $stmt = $this->pdo->prepare("SELECT * FROM {$table} WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function insert($table, $data) {
        $columns = implode(', ', array_keys($data));
        $placeholders = implode(', ', array_fill(0, count($data), '?'));

        $stmt = $this->pdo->prepare("INSERT INTO {$table} ({$columns}) VALUES ({$placeholders})");
        return $stmt->execute(array_values($data));
    }

    public function update($id, $data) {
        $table = strtolower(get_class($this)) . 's';

        $setClause = implode(', ', array_map(fn($k) => "$k = ?", array_keys($data)));
        $stmt = $this->pdo->prepare("UPDATE {$table} SET {$setClause} WHERE id = ?");

        $params = array_values($data);
        $params[] = $id;

        return $stmt->execute($params);
    }

    public function delete($table, $id) {
        $stmt = $this->pdo->prepare("DELETE FROM {$table} WHERE id = ?");
        return $stmt->execute([$id]);
    }
}
