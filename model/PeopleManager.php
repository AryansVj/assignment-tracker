<?php
class People {
    private $conn;

    public function __construct($db_conn) {
        $this->conn = $db_conn;
    }

    public function addPerson($person_name, $role_id, $group_id) {
        $query = "INSERT INTO People(name, role_id, group_id) VALUES (:person_name, :role_id, :group_id)";

        try {
            $stmt = $this->conn->prepare($query);
            $stmt->execute(['person_name'=>$person_name, 'role_id'=>$role_id, 'group_id'=>$group_id]);
        } catch (PDOException $e) {
            echo 'Exception occured. Error code: ' . $e->getCode(); 
            echo '<br>Error Message: ' . $e->getMessage();
        }
    }

    public function getPersonID($person_name) {
        $query = "SELECT person_id FROM People WHERE name = ?";

        try {
            $stmt = $this->conn->prepare($query);
            $stmt->execute([$person_name]);
        } catch (PDOException $e) {
            echo 'Exception occured. Error code: ' . $e->getCode(); 
            echo '<br>Error Message: ' . $e->getMessage();
        }

        if ($row = $stmt->fetch()) {
            return $row['person_id'] + 0;
        } else {
            return -1;
        }
    }
}