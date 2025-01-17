<?php
session_start();
require_once "model/database.php";
require_once "model/Weeks.php";
if ( isset($_POST['WeeklyDate']) ) {
    if (strlen($_POST['WeeklyDate']) > 0) {
        $db = new Database();
        $db->connectDB();
        
        $weeks = new Weeks($db->pdo);
        $weeks->addWeek($_POST['WeeklyDate'], 0, NULL);
        $week_id = $weeks->getWeekID($_POST['WeeklyDate']);
        
        $_SESSION['week'] = $week_id;
        $_SESSION['date'] = $_POST['WeeklyDate'];
        header("Location: Add.php");
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
    <title>Get Date</title>
    <link rel="stylesheet" href="css/stylesSelectWeek.css">
</head>
<body>
    <div class="container">
        <h1>Set the date of the week for assignments</h1>
        <form method="post">
            <p><label for="week">Select the meeting date of the week</label></p>
            <input type="date" id="week" name="WeeklyDate" required><br>
            <input type="submit" value="Submit">
        </form>
    </div>
</body>
</html>
