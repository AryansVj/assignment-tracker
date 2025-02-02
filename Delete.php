<?php
session_start();
require_once "model/database.php";
require_once "model/Assignment.php";
require_once "model/Weeks.php";

$db = new Database();
$db->connectDB();

$assignments = new Assignment($db->pdo);
$weeks = new Weeks($db->pdo);

$back_path = $_SESSION['source'] . ".php";

// Landing for delete request with error checking
if (!isset($_GET['assignment_id'])) {
    $_SESSION['error'] = 'Assignment ID not found!';
    header("Location:" . $back_path);
    return;
} else {
    $assignment = $assignments->getAssignment($_GET['assignment_id']);
    if ($assignment == 0) {
        $_SESSION['error'] = 'Assignment not found!';
        header("Location:" . $back_path);
        return;
    } else {
        $_SESSION['assignment_id'] = $_GET['assignment_id'];
    }
    
    $_SESSION['week'] = $weeks->getWeekID($assignment['weekly_date']);
}

if ($_SESSION['source'] == 'individual') {
    $back_path .= "?Name=" . $assignment['person_name'];
}

// Deleting the assignment through a POST request
if (isset($_POST['assignment_id'])) {
    $res = $assignments->deleteAssignment($_POST['assignment_id']);
    if ($res == 0) {
        // Decrementing weekly count by refering to the db
        $_SESSION['weekly_count'] = $weeks->getCount($_SESSION['week']);
        $weeks->updateCount($_SESSION['week'], $_SESSION['weekly_count'] - 1);

        $_SESSION['success'] = 'Assignment successfully deleted!';
        
        unset($_SESSION['source']);
        header("Location:" . $back_path);
        return;
    } else if ($res == -1) {
        $_SESSION['error'] = 'Assignment delete failed! (ID: '. $_POST['assignment_id'] . ')';

        header("Location: Delete.php?assignment_id=" . htmlentities($_POST['assignment_id']));
        return;
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/stylesDelete.css">
    <link rel="stylesheet" href="css/styles.css">
    <title>Delete Assignment</title>
</head>
<body>
    <div class="nav">
        <a href="index.php"><div class="home"></div></a>
        <a class="log" href="Log.php">Log</a>
        <a class="people" href="ManagePerson.php">People</a>
        <a class="weeks" href="SelectWeek.php">Weeks</a>
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
                <p><strong><?= htmlentities($assignment['person_name']) ?></strong></p>
                <span class="acategory"><?= htmlentities($assignment['category_title']) ?></span>
            </div>
            <p>Assistant: <?= htmlentities($assignment['assistant_name']) ?></p>
            <p>Hall: <?= htmlentities($assignment['hall']) ?></p>
            <p>Status: <?= htmlentities($assignment['status_descriptor']) ?></p>
            <p>Performance: <?= htmlentities($assignment['levels']) ?></p>
        </div>

        <form method="post">
            <p>Do you want to delete this assignment from <span><?= htmlentities($assignment['weekly_date']) ?></span>?</p>
            <input type="hidden" name="assignment_id" value="<?= htmlentities($_GET['assignment_id']) ?>">
            <input type="hidden" name="week_id" value="<?= htmlentities($_SESSION['week']) ?>">
            <div class="button-container">
                <input type="submit" class="delete" value="Delete">
                <a href=<?= $back_path ?> class="btn-link">Cancel</a>
            </div>
        </form>
    </div>
</body>
</html>
