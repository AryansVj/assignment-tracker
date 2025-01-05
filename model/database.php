<?php

class Database {
    private $host = 'localhost';
    private $port = 3306;
    private $db_name = 'AssignmentTracker';

    private $username = 'root';
    private $password = 'root';

    private $pdo_query = "mysql:host=$this->host;port=$this->port;dbname=$this->dbname";;

    public $pdo = null;

    public function createDB() {
        $this->pdo = new PDO($this->pdo_query, $this->username, $this->password);
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }
};