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
    <link rel="stylesheet" href="/css/styles.css">
    <title>Weekly Schedule</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap');

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

        .head {
            font-family: "Poppins", serif;
            display: flex;
            justify-content: space-between;
            padding: 20px;
            margin-left: 10px;
            margin-right: 10px;
            margin-bottom: 5px;
            border-radius: 5px;
            align-items: center;
            background-color: #333;
            color: white;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
        }

        .head h1 {
            margin-top: 0;
            margin-bottom: 0;
            font-size: 2em;
            text-align: left;
            text-transform: uppercase;
        }

        .head .week-calendar span {
            color: #ddd;
            border: 1px dotted;
            border-radius: 3px;
            padding: 5px;
            margin-right: 5px;
        }

        .head .week-calendar .deco {
            background-color: #ffffff35;
        }

        .container {
            display: flex;
            gap: 20px;
            justify-content: center;
        }

        .column {
            flex: 1;
            padding: 10px;
        }

        div.seperator {
            border-right: 1px solid rgba(0, 0, 0, 0.25);
            width: 0;
            margin: 10px 0;
            padding: 0;
        }

        .details {
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 10px;
            min-height: 100px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
        }

        .details p {
            margin-bottom: 5px;
        }

        .details span {
            /* background-color: #e1e1e1; */
            padding: 3px 5px;
            border-radius: 5px;
        }

        .details .heading {
            font-family: "Poppins", serif;
            text-transform: uppercase;
            font-size: 2em;
            font-weight: 500;
        }

        .sunday .details {
            background-color: #38c73c;
        }

        .mid-week .details {
            background-color: #4ca1b1;
        }
        
        .assignments .details {
            background-color: #ecb610;
        }

        .sub-details {
            padding: 5px; 
            margin-top: 10px;
        }

        hr {
            margin: 0 5px 5px;
            margin-bottom: 15px;
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
        
        .sunday .segment-card {
            border-left: 5px solid #38c73c;
        }

        .card-head {
            display: flex;
            justify-content: space-between;
        }

        .assignment-card {
            border-left-color: #ecb610;
        }

        .segment-card.third {
            border-left-color: #4ca1b1;
        }

        .segment-card.fifth {
            border-left-color: #be1728;
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
    <div class="nav">
        <a href="/index.php"><div class="home"></div></a>
        <a class="log" href="/Log.php">Log</a>
        <a class="people" href="/viewPeople/ManagePeople.php">People</a>
        <a class="weeks" href="/SelectWeek.php">Weeks</a>
    </div>

    <div class="head">
        <h1>Weekly Schedule</h1>
        <div class="week-calendar">
            <span><?= date("D M d", ($sunday_date - 6*24*3600))?></span>
            <span class=<?=($_SESSION['dayof_midweek'] == 2)?"deco":"";?>><?= date("d", ($sunday_date - 5*24*3600))?></span>
            <span class=<?=($_SESSION['dayof_midweek'] == 3)?"deco":"";?>><?= date("d", ($sunday_date - 4*24*3600))?></span>
            <span class=<?=($_SESSION['dayof_midweek'] == 4)?"deco":"";?>><?= date("d", ($sunday_date - 3*24*3600))?></span>
            <span class=<?=($_SESSION['dayof_midweek'] == 5)?"deco":"";?>><?= date("d", ($sunday_date - 2*24*3600))?></span>
            <span><?= date("d", ($sunday_date - 1*24*3600))?></span>
            <span class="deco"><?= date("D M d", ($sunday_date))?></span>
        </div>
    </div>

    <div class="container">
        <!-- Sunday Column -->
        <div class="column sunday">
            <div class="details">
                <p class="heading"><?= date("l", $sunday_date) . " " . date("M d, Y", $sunday_date) ?></p>
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

        <div class="seperator"></div>

        <!-- Midweek Column -->
        <div class="column mid-week">
            <div class="details">
                <p class="heading"><?= date("l", $midweek_date) . " " . date("M d, Y", $midweek_date) ?></p>
                <p class="chair">Main Chair: <span><?= $midweek_chair_main['person_name'] ?></span></p>
            </div>
            <div class="card-container">
                <?php foreach ($segment_list_w as $segment): ?>
                    <?php if ($segment['segment_id'] > 6 && $segment['segment_id'] < 16): ?>
                        <div class="card segment-card <?=($segment['segment_id'] > 9)?"fifth":"third"?>">
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
        <div class="column assignments">
            <div class="details">
                <p class="heading">Weekly Assignments</p>
                <p class="chair">Secondary Chair: <span><?= $midweek_chair_sec['person_name'] ?></span></p>
            </div>
            <p class="sub-details">Main Hall</p>
            <hr>
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

            <p class="sub-details">Second Hall</p>
            <hr>
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
