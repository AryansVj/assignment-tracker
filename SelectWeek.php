<?php
session_start();
require_once "model/database.php";
require_once "model/Weeks.php";

// To set the mid week meeting day
$dayof_midweek = 4; 

if ( isset($_POST['WeeklyDate']) ) {
    // Check if the date is set and if it is a Sunday or a pre-set mid week day
    if ((strlen($_POST['WeeklyDate']) > 0) && ( date("w", strtotime(htmlentities($_POST['WeeklyDate']))) == 0 )) {
        $db = new Database();
        $db->connectDB();
        
        $weeks = new Weeks($db->pdo);
        $weeks->addWeek($_POST['WeeklyDate'], 0, NULL);
        $week_id = $weeks->getWeekID($_POST['WeeklyDate']);
        
        $_SESSION['week'] = $week_id;
        $_SESSION['date'] = htmlentities($_POST['WeeklyDate']);
        $_SESSION['mid-date'] = date("Y-m-d", strtotime(htmlentities($_POST['WeeklyDate'])) - (7-$dayof_midweek)*24*3600);

        if ( isset($_POST['segments']) ) {
            header("Location: AddSegment.php");
        } else if ( isset($_POST['assignments']) ) {
            header("Location: Add.php");
        }
        return;
    } else {
        header("Location: SelectWeek.php");
        return;
    }
}
session_destroy();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/stylesSelectWeek.css">
    <link rel="stylesheet" href="css/styles.css">
    <title>Get Date</title>
</head>
<body>
    <div class="nav">
        <a href="index.php"><div class="home"></div></a>
        <a class="log" href="Log.php">Log</a>
        <a class="people" href="ManagePerson.php">People</a>
        <a class="weeks" href="SelectWeek.php">Weeks</a>
    </div>
    <div class="container">
        <h1>Welcome</h1>
        <form method="post">
            <p><label for="week">Select the Sunday of the week to display</label></p>
            <input type="date" id="week" name="WeeklyDate" required><br>
            <input type="submit" name="assignments" value="Assignments">
            <input type="submit" name="segments" value="Segments">
        </form>
    </div>
</body>
</html>
