<?php
class Weeks {
    private $conn;

    public function __construct($db_conn) {
        $this->conn = $db_conn;
    }

    public function addWeek($weekly_date, $count, $notes) {
        $query = "INSERT IGNORE INTO Weeks(weekly_date, weekly_count, special_notes) values (:weekly_date, :weekly_count, :special_notes)";
        
        try {
            $stmt = $this->conn->prepare($query);
            $stmt->execute(['weekly_date' => $weekly_date, 'weekly_count' => $count, 'special_notes' => $notes]);
        }
        catch (PDOException $e) {
            echo 'Exception occured. Error code: ' . $e->getCode(); 
            echo '<br>Error Message: ' . $e->getMessage();
        }
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

    public function updateCount($week_id, $count) {
        $query = "UPDATE Weeks SET weekly_count = :count WHERE week_id = :week_id";

        try {
            $stmt = $this->conn->prepare($query);
            $stmt->execute(['count' => $count, 'week_id' => $week_id]);
        }
        catch (PDOException $e) {
            echo 'Exception occured. Error code: ' . $e->getCode(); 
            echo '<br>Error Message: ' . $e->getMessage();
        }
    }

    public function getCount($week_id) {
        $query = "SELECT weekly_count FROM Weeks WHERE week_id = :week_id";

        try {
            $stmt = $this->conn->prepare($query);
            $stmt->execute(['week_id' => $week_id]);
        }
        catch (PDOException $e) {
            echo 'Exception occured. Error code: ' . $e->getCode(); 
            echo '<br>Error Message: ' . $e->getMessage();
        }

        if ( $row = $stmt->fetch() ) {
            return $row['weekly_count'] + 0;
        } else {
            return -1;
        }
    }

}