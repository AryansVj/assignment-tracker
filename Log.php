<?php
session_start();
require_once "model/database.php";
require_once "model/Assignment.php";
require_once "model/Segments.php";
require_once "model/PeopleManager.php";

$_SESSION['source'] = 'Log';

$db = new Database();
$db->connectDB();

$assignments = new Assignment($db->pdo);
$segments = new Segments($db->pdo);
$people = new People($db->pdo);
$res = 0;

if ( isset($_GET['Name']) && (strlen($_GET['Name']) > 0)) {
    $person_name = htmlentities($_GET['Name']);
    if ( !empty($_GET['date-from']) && !empty($_GET['date-to']) ) {
        if (strtotime($_GET['date-from']) > strtotime($_GET['date-to'])) {
            $assignment_list = -2;
        } else {
            $assignment_list = $assignments->getBoundByDatePerson(htmlentities($_GET['date-from']), htmlentities($_GET['date-to']), $person_name);
            $segment_list = $segments->getBoundByDatePerson(htmlentities($_GET['date-from']), htmlentities($_GET['date-to']), $person_name);
        }
    } else {
        $assignment_list = $assignments->getByIndividual($person_name);
        $segment_list = $segments->getByIndividual($person_name);
    }
    
    $person_info = $people->getPersonInfo($person_name);
    $assignment_count_pp = NULL;
    $segment_count_pp = NULL;
    
    if ($person_info == false) {
        $_SESSION['error'] = "Person named " . $person_name . " was not found!";
        unset($assignment_list);
    } else if ( ($assignment_list == false) && ($segment_list == false) ) {
        $_SESSION['error'] = "No assignments found for the given query";
    } else if ($assignment_list == -2) {
        $_SESSION['error'] = "Invalid range of dates";
        unset($assignment_list);
        unset($segment_list);
    } else if ( ($assignment_list == -1) || ($segment_list == -1) ) {
        $_SESSION['error'] = "Error fetching records";
        unset($assignment_list);
        unset($segment_list);
    } else {
        $assignment_count_pp = $people->getAssignmentCount($person_name);
        $segment_count_pp = $people->getSegmentCount($person_name);
    }

} elseif ( !empty($_GET['date-from']) && !empty($_GET['date-to']) ) {
    if (strtotime($_GET['date-from']) > strtotime($_GET['date-to'])) {
        $_SESSION['error'] = "Invalid range of dates";
    } else {
        $assignment_list = $assignments->getBoundByDate(htmlentities($_GET['date-from']), htmlentities($_GET['date-to']));
        $segment_list = $segments->getBoundByDate(htmlentities($_GET['date-from']), htmlentities($_GET['date-to']));

        if ( ($assignment_list == false) && ($segment_list == false) ) {
            $_SESSION['error'] = "No assignments found for the given dates";
        } else if ( ($assignment_list == -1) || ($segment_list == -1) ) {
            $_SESSION['error'] = "Error fetching records";
            unset($assignment_list);
            unset($segment_list);
        }
    }
    
} else if (isset($_GET['search'])) {
    $_SESSION['error'] = "Invalid query";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/stylesLog.css">
    <link rel="stylesheet" href="css/styles.css">
    <title>Record by Individual</title>
</head>
<body>
    <div class="nav">
        <a href="index.php"><div class="home"></div></a>
        <a class="log" href="Log.php">Log</a>
        <a class="people" href="ManagePerson.php">People</a>
        <a class="weeks" href="SelectWeek.php">Weeks</a>
    </div>
    <h1>Search for Assignment Log</h1>
    <form method="get">
        <input type="text" name="Name" id="Name" placeholder="Enter the name to view the log" value="<?php if (isset($_GET['Name'])) echo urldecode($_GET['Name']); else echo "";?>"><br>
        <div class="date-input">
            <label for="date-from">From</label>
            <input type="date" name="date-from" value="<?php if (!empty($_GET['date-from'])) echo urldecode($_GET['date-from']);?>" id="date-from">
            <label for="date-to">To</label>
            <input type="date" name="date-to" value="<?php if (!empty($_GET['date-to'])) echo urldecode($_GET['date-to']);?>" id="date-to">
        </div>
        <div class="button-container">
            <input type="submit" name="search" value="Search">
            <a href="Log.php" class="btn-link">Clear</a>
        </div>
    </form>
    
    <?php if (isset($_SESSION['success'])): ?>
        <div class="message success" id="log-message"><?= htmlentities($_SESSION['success']); ?></div>
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
        <?php unset($_SESSION['success']); ?>
        
        <?php elseif (isset($_SESSION['error'])): ?>
            <div class="message error" id="log-message"><?= htmlentities($_SESSION['error']); ?></div>
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
        <?php unset($_SESSION['error']); ?>
        <?php endif; ?>
        
        <?php if (isset($_GET['Name']) && (isset($assignment_count_pp) || isset($segment_count_pp)) && ($person_info != false)): ?>
        <div class="person-detail">
            <div>
                <p class="name"> <?= $person_info['name'] ?> </p>
                <p><?= $person_info['role_title'] . ", " . $person_info['group_name'] . " Group" ?></p>
            </div>
            <p><?= "No. of Assignments: " . $assignment_count_pp . " | Segments: " . $segment_count_pp ?></p>
        </div>
        <?php endif; ?>

    <div class="separator"></div>

    <?php if ( isset($assignment_list) ): ?>
        <div class="assignments-container">
            <?php
                $grouped_assignments = [];
                foreach ($assignment_list as $assignment) {
                    $month = date('F Y', strtotime($assignment['week_date']));
                    $grouped_assignments[$month][] = $assignment;
                }
                foreach ($segment_list as $segment) {
                    $month = date('F Y', strtotime($segment['week_date']));
                    $grouped_assignments[$month][] = $segment;
                }

                foreach ($grouped_assignments as $month => $assignments) {
                    usort($assignments, function ($a, $b) {
                        return strtotime($a['week_date']) - strtotime($b['week_date']);
                    });
                    $grouped_assignments[$month] = $assignments; // Reassign after sorting
                }                

                foreach ($grouped_assignments as $month => $assignments): ?>
                    <div class="month-group">
                        <div class="month-heading"><?= $month; ?></div>
                        <?php foreach ($assignments as $row): ?>
                            <?php if (isset($row['segment_track_id'])): ?>
                                <div class="assignment-card segment-card">
                                    <div class="date-box">
                                        <div class="month"><?= date('M', strtotime($row['week_date'])); ?></div>
                                        <div class="day"><?= date('d', strtotime($row['week_date'])); ?></div>
                                    </div>
                                    <div class="details">
                                        <strong class="category"><?= htmlentities($row['segment_name']); ?></strong>
                                        
                                        <?php 
                                        if (strlen($row['title']) > 1) {
                                            echo '<br>' . htmlentities($row['title']);
                                        }
                                        if (strlen($_GET['Name']) < 1) {
                                            echo '<br><strong>' . htmlentities($row['person_name']) . '</strong>';
                                        }
                                        ?>
                                    </div>
                                    <div class="status">
                                        <strong>Rating:</strong> <?= htmlentities($row['levels']); ?>
                                    </div>
                                    <div class="edit">
                                    <a href= <?= "EditSegment.php?segment_track_id=" . htmlentities($row['segment_track_id']) ?>><button class="aedit">Edit</button></a>
                                    <a href= <?= "DeleteSegment.php?segment_track_id=" . htmlentities($row['segment_track_id']) ?>><button class="adelete">Delete</button></a>
                                    </div>
                                </div>
                                
                            <?php elseif (isset($row['assignment_id'])): ?>
                                <div class="assignment-card">
                                    <div class="date-box">
                                        <div class="month"><?= date('M', strtotime($row['week_date'])); ?></div>
                                        <div class="day"><?= date('d', strtotime($row['week_date'])); ?></div>
                                    </div>
                                    <div class="details">
                                        <strong class="category"><?= htmlentities($row['category_title']); ?></strong><br>
                                        <?php if (strlen($_GET['Name']) < 1) {
                                            echo '<strong>' . htmlentities($row['person_name']) . '</strong>';
                                            echo ($row['assistant_name'] != "None") ? (' with ' . htmlentities($row['assistant_name']) . '<br>'): '<br>';
                                        } else {
                                            echo 'Assistant:  ' . htmlentities($row['assistant_name']) . '<br>';
                                        }
                                        ?>
                                        <!-- Assistant: <?= htmlentities($row['assistant_name']); ?><br> -->
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
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
    <?php unset($_GET['Name']); ?>
</body>
</html>
