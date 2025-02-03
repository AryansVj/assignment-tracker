<?php
session_start();
require_once $_SERVER['DOCUMENT_ROOT'] . "/model/database.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/model/Weeks.php";

// To set the mid week meeting day (0-sunday)
if (!isset($_SESSION['dayof_midweek'])) {
    $_SESSION['dayof_midweek'] = 4;
}

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
        $_SESSION['mid-date'] = date("Y-m-d", strtotime(htmlentities($_POST['WeeklyDate'])) - (7-$_SESSION['dayof_midweek'])*24*3600);

        if ( isset($_POST['segments']) ) {
            header("Location: /viewSegments/Add.php");
        } else if ( isset($_POST['assignments']) ) {
            header("Location: /viewAssignments/Add.php");
        }
        return;
    } else {
        $_SESSION['error'] = "Invalid date";
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
        <a href="/index.php"><div class="home"></div></a>
        <a class="log" href="/Log.php">Log</a>
        <a class="people" href="/viewPeople/ManagePeople.php">People</a>
        <a class="weeks" href="/SelectWeek.php">Weeks</a>
    </div>
    <div class="container">

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
