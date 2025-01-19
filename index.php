<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f5f5f5;
            color: #333;
            align-items: center;
            height: 100vh;
        }

        .container {
            text-align: center;
        }

        .nav-container {
            text-align: center;
            padding: 10px;
            width: 40%;
            margin: auto;
            display: flex;
            justify-content: space-around;
        }

        h1 {
            font-size: 50px;
            margin-top: 20vh;
            margin-bottom: 10px;
        }

        p.congregation {
            border: 2px dotted #111111;
            border-radius: 15px;
            padding: 5px 15px;
            width: fit-content;
            margin: 10px auto;
        }

        .details {
            background-color: #ffffff;
            padding: 20px;
            border-radius: 15px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            width: 25%;
            margin: 20px auto;
        }

        p {
            font-size: 16px;
            color: #555;
        }

        a button {
            width: 100%;
            padding: 10px 15px;
            margin: 10px 0;
            font-size: 16px;
            background-color: #007bff;
            color: #fff;
            border: none;
            border-radius: 10px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        a button:hover {
            background-color: #0056b3;
        }

        a {
            text-decoration: none;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Welcome to the JW Assignment Tracker!</h1>
        <p class="congregation">Kandy Congregation</p>
        <div class="nav-container">
            <a href="SelectWeek.php">
                <button type="button">Weekly Dashboard</button>
            </a>
            <a href="PersonalLog.php">
                <button type="button">Personal Log</button>
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
