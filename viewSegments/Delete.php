<?php
session_start();
require_once $_SERVER['DOCUMENT_ROOT'] . "/model/database.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/model/Segments.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/model/Weeks.php";

$db = new Database();
$db->connectDB();

$segments = new Segments($db->pdo);
$weeks = new Weeks($db->pdo);

$back_path = $_SESSION['source'] . ".php";

// Landing for delete request with error checking
if (!isset($_GET['segment_track_id'])) {
    $_SESSION['error'] = 'Segment tracking ID not found!';
    header("Location:" . $back_path);
    return;
} else {
    $segment = $segments->getSegment($_GET['segment_track_id']);
    if ($segment == 0) {
        $_SESSION['error'] = 'Assigned segment not found!';
        header("Location:" . $back_path);
        return;
    } else {
        $_SESSION['segment_track_id'] = $_GET['segment_track_id'];
    }
    
    $_SESSION['week'] = $weeks->getWeekID($segment['weekly_date']);
}

if ($_SESSION['source'] == 'individual') {
    $back_path .= "?Name=" . $segment['person_name'];
}

// Deleting the assignment through a POST request
if (isset($_POST['segment_track_id'])) {
    $res = $segments->deleteSegment($_POST['segment_track_id']);
    if ($res == 0) {
        // Decrementing weekly count by refering to the db
        // $_SESSION['weekly_count'] = $weeks->getCount($_SESSION['week']);
        // $weeks->updateCount($_SESSION['week'], $_SESSION['weekly_count'] - 1);

        $_SESSION['success'] = 'Assignment successfully deleted!';
        
        unset($_SESSION['source']);
        header("Location:" . $back_path);
        return;
    } else if ($res == -1) {
        $_SESSION['error'] = 'Assignment delete failed! (ID: '. $_POST['segment_track_id'] . ')';

        header("Location: /viewSegments/Delete.php?segment_track_id=" . htmlentities($_POST['segment_track_id']));
        return;
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/css/stylesDelete.css">
    <link rel="stylesheet" href="/css/styles.css">
    <title>Delete Assignment</title>
</head>
<body>
    <div class="nav">
        <a href="/index.php"><div class="home"></div></a>
        <a class="log" href="/Log.php">Log</a>
        <a class="people" href="/viewPeople/ManagePeople.php">People</a>
        <a class="weeks" href="/SelectWeek.php">Weeks</a>
    </div>
    <div class="container">
        <h1>Delete Assignment</h1>

        <?php if (isset($_SESSION['error'])): ?>
            <div class="message error">
                <?= htmlentities($_SESSION['error']) ?>
            </div>
            <script>
                // Automatically hide the error message after 5 seconds
                setTimeout(() => {
                    const errorMessage = document.getElementById('error-message');
                    if (errorMessage) {
                        errorMessage.style.transition = "opacity 1s";
                        errorMessage.style.opacity = "0";
                        setTimeout(() => errorMessage.remove(), 1000); // Remove from DOM after fade-out
                    }
                }, 5000);
            </script>
        <?php unset($_SESSION['error']); endif; ?>

        <div class="assignment">
            <div class="atitle">
                <p><strong><?= htmlentities($segment['person_name']) ?></strong></p>
                <span class="acategory"><?= htmlentities($segment['segment_name']) ?></span>
            </div>
            <p>Title: <?= htmlentities($segment['title']) ?></p>
            <p>Performance: <?= htmlentities($segment['performance']) ?></p>
        </div>

        <form method="post">
            <p>Do you want to delete this assignment from <span><?= htmlentities($segment['weekly_date']) ?></span>?</p>
            <input type="hidden" name="segment_track_id" value="<?= htmlentities($_GET['segment_track_id']) ?>">
            <input type="hidden" name="week_id" value="<?= htmlentities($_SESSION['week']) ?>">
            <div class="button-container">
                <input type="submit" class="delete" value="Delete">
                <a href=<?= $back_path ?> class="btn-link">Cancel</a>
            </div>
        </form>
    </div>
</body>
</html>
