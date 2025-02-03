<?php
// To set the mid week meeting day (0-sunday)
$_SESSION['dayof_midweek'] = 4;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home</title>
    <link rel="stylesheet" href="css/styles.css">
    <style>
        body {
            background-image: linear-gradient(150deg, #f5f5f5ff, #f5f5f575 70%), url('assets/8495460_18933.jpg');
            background-size: cover;
            color: #333;
            align-items: center;
            height: 80vh;
        }

        .container {
            text-align: center;
        }

        .nav-container {
            text-align: center;
            padding: 10px 30px;
            width: 40%;
            margin: 40px auto;
            display: flex;
            justify-content: space-between;
            background-color: #ffffff88 ;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
            border-radius: 50px;
            transition: box-shadow, background-color 0.2s;
        }

        .nav-container:hover {
            background-color: #ffffff ;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);            
        }

        h1.main {
            font-size: 50px;
            margin-top: 20vh;
            margin-bottom: 10px;
        }

        p.congregation {
            border: 0px dotted #111111;
            border-radius: 5px;
            padding: 5px 15px;
            width: fit-content;
            margin: 10px auto;
            background-color: #777;
            color: #ffffff;
        }

        .details {
            padding: 20px;
            border-radius: 15px;
            width: 25%;
            margin: 20px auto;
        }

        p {
            font-size: 16px;
            color: #555;
            margin: 5px;
        }

        a button {
            width: 100%;
            padding: 10px 15px;
            margin: 10px 0;
            font-size: 16px;
            background-color: #007bff;
            color: #fff;
            border: 1px solid #fff;
            border-radius: 20px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        a button:hover {
            background-color: #0d60b9;
            /* border: 1px solid #0d60b9; */
        }

        a {
            text-decoration: none;
        }
    </style>
</head>
<body>
    <div class="nav">
        <a href="index.php"><div class="home"></div></a>
        <a class="log" href="Log.php">Log</a>
        <a class="people" href="ManagePerson.php">People</a>
        <a class="weeks" href="SelectWeek.php">Weeks</a>
    </div>
    <div class="container">
        <h1 class="main">Welcome to the JW Assignment Tracker!</h1>
        <p class="congregation">Kandy Congregation</p>
        <div class="nav-container">
            <a href="SelectWeek.php">
                <button type="button">Weekly Dashboard</button>
            </a>
            <a href="Log.php">
                <button type="button">Assignment Log</button>
            </a>
            <a href="ManagePerson.php">
                <button type="button">Manage People</button>
            </a>
        </div>
        <div class="details">
            <p><strong>Location: </strong>Gatambe, Kandy</p>
            <p><strong>Brotherhood: </strong>150</p>
            <p><strong>Groups: </strong>5</p>
            <p><strong>Meeting days: </strong>Sunday and Thursday</p>
        </div>
    </div>
</body>
</html>
