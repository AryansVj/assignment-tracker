

<?php
session_start();
require_once $_SERVER['DOCUMENT_ROOT'] . "/model/database.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/model/Assignment.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/model/Segments.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/model/PeopleManager.php";

$db = new Database();
$db->connectDB();

$assignments = new Assignment($db->pdo);
$segments = new Segments($db->pdo);
$people = new People($db->pdo);

if (isset($_SESSION['week'])) {
    $assignment_list_w = $assignments->getWeek($_SESSION['date']);
    $segment_list_w = $segments->getWeek($_SESSION['date']);

    $sunday_chair = $segments->selectSegment($_SESSION['week'], 1);
    $midweek_chair_main = $segments->selectSegment($_SESSION['week'], 5);
    $midweek_chair_sec = $segments->selectSegment($_SESSION['week'], 6);
    $midweek_endprayer = $segments->selectSegment($_SESSION['week'], 16);

    $sunday_date = strtotime($_SESSION['date']);
    $midweek_date = strtotime($_SESSION['mid-date']);
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Weekly Schedule</title>
    <style>
        * {
            box-sizing: border-box;
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
        }

        body {
            background-color: #f4f4f4;
            padding: 20px;
        }

        .heading {
            text-align: center;
            margin-bottom: 20px;
        }

        .container {
            display: flex;
            gap: 20px;
            justify-content: center;
        }

        .column {
            flex: 1;
            background: white;
            padding: 15px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .details {
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 10px;
            min-height: 80px;
        }

        .sub-details {
            padding: 10px; 
        }

        .card-container {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .card {
            background: #fff;
            padding: 10px;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
            border-left: 5px solid #005a9c;
        }
        
        .sunday .card {
            border-left: 5px solid #ecb610;
        }

        .card-head {
            display: flex;
            justify-content: space-between;
        }

        .assignment-card {
            border-left-color: #28a745;
        }

        .segment-card {
            border-left-color: #dc3545;
        }

        .chair span {
            font-weight: bold;
        }

        @media (max-width: 768px) {
            .container {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>

    <div class="heading">
        <h1>Weekly Schedule</h1>
    </div>

    <div class="container">
        <!-- Sunday Column -->
        <div class="column sunday">
            <div class="details">
                <p><?= date("l", $sunday_date) . " " . date("M d, Y", $sunday_date) ?></p>
                <p class="chair">Chair: <span><?= $sunday_chair['person_name'] ?></span></p>
            </div>
            <div class="card-container">
                <?php foreach ($segment_list_w as $segment): ?>
                    <?php if ($segment['segment_id'] > 1 && $segment['segment_id'] < 5): ?>
                        <div class="card segment-card">
                            <div class="card-head">
                                <p><strong><?= $segment['segment_name'] ?></strong></p>
                                <p><?= $segment['duration'] ?> mins</p>
                            </div>
                            <p><i><?= $segment['title'] ?></i></p>
                            <p><?= $segment['person_name'] ?></p>
                        </div>
                    <?php endif; ?>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Midweek Column -->
        <div class="column mid-week">
            <div class="details">
                <p><?= date("l", $midweek_date) . " " . date("M d, Y", $midweek_date) ?></p>
                <p class="chair">Main Chair: <span><?= $midweek_chair_main['person_name'] ?></span></p>
                <p class="chair">Secondary Chair: <span><?= $midweek_chair_sec['person_name'] ?></span></p>
            </div>
            <div class="card-container">
                <?php foreach ($segment_list_w as $segment): ?>
                    <?php if ($segment['segment_id'] > 6 && $segment['segment_id'] < 16): ?>
                        <div class="card segment-card">
                            <div class="card-head">
                                <p><strong><?= $segment['segment_name'] ?></strong></p>
                                <p><?= $segment['duration'] ?> mins</p>
                            </div>
                            <p><i><?= $segment['title'] ?></i></p>
                            <p><?= $segment['person_name'] ?></p>
                        </div>
                    <?php endif; ?>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Assignments Column -->
        <div class="column">
            <div class="details">
                <p>Weekly Assignments</p>
            </div>
            <p class="sub-details">Main Hall Assignments</p>
            <div class="card-container">
                <?php foreach ($assignment_list_w as $assignment): ?>
                    <?php if ($assignment['hall'] == 1): ?>
                        <div class="card assignment-card">
                            <div class="card-head">
                                <p><strong><?= $assignment['category_title'] ?></strong></p>
                                <p><?= $assignment['duration'] ?> mins</p>
                            </div>
                            <p><?= $assignment['person_name'] ?>
                                <?php if ($assignment['assistant_name'] != "None") echo " <i>with " . $assignment['assistant_name'] . "</i>"; ?>
                            </p>
                        </div>
                    <?php endif; ?>
                <?php endforeach; ?>
            </div>

            <p class="sub-details">Second Hall Assignments</p>
            <div class="card-container">
                <?php foreach ($assignment_list_w as $assignment): ?>
                    <?php if ($assignment['hall'] == 2): ?>
                        <div class="card assignment-card">
                            <div class="card-head">
                                <p><strong><?= $assignment['category_title'] ?></strong></p>
                                <p><?= $assignment['duration'] ?> mins</p>
                            </div>
                            <p><?= $assignment['person_name'] ?>
                                <?php if ($assignment['assistant_name'] != "None") echo " <i>with " . $assignment['assistant_name'] . "</i>"; ?>
                            </p>
                        </div>
                    <?php endif; ?>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

</body>
</html>
