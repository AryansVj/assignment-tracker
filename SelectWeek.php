<?php
session_start();
require_once "model/database.php";
require_once "model/Weeks.php";
if ( isset($_POST['WeeklyDate']) ) {
    if (strlen($_POST['WeeklyDate']) > 0) {
        $db = new Database();
        $db->connectDB();
        
        $weeks = new Weeks($db->pdo);
        $week_id = $weeks->getWeekID($_POST['WeeklyDate']);
        $weekly_count = $weeks->getCount($week_id);
        
        $_SESSION['week'] = $week_id;
        $_SESSION['weekly_count'] = $weekly_count;
        $_SESSION['date'] = $_POST['WeeklyDate'];
        header("Location: addAssignment.php");
        return;
    } else {
        header("Location: getWeek.php");
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
    <title>Get Date</title>
</head>
<body>
    <h1>Set the date of the week for assignments</h1>
    <form method="post">
        <p><label for="week">Select the meeting date of the week</label></p>
        <input type="date" id="week" name="WeeklyDate"><br>
        <input type="submit">
    </form>
</body>
</html>