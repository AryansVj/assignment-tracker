<?php

class Database {
    private $host = 'localhost';
    private $port = 3306;
    private $db_name = 'AssignmentTracker';

    private $username = 'root';
    private $password = 'root';

    public $pdo = null;
    
    public function createDB() {
        $dsn = 'mysql:host=' . $this->host .';port=' . $this->port .';dbname=' . $this->db_name;
        $this->pdo = new PDO($dsn, $this->username, $this->password);
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $this->pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    }
};