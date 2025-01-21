<?php
session_start();
require_once "model/database.php";
require_once "model/PeopleManager.php";

$db = new Database();
$db->connectDB();

$people = new People($db->pdo);

if ( isset($_GET['Name']) ) {
    $person = $people->getPersonInfo($_GET['Name']);
}

if ( isset($_POST['delete']) && isset($_POST['Name']) ) {
    $check_id = $people->getPersonID($_POST['Name']);
    if ( $check_id != -1) {
        $res = $people->deletePerson($check_id);
        if ($res == 0) {
            $_SESSION['success'] = $_POST['Name'] . " was successfully deleted";
        } elseif ($res == -1) {
            $_SESSION['error'] = "Failed to delete record. Try again!";
        }
    } else {
        $_SESSION['error'] = "Error: A record with name " . $_POST['Name'] . " does not exists!";
    }
    header("Location: ManagePerson.php");
    return;
}

if ( isset($_POST['update']) && isset($_POST['person_id']) ) {
    $res = $people->editPerson($_POST['person_id'], $_POST['Name'], $_POST['role'], $_POST['group'], $_POST['responsibility']);
    if ($res == -1) {
        $_SESSION['error'] = "Error: Update failed!";
    } elseif ($res == 0) {
        $_SESSION['success'] = "Record update successful";
    }
    header("Location: ManagePerson.php");
    return;
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="css/styles.css">
    <link rel="stylesheet" href="css/stylesManagePerson.css">
</head>
<body>
    <div class="nav">
        <a href="index.php"><div class="home"></div></a>
        <a class="people" href="ManagePerson.php">People</a>
        <a class="weeks" href="SelectWeek.php">Weeks</a>
    </div>
    <h1>Edit or Delete a person record</h1>
    <div class="form-container">
        <!-- Form Section -->
        <form method="post" class="edit">
            <p>
                <label for="Name">Name</label>
                <input type="text" id="Name" name="Name" value="<?= htmlspecialchars($person['name'], ENT_QUOTES, 'UTF-8') ?>" required>
            </p>
            <p>
                <label for="role">Role Title</label>
                <select name="role" id="role" required>
                    <option value="1" <?= $person['role_title'] == "Unbaptized Publisher" ? "selected" : ""; ?>>Unbaptized Publisher</option>
                    <option value="2" <?= $person['role_title'] == "Baptized Publisher" ? "selected" : ""; ?>>Baptized Publisher</option>
                    <option value="3" <?= $person['role_title'] == "Auxiliary Pioneer" ? "selected" : ""; ?>>Auxiliary Pioneer</option>
                    <option value="4" <?= $person['role_title'] == "Regular Pioneer" ? "selected" : ""; ?>>Regular Pioneer</option>
                    <option value="5" <?= $person['role_title'] == "Special Pioneer" ? "selected" : ""; ?>>Special Pioneer</option>
                </select>
            </p>
            <p>
                <label for="group">Group</label>
                <select name="group" id="group" required>
                    <option value="1" <?= $person['group_name'] == "Kundasale" ? "selected" : ""; ?>>Kundasale</option>
                    <option value="2" <?= $person['group_name'] == "Digana" ? "selected" : ""; ?>>Digana</option>
                </select>
            </p>
            <p>
                <label for="responsibility">Responsibility</label>
                <select name="responsibility" id="responsibility" required>
                    <option value="1" <?= $person['responsibility'] == "None" ? "selected" : ""; ?>>None</option>
                    <option value="2" <?= $person['responsibility'] == "Ministerial Servant" ? "selected" : ""; ?>>Ministerial Servant</option>
                    <option value="3" <?= $person['responsibility'] == "Elder" ? "selected" : ""; ?>>Elder</option>
                </select>
            </p>
            <input type="text" name="person_id" value=<?=$person['person_id']?> hidden>
            <div class="button-container">
                <input class="update" type="submit" name="update" value="Update">
                <input class="delete" type="submit" name="delete" value="Delete">
                <a href="ManagePerson.php" class="btn-link">Cancel</a>
            </div>
        </form>
    </div>
</body>
</html>