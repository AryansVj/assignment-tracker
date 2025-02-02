<?php
class Segments {
    private $conn;
    private $table;

    public function __construct($db_conn) {
        $this->conn = $db_conn;
        $this->table = "SegmentTracker";
    }

    public function getWeek($date) {
        $query = "SELECT People.name AS person_name, Segments.segment_name, Segments.segment_id, Segments.duration, Meetings.title AS meeting_title, Meetings.meeting_id, PerformanceLevels.levels AS performance, SegmentTracker.id AS segment_track_id
        FROM SegmentTracker 
        JOIN People ON SegmentTracker.person_id = People.person_id JOIN Segments ON SegmentTracker.segment_id = Segments.segment_id JOIN Meetings ON Segments.meeting_id = Meetings.meeting_id JOIN PerformanceLevels ON SegmentTracker.performance_id = PerformanceLevels.performance_id JOIN Weeks ON SegmentTracker.week_id = Weeks.week_id 
        WHERE Weeks.weekly_date = CAST(? AS DATE) ORDER BY Segments.segment_id;";

        try {
            $stmt = $this->conn->prepare($query);
            $stmt->execute([$date]);
        }
        catch (PDOException $e) {
            echo 'Exception occured. Error code: ' . $e->getCode(); 
            echo '<br>Error Message: ' . $e->getMessage();
        }

        return $stmt->fetchAll();
    }

    public function getSegment($segment_track_id) {
        $query = "SELECT People.name AS person_name, Segments.segment_name, Segments.duration, Segments.segment_id, Meetings.title AS meeting_title, Meetings.meeting_id, PerformanceLevels.levels AS performance, Weeks.weekly_date
        FROM SegmentTracker 
        JOIN People ON SegmentTracker.person_id = People.person_id JOIN Segments ON SegmentTracker.segment_id = Segments.segment_id JOIN Meetings ON Segments.meeting_id = Meetings.meeting_id JOIN PerformanceLevels ON SegmentTracker.performance_id = PerformanceLevels.performance_id JOIN Weeks ON SegmentTracker.week_id = Weeks.week_id 
        WHERE SegmentTracker.id = :segment_track_id";

        try {
            $stmt = $this->conn->prepare($query);
            $stmt->execute(['segment_track_id' => $segment_track_id]);
        }
        catch (PDOException $e) {
            echo 'Exception occured. Error code: ' . $e->getCode(); 
            echo '<br>Error Message: ' . $e->getMessage();
        }

        $row = $stmt->fetch();
        return $row;
    }

    public function getBoundByDatePerson($start_date, $end_date, $person_name) {
        $query = "SELECT People.name AS person_name, Segments.segment_name, PerformanceLevels.levels DATE_FORMAT(Weeks.weekly_date, \"%M %d, %Y\") SegmentTracker.id AS segment_track_id
        FROM SegmentTracker 
        JOIN People ON SegmentTracker.person_id = People.person_id JOIN Segments ON SegmentTracker.segment_id = Segments.segment_id JOIN PerformanceLevels ON SegmentTracker.performance_id = PerformanceLevels.performance_id JOIN Weeks ON SegmentTracker.week_id = Weeks.week_id 
        WHERE People.name = :person_name AND Weeks.weekly_date BETWEEN DATE(:start_date) AND DATE(:end_date) ORDER BY Weeks.weekly_date;";

        try {
            $stmt = $this->conn->prepare($query);
            $stmt->execute(['start_date' => $start_date, 'end_date' => $end_date, 'person_name' => $person_name]);
        }
        catch (PDOException $e) {
            echo 'Exception occured. Error code: ' . $e->getCode(); 
            echo '<br>Error Message: ' . $e->getMessage();
        }

        return $stmt->fetchAll();
    }

    public function getByIndividual($person_name) {
        $query = "SELECT Segments.segment_name, PerformanceLevels.levels, DATE_FORMAT(Weeks.weekly_date, \"%M %d, %Y\") AS segment_date, SegmentTracker.id
        FROM SegmentTracker 
        JOIN People ON SegmentTracker.person_id = People.person_id JOIN Segments ON SegmentTracker.segment_id = Segments.segment_id JOIN PerformanceLevels ON SegmentTracker.performance_id = PerformanceLevels.performance_id JOIN Weeks ON SegmentTracker.week_id = Weeks.week_id 
        WHERE Person.name = ? ORDER BY Weeks.weekly_date;";

        try {
            $stmt = $this->conn->prepare($query);
            $stmt->execute([$person_name]);
        }
        catch (PDOException $e) {
            echo 'Exception occured. Error code: ' . $e->getCode(); 
            echo '<br>Error Message: ' . $e->getMessage();
            return -1;
        }

        return $stmt->fetchAll();
    }

    public function addSegment($segment_id, $person_id, $week_id, $performance_id) {
        $query = "INSERT INTO SegmentTracker (segment_id, person_id, week_id, performance_id) VALUES (:segment_id, :person_id, :week_id, :performance_id)";

        try {
            $stmt = $this->conn->prepare($query);
            $stmt->execute(['segment_id' => $segment_id, 'person_id'=> $person_id, 'week_id' => $week_id, 'performance_id' => $performance_id]);
        }
        catch (PDOException $e) {
            echo 'Exception occured. Error code: ' . $e->getCode(); 
            echo '<br>Error Message: ' . $e->getMessage();
            return -1;
        }
        
        return 0;
    
    }
    
    public function deleteSegment($segment_track_id) {
        $query = "DELETE FROM SegmentTracker WHERE id = :segment_track_id";

        try {
            $stmt = $this->conn->prepare($query);
            $stmt->execute(['segment_track_id' => $segment_track_id]);
        }
        catch (PDOException $e) {
            echo 'Exception occured. Error code: ' . $e->getCode(); 
            echo '<br>Error Message: ' . $e->getMessage();
            return -1;
        }
        
        return 0;
    }

    public function updateSegment($segment_track_id, $person_id, $segment_id, $performance_id) {
        $query = "UPDATE SegmentTracker SET person_id = :person_id, segment_id = :segment_id, performance_id = :performance_id WHERE id = :segment_track_id";

        try {
            $stmt = $this->conn->prepare($query);
            $stmt->execute(['person_id'=> $person_id, 'segment_id' => $segment_id, 'performance_id' => $performance_id, 'segment_track_id' => $segment_track_id]);
        }
        catch (PDOException $e) {
            echo 'Exception occured. Error code: ' . $e->getCode(); 
            echo '<br>Error Message: ' . $e->getMessage();
            return -1;
        }
        
        return 0;
    }

}