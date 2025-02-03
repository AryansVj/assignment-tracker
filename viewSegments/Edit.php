<?php
session_start();
require_once $_SERVER['DOCUMENT_ROOT'] . "/model/database.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/model/Segments.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/model/PeopleManager.php";

$db = new Database();
$db->connectDB();

$segments = new Segments($db->pdo);
$people = new People($db->pdo);

$back_path = $_SESSION['source'] . ".php";

if ( !isset($_GET['segment_track_id']) ) {
    $_SESSION['error'] = 'Segment Tracking ID not found!';
    return;
} else {
    $segment = $segments->getSegment($_GET['segment_track_id']);
    if (!$segment) {
        $_SESSION['error'] = 'Assigned segment not found!';
    } else {
        $_SESSION['segment_track_id'] = $_GET['segment_track_id'];

        $pre_name = $segment['person_name'];
        $pre_title = $segment['title'];
        $pre_segment = $segment['segment_id'];
        $pre_duration = $segment['duration'];
        $pre_performance = $segment['performance'];
        $pre_meeting = $segment['meeting_title'];
        $pre_date = $segment['weekly_date'];
    }
}

if ($_SESSION['source'] == 'Log') {
    $back_path .= "?Name=" . urlencode($segment['person_name']);
}

if ( isset($_POST['Name']) && isset($_POST['segment']) ) {
    if ( (strlen($_POST['Name']) > 0) && (strlen($_POST['segment']) > 0) ) {
        $person_id = $people->getPersonID($_POST['Name']);
        if ($person_id == -1) {
            $_SESSION['error'] = 'Segment addition failed. Person Name error!';
        }

        if ( $segments->updateSegment($_SESSION['segment_track_id'], $_POST['title'], $person_id, $_POST['segment'], $_POST['level'] + 0) == 0) {
            $_SESSION['success'] = 'Segment was successfully updated! (ID: '. $_SESSION['segment_track_id'] . ')';
            
        } else if ( !isset($_SESSION['error']) ) {
            $_SESSION['error'] = 'Segment update failed!';
        }
    }
    
    unset($_SESSION['source']);

    header("Location: " . $back_path);
    return;
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/css/stylesEdit.css">
    <link rel="stylesheet" href="/css/styles.css">
    <title>Edit Segment</title>
</head>
<body>
    <div class="nav">
        <a href="/index.php"><div class="home"></div></a>
        <a class="log" href="/Log.php">Log</a>
        <a class="people" href="/viewPeople/ManagePeople.php">People</a>
        <a class="weeks" href="/SelectWeek.php">Weeks</a>
    </div>
    <h1>Edit the Assigned Segment</h1>
    <div class="container">
        <div class="sub-data">
            <p>Assignment ID: <?= htmlentities($_SESSION['segment_track_id']); ?></p>
            <p>Date: <?= htmlentities($pre_date); ?></p>
        </div>

        <form method="post">
            <div class="form-group">
                <label for="segment">Segment</label>
                <select name="segment" id="segment">
                    <option value="0">Select segment</option>
                    <option value="1" <?=$pre_segment == 1 ? "selected":""; ?>>Chair - Sunday</option>
                    <option value="2" <?=$pre_segment == 2 ? "selected":""; ?>>Bible talk</option>
                    <option value="3" <?=$pre_segment == 3 ? "selected":""; ?>>Watchtower study</option>
                    <option value="4" <?=$pre_segment == 4 ? "selected":""; ?>>Watchtower reading</option>
                    <option value="4" <?=$pre_segment == 4 ? "selected":""; ?>></option>
                    <option value="5" <?=$pre_segment == 5 ? "selected":""; ?>>Main chair - Mid week</option>
                    <option value="6" <?=$pre_segment == 6 ? "selected":""; ?>>Secondary chair - Mid week</option>
                    <option value="7" <?=$pre_segment == 7 ? "selected":""; ?>>Treasures from God's word</option>
                    <option value="8" <?=$pre_segment == 8 ? "selected":""; ?>>Spiritual gems</option>
                    <option value="10" <?=$pre_segment == 10 ? "selected":""; ?>>Discussion (15)</option>
                    <option value="11" <?=$pre_segment == 11 ? "selected":""; ?>>Discussion (10)</option>
                    <option value="12" <?=$pre_segment == 12 ? "selected":""; ?>>Local needs (15)</option>
                    <option value="13" <?=$pre_segment == 13 ? "selected":""; ?>>Local needs (5)</option>
                    <option value="14" <?=$pre_segment == 14 ? "selected":""; ?>>Congregation bible study</option>
                    <option value="15" <?=$pre_segment == 15 ? "selected":""; ?>>Congregation bible study reading</option>
                    <option value="16" <?=$pre_segment == 16 ? "selected":""; ?>>Enging prayer</option>
                </select>
            </div>

            <div class="form-group">
                <label for="Name">Enter the Name</label>
                <input type="text" id="Name" name="Name" value="<?= htmlentities($pre_name); ?>">
            </div>

            <div class="form-group">
                <label for="title">Enter the Segment title</label>
                <input type="text" id="title" name="title" value="<?= htmlentities($pre_title); ?>">
            </div>

            <div class="form-group">
                <label for="level">Performance Rating</label>
                <select name="level" id="level">
                    <option value="1" <?= $pre_performance == "Excellent" ? "selected" : ""; ?>>Excellent</option>
                    <option value="2" <?= $pre_performance == "Good" ? "selected" : ""; ?>>Good</option>
                    <option value="3" <?= $pre_performance == "Neutral" ? "selected" : ""; ?>>Neutral</option>
                    <option value="4" <?= $pre_performance == "Poor" ? "selected" : ""; ?>>Poor</option>
                    <option value="5" <?= $pre_performance == "Very Poor" ? "selected" : ""; ?>>Very Poor</option>
                    <option value="6" <?= $pre_performance == "Voluntary" ? "selected" : ""; ?>>Voluntary</option>
                    <option value="7" <?= $pre_performance === "Not-rated" ? "selected" : ""; ?>>Not-rated</option>
                </select>
            </div>

            <div class="button-container">
                <input type="submit" value="Update Segment">
                <a href=<?= $back_path ?> class="btn-link">Cancel</a>
            </div>
        </form>


        <?php if (isset($_SESSION['error'])): ?>
            <div class="message error"><?= htmlentities($_SESSION['error']); ?></div>
        <?php unset($_SESSION['error']); endif; ?>

        <?php if (isset($_SESSION['success'])): ?>
            <div class="message success"><?= htmlentities($_SESSION['success']); ?></div>
        <?php unset($_SESSION['success']); endif; ?>
    </div>
</body>
</html>
