<?php
session_start();
require_once "model/database.php";
require_once "model/Assignment.php";
require_once "model/PeopleManager.php";

$_SESSION['source'] = 'PersonalLog';

$db = new Database();
$db->connectDB();

$assignments = new Assignment($db->pdo);
$people = new People($db->pdo);
$res = 0;

if ( isset($_GET['Name']) && (strlen($_GET['Name']) > 0)) {
    $person_name = htmlentities($_GET['Name']);
    $assignment_list = $assignments->getByIndividual($person_name);
    
    $person_info = $people->getPersonInfo($person_name);
    $assignment_count_pp = 0;

    if ($assignment_list == false) {
        $_SESSION['error'] = "No assignments found for " . $person_name;
    } else if ($assignment_list == -1) {
        $_SESSION['error'] = "Error fetching records";
    } else {
        $assignment_count_pp = $people->getAssignmentCount($person_name);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/stylesIndividual.css">
    <link rel="stylesheet" href="css/styles.css">
    <title>Record by Individual</title>
</head>
<body>
    <div class="nav">
        <a href="index.php"><div class="home"></div></a>
        <a class="people" href="ManagePerson.php">People</a>
        <a class="weeks" href="SelectWeek.php">Weeks</a>
    </div>
    <h1>Search for an individual</h1>
    <form method="get">
        <label for="Name">Enter the name of the individual:</label><br>
        <input type="text" name="Name" id="Name" required value="<?php if (isset($_GET['Name'])) echo urldecode($_GET['Name']); else echo "";?>"><br>
        <input type="submit" value="Submit">
    </form>
    
    <?php if (isset($_SESSION['success'])): ?>
        <div class="message success" id="log-message"><?= htmlentities($_SESSION['success']); ?></div>
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
        <?php unset($_SESSION['success']); ?>
        
        <?php elseif (isset($_SESSION['error'])): ?>
            <div class="message error" id="log-message"><?= htmlentities($_SESSION['error']); ?></div>
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
        <?php unset($_SESSION['error']); ?>
        <?php endif; ?>
        
        <?php if (isset($_GET['Name']) && isset($assignment_count_pp)): ?>
        <div class="person-detail">
            <div>
                <p class="name"> <?= $person_name ?> </p>
                <p><?= $person_info['role_title'] . ", " . $person_info['group_name'] . " Group" ?></p>
            </div>
            <p><?= "Total Assignment Count: " . $assignment_count_pp ?></p>
        </div>
        <?php endif; ?>

    <div class="separator"></div>

    <?php if ( isset($_GET['Name']) ): ?>
        <div class="assignments-container">
            <?php
                $grouped_assignments = [];
                foreach ($assignment_list as $assignment) {
                    $year = date('Y', strtotime($assignment['assignment_date']));
                    $grouped_assignments[$year][] = $assignment;
                }

                foreach ($grouped_assignments as $year => $assignments): ?>
                    <div class="year-group">
                        <div class="year-heading"><?= $year; ?></div>
                        <?php foreach ($assignments as $row): ?>
                            <div class="assignment-card">
                                <div class="date-box">
                                    <div class="month"><?= date('M', strtotime($row['assignment_date'])); ?></div>
                                    <div class="day"><?= date('d', strtotime($row['assignment_date'])); ?></div>
                                </div>
                                <div class="details">
                                    <strong class="category"><?= htmlentities($row['category_title']); ?></strong><br>
                                    Assistant: <?= htmlentities($row['assistant_name']); ?><br>
                                    Hall: <?= htmlentities($row['hall']); ?>
                                </div>
                                <div class="status">
                                    <strong>Status:</strong> <?= htmlentities($row['status_descriptor']); ?><br>
                                    <strong>Rating:</strong> <?= htmlentities($row['levels']); ?>
                                </div>
                                <div class="edit">
                                <a href= <?= "Edit.php?assignment_id=" . htmlentities($row['assignment_id']) ?>><button class="aedit">Edit</button></a>
                                <a href= <?= "Delete.php?assignment_id=" . htmlentities($row['assignment_id']) ?>><button class="adelete">Delete</button></a>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
    <?php unset($_GET['Name']); ?>
</body>
</html>
