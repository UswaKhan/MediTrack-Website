<?php
class Database {
    private $conn;
    private static $instance = null;

    private function __construct() {
        $this->conn = new mysqli("localhost", "root", "", "medicine_inventory");
        
        if ($this->conn->connect_error) {
            die("Connection failed: " . $this->conn->connect_error);
        }
        
        // Set charset to prevent character encoding issues
        $this->conn->set_charset("utf8mb4");
    }

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function getConnection() {
        return $this->conn;
    }

    // Safe query execution with prepared statements
    public function query($sql, $types = "", $params = []) {
        $stmt = $this->conn->prepare($sql);
        
        if ($stmt === false) {
            throw new Exception("Query preparation failed: " . $this->conn->error);
        }

        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }

        $stmt->execute();
        return $stmt;
    }

    // Safe select query
    public function select($sql, $types = "", $params = []) {
        $stmt = $this->query($sql, $types, $params);
        $result = $stmt->get_result();
        $data = [];
        
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }
        
        $stmt->close();
        return $data;
    }

    // Safe insert query
    public function insert($table, $data) {
        $columns = implode(", ", array_keys($data));
        $values = implode(", ", array_fill(0, count($data), "?"));
        $types = str_repeat("s", count($data));
        
        $sql = "INSERT INTO $table ($columns) VALUES ($values)";
        $stmt = $this->query($sql, $types, array_values($data));
        
        $insertId = $this->conn->insert_id;
        $stmt->close();
        return $insertId;
    }

    // Safe update query
    public function update($table, $data, $where, $whereTypes = "", $whereParams = []) {
        $set = implode(" = ?, ", array_keys($data)) . " = ?";
        $types = str_repeat("s", count($data)) . $whereTypes;
        $params = array_merge(array_values($data), $whereParams);
        
        $sql = "UPDATE $table SET $set WHERE $where";
        $stmt = $this->query($sql, $types, $params);
        
        $affectedRows = $stmt->affected_rows;
        $stmt->close();
        return $affectedRows;
    }

    // Input sanitization
    public function sanitize($input) {
        if (is_array($input)) {
            return array_map([$this, 'sanitize'], $input);
        }
        return $this->conn->real_escape_string(trim($input));
    }

    // Validate table name
    public function validateTableName($table) {
        // Only allow alphanumeric characters and underscores
        if (!preg_match('/^[a-zA-Z0-9_]+$/', $table)) {
            throw new Exception("Invalid table name");
        }
        return $table;
    }

    // Validate column name
    public function validateColumnName($column) {
        // Only allow alphanumeric characters and underscores
        if (!preg_match('/^[a-zA-Z0-9_]+$/', $column)) {
            throw new Exception("Invalid column name");
        }
        return $column;
    }

    public function __destruct() {
        if ($this->conn) {
            $this->conn->close();
        }
    }
}
?> 