

<?php
session_start();
require_once "model/database.php";
require_once "model/Assignment.php";
require_once "model/PeopleManager.php";

$_SESSION['source'] = 'individual';

$db = new Database();
$db->connectDB();

$assignments = new Assignment($db->pdo);
$people = new People($db->pdo);
$res = 0;

if ( isset($_GET['Name']) && (strlen($_GET['Name']) > 0)) {
    $person_name = $_GET['Name'];
    $res = $assignments->getByIndividual($person_name);

    if ($res == false) {
        $_SESSION['error'] = "No assignments found for " . $person_name;
    } else if ($res == -1) {
        $_SESSION['error'] = "Error fetching records";
    } else {
        $_SESSION['assignments'] = $res;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Record by Individual</title>
    <link rel="stylesheet" href="css/stylesIndividual.css">
    <style>

    </style>
</head>
<body>
    <h1>Search for an individual</h1>
    <form method="get" action="individual.php">
        <label for="Name">Enter the name of the individual:</label><br>
        <input type="text" name="Name" id="Name" required value=
            <?php
                if (isset($_GET['Name'])) echo $_GET['Name'];
                else echo "";
            ?>
        ><br>
        <input type="submit" value="Submit">
    </form>

    <div class="separator"></div>

    <?php if (isset($_SESSION['assignments']) && isset($_GET['Name'])): ?>
        <div class="assignments-container">
            <div class="person-name"><?= htmlentities($_GET['Name']); ?></div>
            <?php
                $grouped_assignments = [];
                foreach ($_SESSION['assignments'] as $assignment) {
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
        <?php unset($_SESSION['assignments']); ?>
    <?php elseif (isset($_SESSION['error'])): ?>
        <div class="error"><?= htmlentities($_SESSION['error']); ?></div>
        <?php unset($_SESSION['error']); ?>
    <?php endif; ?>
    <?php unset($_GET['Name']); ?>
</body>
</html>
