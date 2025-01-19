<?php
session_start();
require_once "model/database.php";
require_once "model/Assignment.php";
require_once "model/PeopleManager.php";

$db = new Database();
$db->connectDB();

$assignments = new Assignment($db->pdo);
$people = new People($db->pdo);

$back_path = $_SESSION['source'] . ".php";

if ( !isset($_GET['assignment_id']) ) {
    $_SESSION['error'] = 'Assignment ID not found!';
    return;
} else {
    $assignment = $assignments->getAssignment($_GET['assignment_id']);
    if ($assignment == 0) {
        $_SESSION['error'] = 'Assignment not found!';
    } else {
        $_SESSION['assignment_id'] = $_GET['assignment_id'];

        $pre_name = $assignment['person_name'];
        $pre_category = $assignment['category_title'];
        $pre_assistant = $assignment['assistant_name'];
        $pre_status = $assignment['status_descriptor'];
        $pre_performance = $assignment['levels'];
        $pre_hall = $assignment['hall'];
        $pre_date = $assignment['weekly_date'];
    }
}

if ($_SESSION['source'] == 'individual') {
    $back_path .= "?Name=" . $assignment['person_name'];
}

if ( isset($_POST['Name']) && isset($_POST['category']) ) {
    if ( (strlen($_POST['Name']) > 0) && (strlen($_POST['category']) > 0) ) {
        $person_id = $people->getPersonID($_POST['Name']);
        if ($person_id == -1) {
            $_SESSION['error'] = 'Assignment addition failed. Person Name error!';
        }
        if ($_POST['assistant'] === "None") {
            $assistant_id = 5;
        } else {
            $assistant_id = $people->getPersonID($_POST['assistant']);
        }

        if ( $assignments->updateAssignment($_SESSION['assignment_id'], $person_id, $_POST['category'], $assistant_id, $_POST['status'] + 0, $_POST['level'] + 0, $_POST['hall'] + 0) == 0) {
            $_SESSION['success'] = 'Assignment was successfully updated! (ID: '. $_SESSION['assignment_id'] . ')';
            
        } else if ( !isset($_SESSION['error']) ) {
            $_SESSION['error'] = 'Assignment update failed!';
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
    <link rel="stylesheet" href="css/stylesEdit.css">
    <title>Edit Assignment</title>
</head>
<body>
    <h1>Edit the Assignment</h1>
    <div class="container">
        <div class="sub-data">
            <p>Assignment ID: <?= htmlentities($_SESSION['assignment_id']); ?></p>
            <p>Date: <?= htmlentities($pre_date); ?></p>
        </div>

        <form method="post">
            <div class="form-group">
                <label for="Name">Enter the Name</label>
                <input type="text" id="Name" name="Name" value="<?= htmlentities($pre_name); ?>">
            </div>

            <div class="form-group">
                <label for="category">Assignment</label>
                <select name="category" id="category">
                    <option value="0" <?= $pre_category == "Select Category" ? "selected" : ""; ?>>Select Category</option>
                    <option value="1" <?= $pre_category == "Bible Reading" ? "selected" : ""; ?>>Bible Reading</option>
                    <option value="2" <?= $pre_category == "Talk" ? "selected" : ""; ?>>Talk</option>
                    <option value="3" <?= $pre_category == "Start Conversation (1)" ? "selected" : ""; ?>>Start Conversation (1)</option>
                </select>
            </div>

            <div class="form-group">
                <label for="assistant">Assistant Name</label>
                <input type="text" name="assistant" id="assistant" value="<?= htmlentities($pre_assistant); ?>">
            </div>

            <div class="form-group">
                <label>Hall</label>
                <div class="radio-group">
                    <label><input type="radio" name="hall" id="hall1" value="1" <?= $pre_hall == 1 ? "checked" : ""; ?>> Hall 1</label>
                    <label><input type="radio" name="hall" id="hall2" value="2" <?= $pre_hall == 2 ? "checked" : ""; ?>> Hall 2</label>
                </div>
            </div>

            <div class="form-group">
                <label for="status">Assignment Status</label>
                <select name="status" id="status">
                    <option value="1" <?= $pre_status == "Pending" ? "selected" : ""; ?>>Pending</option>
                    <option value="2" <?= $pre_status == "Completed" ? "selected" : ""; ?>>Completed</option>
                    <option value="3" <?= $pre_status == "Missed" ? "selected" : ""; ?>>Missed</option>
                </select>
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
                <input type="submit" value="Update Assignment">
                <a href=<?= $back_path ?> class="cancel-btn">Cancel</a>
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
