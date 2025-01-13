<?php
class Weeks {
    private $conn;

    public function __construct($db_conn) {
        $this->conn = $db_conn;
    }

    public function getWeekID($weekly_date) {
        $query = "SELECT week_id FROM Weeks WHERE weekly_date = :weekly_date";
        
        try {
            $stmt = $this->conn->prepare($query);
            $stmt->execute(['weekly_date' => $weekly_date]);
        }
        catch (PDOException $e) {
            echo 'Exception occured. Error code: ' . $e->getCode(); 
            echo '<br>Error Message: ' . $e->getMessage();
        }

        if ( $row = $stmt->fetch() ) {
            return $row['week_id'] + 0;
        } else {
            return -1;
        }
    }

}