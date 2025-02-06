<?php
session_start();
require_once $_SERVER['DOCUMENT_ROOT'] . "/model/database.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/model/Assignment.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/model/PeopleManager.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/model/Weeks.php";

//Current source file path
$_SESSION['source'] = '/viewAssignments/Add';

$db = new Database();
$db->connectDB();

$assignments = new Assignment($db->pdo);
$people = new People($db->pdo);
$weeks = new Weeks($db->pdo);

// If the Name of the person and the assignment category is properly set, add the assignment
if ( isset($_POST['Name']) && isset($_POST['category']) ) {
    if ( (strlen($_POST['Name']) > 0) && (strlen($_POST['category']) > 0) ) {

        $person_id = $people->getPersonID($_POST['Name']);
        if ($person_id == -1) {
            $_SESSION['error'] = 'Assignment addition failed. Person Name error!';
        }
        
        $assistant_id = $people->getPersonID($_POST['assistant']);
        
        if ( $assignments->addAssignment($person_id, $_POST['category'], $assistant_id, $_SESSION['week'], $_POST['status'] + 0, $_POST['level'] + 0, $_POST['hall']) == 0) {
            $_SESSION['success'] = 'Assignment for ' . $_POST['Name'] . ' was successfully added!';
            
            // Incrementing weekly count by refering to the db
            $weekly_count = $weeks->getCount($_SESSION['week']);
            $weeks->updateCount($_SESSION['week'], $weekly_count + 1);

        } else if ( !isset($_SESSION['error']) ) {
            $_SESSION['error'] = 'Assignment addition failed!';
        }

    }
    header("Location: /viewAssignments/Add.php");
    return;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/css/stylesAdd.css">
    <link rel="stylesheet" href="/css/styles.css">
    <title>Add Assignment</title>
</head>
<body>
    <div class="nav">
        <a href="/index.php"><div class="home"></div></a>
        <a class="log" href="/Log.php">Log</a>
        <a class="people" href="/viewPeople/ManagePeople.php">People</a>
        <a class="weeks" href="/SelectWeek.php">Weeks</a>
    </div>
    <h1>Weekly Dashboard</h1>

    <div class="container">
        <!-- Log Messages -->
        <div class="form-container">
            <?php if (isset($_SESSION['error'])): ?>
                <div class="message error" id="log-message">
                    <?= htmlentities($_SESSION['error']) ?>
                </div>
                <script>
                    // Automatically hide the error message after 5 seconds
                    setTimeout(() => {
                        const errorMessage = document.getElementById('log-message');
                        if (errorMessage) {
                            errorMessage.style.transition = "opacity 1s";
                            errorMessage.style.opacity = "0";
                            setTimeout(() => errorMessage.remove(), 1000); // Remove from DOM after fade-out
                        }
                    }, 3000);
                </script>
            <?php unset($_SESSION['error']); endif; ?>

            <?php if (isset($_SESSION['success'])): ?>
                <div class="message success" id="log-message">
                    <?= htmlentities($_SESSION['success']) ?>
                </div>
                <script>
                    // Automatically hide the error message after 5 seconds
                    setTimeout(() => {
                        const errorMessage = document.getElementById('log-message');
                        if (errorMessage) {
                            errorMessage.style.transition = "opacity 1s";
                            errorMessage.style.opacity = "0";
                            setTimeout(() => errorMessage.remove(), 1000); // Remove from DOM after fade-out
                        }
                    }, 3000);
                </script>
            <?php unset($_SESSION['success']); endif; ?>

            <!-- Form section -->
            <form method="post">
                <p>
                    <label for="Name">Enter the Name </label>
                    <input type="text" id="Name" name="Name" placeholder="Name">
                </p>
                <p>
                    <label for="category">Assignment</label>
                    <select name="category" id="category" required>
                        <option value="0" selected>Select Category</option>
                        <option value="1"?>Bible Reading</option>
                        <option value="2">Start Conv (1)</option>
                        <option value="3">Start Conv (2)</option>
                        <option value="4">Start Conv (3)</option>
                        <option value="5">Start Conv (4)</option>
                        <option value="6">Follow up (3)</option>
                        <option value="7">Follow up (4)</option>
                        <option value="8">Making Disciple</option>
                        <option value="9">Explain your belief</option>
                        <option value="10">Talk</option>
                    </select>

                </p>
                <p>
                    <label for="assistant">Assistant Name</label>
                    <input type="text" name="assistant" id="assistant" value="None">
                </p>
                <p>
                    <label for="">Hall</label>
                    <input type="radio" name="hall" id="hall1" value="1" checked> Hall 1 <br>
                    <input type="radio" name="hall" id="hall2" value="2"> Hall 2
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
                <div class="button-container">
                    <input type="submit" value="Add Assignment">
                    <a href="/index.php" class="btn-link">Home</a>
                </div>
            </form>
        </div>

        <!-- Assignments Section -->
        <?php
            $weekly_assignments = $assignments->getWeek($_SESSION['date']);
        ?>
        <div class="assignments-container">
            <div class="week-details">
                <?php 
                    $date = strtotime($_SESSION['mid-date']);
                    $weekly_count = $weeks->getCount($_SESSION['week']);
                ?>
                <p><?= date('F d', $date); ?></p>
                <p><?= date('Y', $date); ?></p>
                <a class="btn-link" href="/SelectWeek.php">Change</a>
                <p class="plabel">Assignment Count</p>
                <p><?= $weekly_count ?></p>
            </div>
            <div class="assignment-section">
                <h4>Hall 1 Assignments</h4>
                <?php
                if (!$weekly_assignments === false) {
                    foreach ($weekly_assignments as $assignment) {
                        if ($assignment['hall'] == 1) {
                            echo '<div class="assignment">';
                            echo '<div class="atitle"><p><strong>' . htmlentities($assignment['person_name']) . '</strong></p>';
                            echo '<span class="acategory">' . htmlentities($assignment['category_title']) . '</span></div>';
                            echo 'Assistant: ' . htmlentities($assignment['assistant_name']) . '<br>';
                            echo 'Status: ' . htmlentities($assignment['status_descriptor']) . '<br>';
                            echo 'Performance: ' . htmlentities($assignment['levels']) . '<br>';
                            echo '<a href="/viewAssignments/Edit.php?assignment_id=' . htmlentities($assignment['assignment_id']) . '"><button class="aedit">Edit</button></a> <a href="/viewAssignments/Delete.php?assignment_id='  . htmlentities($assignment['assignment_id']) . '"><button class="adelete">Delete</button></a>';
                            echo '</div>';
                        }
                    }
                }
                ?>
            </div>
            <div class="assignment-section">
                <h4>Hall 2 Assignments</h4>
                <?php
                if (!$weekly_assignments === false) {
                    foreach ($weekly_assignments as $assignment) {
                        if ($assignment['hall'] == 2) {
                            echo '<div class="assignment">';
                            echo '<div class="atitle"><p><strong>' . htmlentities($assignment['person_name']) . '</strong></p>';
                            echo '<span class="acategory">' . htmlentities($assignment['category_title']) . '</span></div>';
                            echo 'Assistant: ' . htmlentities($assignment['assistant_name']) . '<br>';
                            echo 'Status: ' . htmlentities($assignment['status_descriptor']) . '<br>';
                            echo 'Performance: ' . htmlentities($assignment['levels']) . '<br>';
                            echo '<a href="/viewAssignments/Edit.php?assignment_id=' . htmlentities($assignment['assignment_id']) . '"><button class="aedit">Edit</button></a> <a href="/viewAssignments/Delete.php?assignment_id=' . htmlentities($assignment['assignment_id']) . '"><button class="adelete">Delete</button></a>';
                            echo '</div>';
                        }
                    }
                }
                ?>
            </div>
        </div>
    </div>
</body>
</html>
