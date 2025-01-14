<?php
session_start();
require_once "model/database.php";
require_once "model/Assignment.php";
require_once "model/PeopleManager.php";
require_once "model/Weeks.php";
if ( isset($_POST['Name']) && isset($_POST['category']) ) {
    if ( (strlen($_POST['Name']) > 0) && (strlen($_POST['category']) > 0) ) {

        $db = new Database();
        $db->connectDB();

        $assignments = new Assignment($db->pdo);
        $people = new People($db->pdo);
        $weeks = new Weeks($db->pdo);

        $person_id = $people->getPersonID($_POST['Name']);
        if ($_POST['assistant'] === "None") {
            $assistant_id = 5;
        } else {
            $assistant_id = $people->getPersonID($_POST['assistant']);
        }
        
        if ( $assignments->addAssignment($person_id, $_POST['category'], $assistant_id, $_SESSION['week'], $_POST['status'] + 0, $_POST['level'] + 0, $_POST['hall']) == 0) {
            $_SESSION['success'] = 'Assignment for ' . $_POST['Name'] . ' was successfully added!';
            $_SESSION['weekly_count'] = $_SESSION['weekly_count'] + 1;
            
            $weeks->updateCount($_SESSION['week'], $_SESSION['weekly_count']);

        } else {
            $_SESSION['error'] = 'Error adding the assignment!';
        }

        $weekly_assignments = $assignments->getWeek($_SESSION['date']);
    }
    header("Location: addAssignment.php");
    return;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <form method="post">
        <p>
            <label for="Name">Enter the Name (No spelling mistakes)</label>
            <input type="text" id="Name" name="Name" value="Name">
        </p>
        <p>
            <label for="category">Assignment</label>
            <select name="category" id="category">
                <option value="0">Select Category</option>
                <option value="1">Bible Reading</option>
                <option value="2">Talk</option>
                <option value="3">Start Conversation (1)</option>
            </select>
        </p>
        <p>
            <label for="assistant">Assistant Name</label>
            <input type="text" name="assistant" id="assistant" value="None">
        </p>
        <p>
            Hall<br>
            <input type="radio" name="hall" id="hall1" value=1 checked>Hall 1 <br>
            <input type="radio" name="hall" id="hall2" value=2>Hall 2
        </p>
        <p>
            <label for="status">Assignment Status</label>
            <select name="status" id="status">
                <option value="1" selected>Pending</option>
                <option value="2">Completed</option>
                <option value="3">Missed</option>
            </select>
        </p>
        <p>
            <label for="level">Performance Rating</label>
            <select name="level" id="level">
                <option value="1">Excellent</option>
                <option value="2">Good</option>
                <option value="3">Neutral</option>
                <option value="4">Poor</option>
                <option value="5">Very Poor</option>
                <option value="6">Voluntary</option>
                <option value="7" selected>Not-Rated</option>
            </select>
        </p>

        <input type="submit">
    </form>
    <a href="SelectWeek.php">Go Back</a>

    <pre>
        <?php
        if ( isset($_SESSION['error']) ) {
            echo '<p style="color:red">' . $_SESSION['error'] . '</p>';
            unset($_SESSION['error']);
        }

        if ( isset($_SESSION['success']) ) {
            echo '<p style="color:green">' . $_SESSION['success'] . '</p>';
            unset($_SESSION['success']);
        }

        print_r($_SESSION);
        ?>
    </pre>
</body>
</html>