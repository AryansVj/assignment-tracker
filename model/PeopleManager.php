<?php
class People {
    private $conn;

    public function __construct($db_conn) {
        $this->conn = $db_conn;
    }

    public function addPerson($person_name, $role_id, $group_id, $responsibility_id) {
        $query = "INSERT INTO People(name, role_id, group_id, responsibility_id) VALUES (:person_name, :role_id, :group_id, :responsibility_id)";

        try {
            $stmt = $this->conn->prepare($query);
            $stmt->execute(['person_name' => $person_name, 'role_id' => $role_id, 'group_id' => $group_id, 'responsibility_id' => $responsibility_id]);
        } catch (PDOException $e) {
            echo 'Exception occured. Error code: ' . $e->getCode(); 
            echo '<br>Error Message: ' . $e->getMessage();

            return -1;
        }

        return 0;
    }

    public function deletePerson($person_id) {
        $query = "DELETE FROM People WHERE person_id = :person_id";

        try {
            $stmt = $this->conn->prepare($query);
            $stmt->execute(['person_id' => $person_id]);
        } catch (PDOException $e) {
            echo 'Exception occured. Error code: ' . $e->getCode(); 
            echo '<br>Error Message: ' . $e->getMessage();

            return -1;
        }

        return 0;
    }

    public function editPerson($person_id, $person_name, $role_id, $group_id, $responsibility_id) {
        $query = "UPDATE People SET name=:person_name, role_id = :role_id, group_id = :group_id, responsibility_id = :responsibility_id WHERE person_id = :person_id";

        try {
            $stmt = $this->conn->prepare($query);
            $stmt->execute(['person_name' => $person_name, 'role_id' => $role_id, 'group_id' => $group_id, 'responsibility_id' => $responsibility_id, 'person_id' => $person_id]);
        } catch (PDOException $e) {
            echo 'Exception occured. Error code: ' . $e->getCode(); 
            echo '<br>Error Message: ' . $e->getMessage();

            return -1;
        }

        return 0;
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

    public function getPersonInfo($person_name) {
        $query = "SELECT People.name, People.person_id, Roles.role_title, Groups.group_name, Responsibilities.responsibility FROM People JOIN Roles ON People.role_id = Roles.role_id JOIN Groups ON People.group_id = Groups.group_id JOIN Responsibilities ON People.responsibility_id = Responsibilities.id WHERE People.name = ?";

        try {
            $stmt = $this->conn->prepare($query);
            $stmt->execute([$person_name]);
        } catch (PDOException $e) {
            echo 'Exception occured. Error code: ' . $e->getCode(); 
            echo '<br>Error Message: ' . $e->getMessage();

            return -1;
        }

        return $stmt->fetch();
    }

    public function getAssignmentCount($person_name) {
        // Get Person wise count of total assignments
        $query = "SELECT COUNT(assignment_id) AS assignment_count FROM WeeklyTracker JOIN People ON WeeklyTracker.person_id = People.person_id WHERE People.name = ?";

        try {
            $stmt = $this->conn->prepare($query);
            $stmt->execute([$person_name]);
        } catch (PDOException $e) {
            echo 'Exception occured. Error code: ' . $e->getCode(); 
            echo '<br>Error Message: ' . $e->getMessage();

            return -1;
        }

        $res = $stmt->fetch();
        return $res['assignment_count'];
    }

    public function getSegmentCount($person_name) {
        // Get Person wise count of total assignments
        $query = "SELECT COUNT(id) AS segment_count FROM SegmentTracker JOIN People ON SegmentTracker.person_id = People.person_id WHERE People.name = ?";

        try {
            $stmt = $this->conn->prepare($query);
            $stmt->execute([$person_name]);
        } catch (PDOException $e) {
            echo 'Exception occured. Error code: ' . $e->getCode(); 
            echo '<br>Error Message: ' . $e->getMessage();

            return -1;
        }

        $res = $stmt->fetch();
        return $res['segment_count'];
    }

    public function searchPeople($person_name = "", $role = NULL, $group = NULL, $responsibility = NULL) {
        $query = "SELECT People.person_id, People.name, Roles.role_title, Groups.group_name, Responsibilities.responsibility
                FROM People JOIN Roles ON People.role_id = Roles.role_id JOIN Groups ON People.group_id = Groups.group_id JOIN Responsibilities ON People.responsibility_id = Responsibilities.id 
                WHERE People.name LIKE :person_name";
        
        $params = array('person_name' => "%" . $person_name . "%");

        if ($role != NULL) {
            $query .= " AND People.role_id = :role";
            $params['role'] = $role;
        }
        if ($group != NULL) {
            $query .= " AND People.group_id = :group";
            $params['group'] = $group;
        }
        if ($responsibility != NULL) {
            $query .= " AND People.responsibility_id = :responsibility";
            $params['responsibility'] = $responsibility;
        }

        $query .= " ORDER BY People.name";

        try {
            $stmt = $this->conn->prepare($query);
            $stmt->execute($params);
        } catch (PDOException $e) {
            echo 'Exception occured. Error code: ' . $e->getCode(); 
            echo '<br>Error Message: ' . $e->getMessage();

            return -1;
        }

        return $stmt->fetchAll();
    }
}