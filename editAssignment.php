<?php
session_start();
require_once "model/database.php";
require_once "model/Assignment.php";
require_once "model/PeopleManager.php";

$db = new Database();
$db->connectDB();

$assignments = new Assignment($db->pdo);
$people = new People($db->pdo);

if ( isset($_POST['Name']) && isset($_POST['category']) ) {
    if ( (strlen($_POST['Name']) > 0) && (strlen($_POST['category']) > 0) ) {
        $person_id = $people->getPersonID($_POST['Name']);
        if ($person_id == -1) {
            $_SESSION['error'] = 'Assignment addition failed. Person Name error!';
        }
        if ($_POST['assistant'] === "None") {
            $assistant_id = 5;
        } else {
            $assistant_id = $people->getPersonID($_POST['assistant']);
        }

        if ( $assignments->updateAssignment($_SESSION['assignment_id'], $person_id, $_POST['category'], $assistant_id, $_POST['status'] + 0, $_POST['level'] + 0, $_POST['hall'] + 0) == 0) {
            $_SESSION['success'] = 'Assignment ' . $_SESSION['assignment_id'] . ' was successfully updated!';
            
        } else if ( !isset($_SESSION['error']) ) {
            $_SESSION['error'] = 'Assignment update failed!';
        }
    }
    // if ($_SESSION['source_page'] == "add") {
        header("Location: addAssignment.php");
    // }
    return;
}

if ( !isset($_GET['assignment_id']) ) {
    $_SESSION['error'] = 'Assignment ID not found!';
    return;
} else {
    $assignment = $assignments->getAssignment($_GET['assignment_id']);
    if ($assignment == 0) {
        $_SESSION['error'] = 'Assignment not found!';
    } else {
        $_SESSION['assignment_id'] = $_GET['assignment_id'];

        $pre_name = $assignment['person_name'];
        $pre_category = $assignment['category_title'];
        $pre_assistant = $assignment['assistant_name'];
        $pre_status = $assignment['status_descriptor'];
        $pre_performance = $assignment['levels'];
        $pre_hall = $assignment['hall'];
        $pre_date = $assignment['weekly_date'];
    }
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
    <h1>Update the assignment</h1>
    <h3>Assignment ID: <?= htmlentities($_SESSION['assignment_id']); ?></h3>
    <form method="post">
        <p><label for="">Date: <?= htmlentities($pre_date); ?></label></p>
        <p>
            <label for="Name">Enter the Name (No spelling mistakes)</label>
            <input type="text" id="Name" name="Name" value= <?= htmlentities($pre_name) ?>>
        </p>
        <p>
            <label for="category">Assignment (Currently: <?= htmlentities($pre_category) ?>)</label>
            <select name="category" id="category">
                <option value="0">Select Category</option>
                <option value="1">Bible Reading</option>
                <option value="2">Talk</option>
                <option value="3">Start Conversation (1)</option>
            </select>
        </p>
        <p>
            <label for="assistant">Assistant Name</label>
            <input type="text" name="assistant" id="assistant" value= <?= htmlentities($pre_assistant) ?>>
        </p>
        <p>
            Hall (Currently: Hall <?= htmlentities($pre_hall) ?>)<br>
            <input type="radio" name="hall" id="hall1" value=1>Hall 1 <br>
            <input type="radio" name="hall" id="hall2" value=2>Hall 2
        </p>
        <p>
            <label for="status">Assignment Status (Currently: <?= htmlentities($pre_status) ?>)</label>
            <select name="status" id="status">
                <option value="1" selected>Pending</option>
                <option value="2">Completed</option>
                <option value="3">Missed</option>
            </select>
        </p>
        <p>
            <label for="level">Performance Rating (Currently: <?= htmlentities($pre_performance) ?>)</label>
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

        print_r($_SESSION['weekly_assignments']);
        ?>
    </pre>
</body>
</html>