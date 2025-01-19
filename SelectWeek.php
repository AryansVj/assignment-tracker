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
    <link rel="stylesheet" href="css/stylesSelectWeek.css">
    <link rel="stylesheet" href="css/styles.css">
    <title>Get Date</title>
</head>
<body>
    <div class="nav">
        <a href="index.php"><div class="home"></div></a>
        <a class="people" href="ManagePerson.php">People</a>
        <a class="weeks" href="SelectWeek.php">Weeks</a>
    </div>
    <div class="container">
        <h1>Welcome</h1>
        <form method="post">
            <p><label for="week">Select the meeting date of the week to display</label></p>
            <input type="date" id="week" name="WeeklyDate" required><br>
            <input type="submit" value="Submit">
        </form>
    </div>
</body>
</html>
