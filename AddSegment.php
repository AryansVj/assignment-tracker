<?php
session_start();
require_once "model/database.php";
require_once "model/Segments.php";
require_once "model/PeopleManager.php";
require_once "model/Weeks.php";

$_SESSION['source'] = 'AddSegment';

$db = new Database();
$db->connectDB();

$segments = new Segments($db->pdo);
$people = new People($db->pdo);
$weeks = new Weeks($db->pdo);

if ( isset($_POST['Name']) && isset($_POST['segment']) ) {
    if ( (strlen($_POST['Name']) > 0) && (strlen($_POST['segment']) > 0) ) {

        $person_id = $people->getPersonID($_POST['Name']);
        if ($person_id == -1) {
            $_SESSION['error'] = 'Assignment addition failed. Person Name error!';
        }
        
        if ( $segments->addSegment($_POST['segment'], $_POST['title'], $person_id, $_SESSION['week'], $_POST['level'] + 0) == 0 ) {
            $_SESSION['success'] = 'Assignment for ' . $_POST['Name'] . ' was successfully added!';
            
            // Incrementing weekly count by refering to the db
            // $weekly_count = $weeks->getCount($_SESSION['week']);
            // $weeks->updateCount($_SESSION['week'], $weekly_count + 1);

        } else if ( !isset($_SESSION['error']) ) {
            $_SESSION['error'] = 'Assignment addition failed!';
        }

    }
    header("Location: AddSegment.php");
    return;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/stylesAddSegment.css">
    <link rel="stylesheet" href="css/styles.css">
    <title>Manage the segments</title>
</head>
<body>
    <div class="nav">
        <a href="index.php"><div class="home"></div></a>
        <a class="log" href="Log.php">Log</a>
        <a class="people" href="ManagePerson.php">People</a>
        <a class="weeks" href="SelectWeek.php">Weeks</a>
    </div>
    <h1>Segments Dashboard</h1>

    <div class="container">
        <!-- Log Messages -->
        <div class="form-container">
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

            <?php if (isset($_SESSION['success'])): ?>
                <div class="message success" id="log-message">
                    <?= htmlentities($_SESSION['success']) ?>
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
            <?php unset($_SESSION['success']); endif; ?>

            <!-- Form section -->
            <form method="post">
                <p>
                    <label for="segment">Segment</label>
                    <select name="segment" id="segment">
                        <option value="0">Select segment</option>
                        <option value="1">Chair - Sunday</option>
                        <option value="2">Bible talk</option>
                        <option value="3">Watchtower study</option>
                        <option value="4">Watchtower reading</option>
                        <option value="5">Main chair - Mid week</option>
                        <option value="6">Secondary chair - Mid week</option>
                        <option value="7">Treasures from God's word</option>
                        <option value="8">Spiritual gems</option>
                        <option value="10">Discussion (15)</option>
                        <option value="11">Discussion (10)</option>
                        <option value="12">Local needs (15)</option>
                        <option value="13">Local needs (5)</option>
                        <option value="14">Congregation bible study</option>
                        <option value="15">Congregation bible study reading</option>
                        <option value="16">Ending prayer</option>
                    </select>
                </p>
                <p>
                    <label for="Name">Enter the Name </label>
                    <input type="text" id="Name" name="Name" placeholder="Name">
                </p>
                <p>
                    <label for="title">Enter the Segment title </label>
                    <input type="text" id="title" name="title" placeholder="Segment Title">
                </p>
                <p>
                    <label for="level">Performance Rating</label>
                    <select name="level" id="level">
                        <option value="1">Excellent</option>
                        <option value="2">Good</option>
                        <option value="3">Neutral</option>
                        <option value="4">Poor</option>
                        <option value="5">Very Poor</option>
                        <option value="6">Voluntary</option>
                        <option value="7" selected>Not-Rated</option>
                    </select>
                </p>
                <div class="button-container">
                    <input type="submit" value="Add Assignment">
                    <a href="index.php" class="btn-link">Go Back</a>
                </div>
            </form>
        </div>

        <!-- Assignments Section -->
        <?php
            $weekly_segments = $segments->getWeek($_SESSION['date']);
        ?>
        <div class="segments-container">
            <div class="week-details">
                <?php 
                    $date = strtotime(htmlentities($_SESSION['date']));
                ?>
                <p><?= date('F d', $date); ?></p>
                <p><?= date('Y', $date); ?></p>
                <a class="btn-link" href="SelectWeek.php">Change</a>
            </div>
            <div class="segment-section">
                <h4>Sunday Meetings</h4>
                <?php if (!$weekly_segments === false):?>
                    <?php foreach ($weekly_segments as $segment): ?>
                        <?php if ($segment['meeting_id'] <= 3): ?>
                            <div class="segment">
                                <div class="segment-details">
                                    <div class="title">
                                        <?php
                                        if ($segment['title'] == NULL) {
                                            echo "<p><strong>" . htmlentities($segment['segment_name']) . "</strong></p>";
                                        } else {
                                            echo "<p><strong>" . htmlentities($segment['title']) . "</strong></p>";
                                            // echo "<p style=\"font-size: 1em; color: #18497e;\"><i>" . htmlentities($segment['title']) . "</i></p>";
                                        }
                                        ?>
                                        <span class="meeting"> <?=htmlentities($segment['meeting_title'])?> </span>
                                    </div>
                                    <p><?=htmlentities($segment['person_name'])?> </p>
                                    <p>Duration: <?=htmlentities($segment['duration'])?> mins</p>
                                </div>
                                <div class="segment-extra">
                                    <p><?=htmlentities($segment['performance'])?></p>
                                    <a href="EditSegment.php?segment_track_id=<?= htmlentities($segment['segment_track_id'])?>"><button class="edit">Edit</button></a> 
                                    <a href="DeleteSegment.php?segment_track_id=<?=htmlentities($segment['segment_track_id'])?>"><button class="delete">Delete</button></a>
                                </div>
                            </div>
                        <?php endif;?>
                    <?php endforeach;?>
                <?php endif;?>
            </div>
            <div class="segments-section">
                <h4>Mid-Week Meetings</h4>
                <?php if (!$weekly_segments === false):?>
                    <?php foreach ($weekly_segments as $segment): ?>
                        <?php if ($segment['meeting_id'] > 3): ?>
                            <div class="segment">
                                <div class="segment-details">
                                <div class="title">
                                        <?php
                                        if ($segment['title'] == NULL) {
                                            echo "<p><strong>" . htmlentities($segment['segment_name']) . "</strong></p>";
                                        } else {
                                            echo "<p><strong>" . htmlentities($segment['title']) . "</strong></p>";
                                            // echo "<p style=\"font-size: 1em; color: #18497e;\"><i>" . htmlentities($segment['title']) . "</i></p>";
                                        }
                                        ?>
                                        <span class="meeting"> <?=htmlentities($segment['meeting_title'])?> </span>
                                    </div>
                                    <p><?=htmlentities($segment['person_name'])?> </p>
                                    <p>Duration: <?=htmlentities($segment['duration'])?> mins</p>
                                </div>
                                <div class="segment-extra">
                                    <p><?=htmlentities($segment['performance'])?></p>
                                    <a href="EditSegment.php?segment_track_id=<?= htmlentities($segment['segment_track_id'])?>"><button class="edit">Edit</button></a> 
                                    <a href="DeleteSegment.php?segment_track_id=<?=htmlentities($segment['segment_track_id'])?>"><button class="delete">Delete</button></a>
                                </div>
                            </div>
                        <?php endif;?>
                    <?php endforeach;?>
                <?php endif;?>
            </div>
        </div>
    </div>
</body>
</html>
