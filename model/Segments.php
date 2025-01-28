<?php
class Segments {
    private $conn;
    private $table;

    public function __construct($db_conn) {
        $this->conn = $db_conn;
        $this->table = "SegmentTracker";
    }

    public function getWeek($date) {
        $query = "SELECT People.name AS person_name, Segments.segment_name, Segments.segment_id, Segments.duration, Meetings.title AS meeting_title, Meetings.meeting_id, PerformanceLevels.levels AS performance
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
}