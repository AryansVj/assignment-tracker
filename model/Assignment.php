<?php
class Assignment {
    private $conn;
    private $table;

    public function __construct($db_conn) {
        $this->conn = $db_conn;
        $this->table = "WeeklyTracker";
    }

    public function getAll() {
        $query = "SELECT * FROM " . $this->table;
        try {
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
        }
        catch (PDOException $e) {
            echo 'Exception occured. Error code: ' . $e->getCode(); 
            echo '<br>Error Message: ' . $e->getMessage();
        }

        return $stmt->fetchAll();
    }

    public function getWeek($date) {
        $query = "SELECT Person.name AS person_name, AssignmentCategories.category_title, Assistant.name AS assistant_name, Status.status_descriptor, PerformanceLevels.levels, WeeklyTracker.hall, WeeklyTracker.assignment_id
        FROM WeeklyTracker 
        JOIN People AS Person ON WeeklyTracker.person_id = Person.person_id JOIN AssignmentCategories ON WeeklyTracker.category_id = AssignmentCategories.category_id JOIN People AS Assistant ON WeeklyTracker.assistant_id = Assistant.person_id JOIN Status ON WeeklyTracker.status_id = Status.status_id JOIN PerformanceLevels ON WeeklyTracker.performance_id = PerformanceLevels.performance_id JOIN Weeks ON WeeklyTracker.week_id = Weeks.week_id 
        WHERE Weeks.weekly_date = CAST(? AS DATE) ORDER BY Person.name;";

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

    public function getMonth($year, $month) {
        $query = "SELECT Person.name AS person_name, AssignmentCategories.category_title, Assistant.name AS assistant_name, Status.status_descriptor, PerformanceLevels.levels, WeeklyTracker.hall, Weeks.weekly_date, WeeklyTracker.assignment_id
        FROM WeeklyTracker 
        JOIN People AS Person ON WeeklyTracker.person_id = Person.person_id JOIN AssignmentCategories ON WeeklyTracker.category_id = AssignmentCategories.category_id JOIN People AS Assistant ON WeeklyTracker.assistant_id = Assistant.person_id JOIN Status ON WeeklyTracker.status_id = Status.status_id JOIN PerformanceLevels ON WeeklyTracker.performance_id = PerformanceLevels.performance_id JOIN Weeks ON WeeklyTracker.week_id = Weeks.week_id 
        WHERE YEAR(Weeks.weekly_date) = ? AND MONTH(Weeks.weekly_date) = ? ORDER BY Weeks.weekly_date;";

        try {
            $stmt = $this->conn->prepare($query);
            $stmt->execute([$year, $month]);
        }
        catch (PDOException $e) {
            echo 'Exception occured. Error code: ' . $e->getCode(); 
            echo '<br>Error Message: ' . $e->getMessage();
        }

        return $stmt->fetchAll();
    }

    public function getBoundByDate($start_date, $end_date) {
        $query = "SELECT Person.name AS person_name, AssignmentCategories.category_title, Assistant.name AS assistant_name, Status.status_descriptor, PerformanceLevels.levels, WeeklyTracker.hall, DATE_FORMAT(Weeks.weekly_date, \"%M %d, %Y\") AS assignment_date, WeeklyTracker.assignment_id
        FROM WeeklyTracker 
        JOIN People AS Person ON WeeklyTracker.person_id = Person.person_id JOIN AssignmentCategories ON WeeklyTracker.category_id = AssignmentCategories.category_id JOIN People AS Assistant ON WeeklyTracker.assistant_id = Assistant.person_id JOIN Status ON WeeklyTracker.status_id = Status.status_id JOIN PerformanceLevels ON WeeklyTracker.performance_id = PerformanceLevels.performance_id JOIN Weeks ON WeeklyTracker.week_id = Weeks.week_id 
        WHERE Weeks.weekly_date BETWEEN DATE(:start_date) AND DATE(:end_date) ORDER BY Weeks.weekly_date;";

        try {
            $stmt = $this->conn->prepare($query);
            $stmt->execute(['start_date' => $start_date, 'end_date' => $end_date]);
        }
        catch (PDOException $e) {
            echo 'Exception occured. Error code: ' . $e->getCode(); 
            echo '<br>Error Message: ' . $e->getMessage();
        }

        return $stmt->fetchAll();
    }

    public function getBoundByDatePerson($start_date, $end_date, $person_name) {
        $query = "SELECT Person.name AS person_name, AssignmentCategories.category_title, Assistant.name AS assistant_name, Status.status_descriptor, PerformanceLevels.levels, WeeklyTracker.hall, DATE_FORMAT(Weeks.weekly_date, \"%M %d, %Y\") AS assignment_date, WeeklyTracker.assignment_id
        FROM WeeklyTracker 
        JOIN People AS Person ON WeeklyTracker.person_id = Person.person_id JOIN AssignmentCategories ON WeeklyTracker.category_id = AssignmentCategories.category_id JOIN People AS Assistant ON WeeklyTracker.assistant_id = Assistant.person_id JOIN Status ON WeeklyTracker.status_id = Status.status_id JOIN PerformanceLevels ON WeeklyTracker.performance_id = PerformanceLevels.performance_id JOIN Weeks ON WeeklyTracker.week_id = Weeks.week_id 
        WHERE Person.name = :person_name AND Weeks.weekly_date BETWEEN DATE(:start_date) AND DATE(:end_date) ORDER BY Weeks.weekly_date;";

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
        $query = "SELECT AssignmentCategories.category_title, Assistant.name AS assistant_name, Status.status_descriptor, PerformanceLevels.levels, WeeklyTracker.hall, DATE_FORMAT(Weeks.weekly_date, \"%M %d, %Y\") AS assignment_date, WeeklyTracker.assignment_id
        FROM WeeklyTracker 
        JOIN People AS Person ON WeeklyTracker.person_id = Person.person_id JOIN AssignmentCategories ON WeeklyTracker.category_id = AssignmentCategories.category_id JOIN People AS Assistant ON WeeklyTracker.assistant_id = Assistant.person_id JOIN Status ON WeeklyTracker.status_id = Status.status_id JOIN PerformanceLevels ON WeeklyTracker.performance_id = PerformanceLevels.performance_id JOIN Weeks ON WeeklyTracker.week_id = Weeks.week_id 
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

    public function getAssignment($assignment_id) {
        $query = "SELECT Person.name AS person_name, AssignmentCategories.category_title, Assistant.name AS assistant_name, Status.status_descriptor, PerformanceLevels.levels, WeeklyTracker.hall, Weeks.weekly_date
        FROM WeeklyTracker 
        JOIN People AS Person ON WeeklyTracker.person_id = Person.person_id JOIN AssignmentCategories ON WeeklyTracker.category_id = AssignmentCategories.category_id JOIN People AS Assistant ON WeeklyTracker.assistant_id = Assistant.person_id JOIN Status ON WeeklyTracker.status_id = Status.status_id JOIN PerformanceLevels ON WeeklyTracker.performance_id = PerformanceLevels.performance_id JOIN Weeks ON WeeklyTracker.week_id = Weeks.week_id 
        WHERE WeeklyTracker.assignment_id = :assignment_id";

        try {
            $stmt = $this->conn->prepare($query);
            $stmt->execute(['assignment_id' => $assignment_id]);
        }
        catch (PDOException $e) {
            echo 'Exception occured. Error code: ' . $e->getCode(); 
            echo '<br>Error Message: ' . $e->getMessage();
        }

        $row = $stmt->fetch();
        if ($row === False) return 0;
        else return $row;
    }

    public function addAssignment($person_id, $category_id, $assistant_id, $week_id, $status_id, $performance_id, $hall) {
        $query = "INSERT INTO WeeklyTracker (person_id, category_id, assistant_id, week_id, status_id, performance_id, hall) VALUES (:person_id, :category_id, :assistant_id, :week_id, :status_id, :performance_id, :hall)";

        try {
            $stmt = $this->conn->prepare($query);
            $stmt->execute(['person_id'=> $person_id, 'category_id' => $category_id, 'assistant_id' => $assistant_id, 'week_id' => $week_id, 'status_id' => $status_id, 'performance_id' => $performance_id, 'hall' => $hall]);
        }
        catch (PDOException $e) {
            echo 'Exception occured. Error code: ' . $e->getCode(); 
            echo '<br>Error Message: ' . $e->getMessage();
            return -1;
        }
        
        return 0;
    }

    public function updateAssignment($assignment_id, $person_id, $category_id, $assistant_id, $status_id, $performance_id, $hall) {
        $query = "UPDATE WeeklyTracker SET person_id = :person_id, category_id = :category_id, assistant_id = :assistant_id, status_id = :status_id, performance_id = :performance_id, hall = :hall WHERE assignment_id = :assignment_id";

        try {
            $stmt = $this->conn->prepare($query);
            $stmt->execute(['person_id'=> $person_id, 'category_id' => $category_id, 'assistant_id' => $assistant_id, 'status_id' => $status_id, 'performance_id' => $performance_id, 'hall' => $hall, 'assignment_id' => $assignment_id]);
        }
        catch (PDOException $e) {
            echo 'Exception occured. Error code: ' . $e->getCode(); 
            echo '<br>Error Message: ' . $e->getMessage();
            return -1;
        }
        
        return 0;
    }

    public function deleteAssignment($assignment_id) {
        $query = "DELETE FROM WeeklyTracker WHERE assignment_id = :assignment_id";

        try {
            $stmt = $this->conn->prepare($query);
            $stmt->execute(['assignment_id' => $assignment_id]);
        }
        catch (PDOException $e) {
            echo 'Exception occured. Error code: ' . $e->getCode(); 
            echo '<br>Error Message: ' . $e->getMessage();
            return -1;
        }
        
        return 0;
    }
};