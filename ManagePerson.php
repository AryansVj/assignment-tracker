<?php
session_start();
require_once "model/database.php";
require_once "model/PeopleManager.php";

$db = new Database();
$db->connectDB();

$people = new People($db->pdo);


if ( isset($_POST['Add']) && isset($_POST['Name']) && ($_POST['role'] != 0) && ($_POST['group'] != 0) && ($_POST['Name'] != "Name") ) {
    
    $check_id = $people->getPersonID($_POST['Name']);
    
    if ($check_id == -1) {
        $res = $people->addPerson($_POST['Name'], $_POST['role'] + 0, $_POST['group'] + 0,  $_POST['responsibility'] + 0);
        if ($res == 0) {
            $_SESSION['success'] = $_POST['Name'] . " was successfully added";
        } else {
            $_SESSION['error'] = "Error adding the record! Try again";
        }
    } else {
        $_SESSION['error'] = "Error: A record with name " . $_POST['Name'] . " already exists!";
    }
    header("Location: ManagePerson.php");
    return;
} elseif ( isset($_POST['Name']) ) {
    $_SESSION['error'] = "Error: One or more fields you entered are invalid!";
    header("Location: ManagePerson.php");
    return;
} 

// Search section using GET
if ( isset($_GET['search']) ) {
    if ( isset($_GET['search-name']) || ($_GET['search-role'] > 0) || ($_GET['search-group'] > 0) || ($_GET['search-responsibility'] > 0) ) {
        $search = $people->searchPeople($_GET['search-name'], $_GET['search-role'] + 0, $_GET['search-group'] + 0, $_GET['search-responsibility'] + 0);
        if ($search == -1) {
            $_SESSION['error'] = "Search failed!";
        } elseif (sizeof($search) == 0) {
            $_SESSION['error'] = "No results found for the given query";
        } 
    }
} elseif ( isset($_GET['clear']) ) {
    header("Location: ManagePerson.php");
    return;
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/stylesManagePerson.css">
    <link rel="stylesheet" href="css/styles.css">
    <title>Add Person</title>
</head>
<body>
    <div class="nav">
        <a href="index.php"><div class="home"></div></a>
        <a class="people" href="ManagePerson.php">People</a>
        <a class="weeks" href="SelectWeek.php">Weeks</a>
    </div>
    <h1>Manage People</h1>
    <div class="container">
        <div class="form-container">
            <!-- Log Message Section -->
            <?php if (isset($_SESSION['error'])): ?>
                <div class="message error" id="log-message">
                    <?= htmlentities($_SESSION['error']) ?>
                </div>
            <?php unset($_SESSION['error']); endif; ?>

            <?php if (isset($_SESSION['success'])): ?>
                <div class="message success" id="log-message">
                    <?= htmlentities($_SESSION['success']) ?>
                </div>
            <?php unset($_SESSION['success']); endif; ?>

            <!-- Form Section -->
            <form method="post" class="add">
                <p>
                    <label for="Name">Enter the Name</label>
                    <input type="text" id="Name" name="Name" placeholder="Name" required>
                </p>
                <p>
                    <label for="role">Role Title</label>
                    <select name="role" id="role" required>
                        <option value="0" selected>Select Role</option>
                        <option value="1">Unbaptized Publisher</option>
                        <option value="2">Baptized Publisher</option>
                        <option value="3">Auxiliary Pioneer</option>
                        <option value="4">Regular Pioneer</option>
                        <option value="5">Special Pioneer</option>
                    </select>
                </p>
                <p>
                    <label for="group">Group</label>
                    <select name="group" id="group" required>
                        <option value="0" selected>Select Group</option>
                        <option value="1">Kundasale</option>
                        <option value="2">Digana</option>
                    </select>
                </p>
                <p>
                    <label for="responsibility">Responsibility</label><br>
                    <input type="radio" name="responsibility" value="1" checked> None
                    <input type="radio" name="responsibility" value="2"> Ministerial Servant
                    <input type="radio" name="responsibility" value="3"> Elder
                </p>
                <div class="button-container">
                    <input class="add" type="submit" name="Add" value="Add Person">
                    <a href="index.php" class="btn-link">Home</a>
                </div>
            </form>
        </div>

        <div class="display-container">
            <form class="search" method="get">
                <p>
                    <input type="text" name="search-name" placeholder="Enter name to search">
                </p>
                <div class="search-select">
                    <p>
                        <select name="search-role" id="search-role" required>
                            <option value="0" selected>Role</option>
                            <option value="1">Unbaptized Publisher</option>
                            <option value="2">Baptized Publisher</option>
                            <option value="3">Auxiliary Pioneer</option>
                            <option value="4">Regular Pioneer</option>
                            <option value="5">Special Pioneer</option>
                        </select>
                    </p>
                    <p>
                        <select name="search-group" id="search-group" required>
                            <option value="0" selected>Group</option>
                            <option value="1">Kundasale</option>
                            <option value="2">Digana</option>
                        </select>
                    </p>
                    <p>
                        <select name="search-responsibility" id="search-responsibility">
                            <option value="0" selected>Responsibility</option>
                            <option value="1">Brother/Sister</option>
                            <option value="2">Ministerial Servant</option>
                            <option value="3">Elder</option>
                        </select>
                    </p>
                    <input class="clear" type="submit" name="clear" value="Clear">
                    <input class="search" type="submit" name="search" value="Search">
                </div>    
            </form>
            <hr>
            <?php if (isset($_GET['search'])): ?>
                <div class="display">
                    <?php foreach($search as $row): ?>
                        <div class="search-row">
                            <p>
                                <strong><?= $row['name']?></strong><br>
                                <?= $row['role_title']?>, <?= $row['group_name']?>
                            </p>
                            <?php if ($row['responsibility'] != "None"): ?>
                                <p class="resp"><?= $row['responsibility']?></p>
                            <?php endif;?>
                            <div class="edit">
                                <?php $view_path = str_replace(' ', '+', "PersonalLog.php?Name=" . $row['name']); ?>
                                <?php $edit_path = str_replace(' ', '+', "EditPerson.php?Name=" . $row['name']); ?>
                                <a href=<?= $view_path ?>><button>View</button></a>
                                <a href=<?= $edit_path ?>><button>Edit</button></a>
                            </div>
                        </div>
                    <?php endforeach;?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
