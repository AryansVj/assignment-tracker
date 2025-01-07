<?php
class Assignment {
    private $conn;
    private $table;

    public function __construct($db_conn, $table_name) {
        $this->conn = $db_conn;
        $this->table = $table_name;
    }

    public function getAll() {
        $query = "SELECT * FROM " . $this->table;
        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getWeek($date) {
        $query = "SELECT Person.name AS person_name, AssignmentCategories.category_title, Assistant.name AS assistant_name, Status.status_descriptor, PerformanceLevels.levels, WeeklyTracker.hall 
        FROM WeeklyTracker 
        JOIN People AS Person ON WeeklyTracker.person_id = Person.person_id JOIN AssignmentCategories ON WeeklyTracker.category_id = AssignmentCategories.category_id JOIN People AS Assistant ON WeeklyTracker.assistant_id = Assistant.person_id JOIN Status ON WeeklyTracker.status_id = Status.status_id JOIN PerformanceLevels ON WeeklyTracker.performace_id = PerformanceLevels.performace_id JOIN Weeks ON WeeklyTracker.week_id = Weeks.week_id 
        WHERE Weeks.weekly_date = CAST(? AS DATE) ORDER BY Person.name;";

        $stmt = $this->conn->prepare($query);
        $stmt->execute([$date]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getMonth($year, $month) {
        $query = "SELECT Person.name AS person_name, AssignmentCategories.category_title, Assistant.name AS assistant_name, Status.status_descriptor, PerformanceLevels.levels, WeeklyTracker.hall, Weeks.weekly_date
        FROM WeeklyTracker 
        JOIN People AS Person ON WeeklyTracker.person_id = Person.person_id JOIN AssignmentCategories ON WeeklyTracker.category_id = AssignmentCategories.category_id JOIN People AS Assistant ON WeeklyTracker.assistant_id = Assistant.person_id JOIN Status ON WeeklyTracker.status_id = Status.status_id JOIN PerformanceLevels ON WeeklyTracker.performace_id = PerformanceLevels.performace_id JOIN Weeks ON WeeklyTracker.week_id = Weeks.week_id 
        WHERE YEAR(Weeks.weekly_date) = ? AND MONTH(Weeks.weekly_date) = ? ORDER BY Weeks.weekly_date;";

        try {
            $stmt = $this->conn->prepare($query);
            $stmt->execute([$year, $month]);
        }
        catch (PDOException $e) {
            echo 'Exception occured. Error code: ' . $e->getCode(); 
            echo '<br>Error Message: ' . $e->getMessage();
        }

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getIndividual($person_name) {
        $query = "SELECT AssignmentCategories.category_title, Assistant.name AS assistant_name, Status.status_descriptor, PerformanceLevels.levels, WeeklyTracker.hall, Weeks.weekly_date
        FROM WeeklyTracker 
        JOIN People AS Person ON WeeklyTracker.person_id = Person.person_id JOIN AssignmentCategories ON WeeklyTracker.category_id = AssignmentCategories.category_id JOIN People AS Assistant ON WeeklyTracker.assistant_id = Assistant.person_id JOIN Status ON WeeklyTracker.status_id = Status.status_id JOIN PerformanceLevels ON WeeklyTracker.performace_id = PerformanceLevels.performace_id JOIN Weeks ON WeeklyTracker.week_id = Weeks.week_id 
        WHERE Person.name = ? ORDER BY Weeks.weekly_date;";

        $stmt = $this->conn->prepare($query);
        $stmt->execute([$person_name]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getAssignment($person_name, $date) {
        $query = "SELECT Person.name AS person_name, AssignmentCategories.category_title, Assistant.name AS assistant_name, Status.status_descriptor, PerformanceLevels.levels, WeeklyTracker.hall, Weeks.weekly_date
        FROM WeeklyTracker 
        JOIN People AS Person ON WeeklyTracker.person_id = Person.person_id JOIN AssignmentCategories ON WeeklyTracker.category_id = AssignmentCategories.category_id JOIN People AS Assistant ON WeeklyTracker.assistant_id = Assistant.person_id JOIN Status ON WeeklyTracker.status_id = Status.status_id JOIN PerformanceLevels ON WeeklyTracker.performace_id = PerformanceLevels.performace_id JOIN Weeks ON WeeklyTracker.week_id = Weeks.week_id 
        WHERE Person.name = :person_name AND Weeks.weekly_date = :date";

        $stmt = $this->conn->prepare($query);
        $stmt->execute(['person_name' => $person_name, 'date' => $date]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function addAssignment($person_id, $category_id, $assistant_id, $week_id, $status_id, $performace_id, $hall) {
        $query = "INSERT INTO WeeklyTracker (person_id, category_id, assistant_id, week_id, status_id, performace_id, hall) VALUES (:person_id, :category_id, :assistant_id, :week_id, :status_id, :performace_id, :hall)";

        $stmt = $this->conn->prepare($query);
        $stmt->execute(['person_id'=> $person_id, 'category_id' => $category_id, 'assistant_id' => $assistant_id, 'week_id' => $week_id, 'status_id' => $status_id, 'performace_id' => $performace_id, 'hall' => $hall]);
        
        return 0;
    }
};