<?php
// Check if the user is logged in
if (!isset($_COOKIE['userid'])) {
    header("Location: /");
    exit();
}

$id = "";
include 'fw/db.php';

// Validate and sanitize the ID from the POST data
if (isset($_POST['id']) && strlen(trim($_POST['id'])) !== 0) {
    $id = intval($_POST['id']);

    // Check if the ID exists in the database
    $stmt = executeStatement("SELECT ID, title, state FROM tasks WHERE ID = $id");
    if ($stmt->num_rows === 0) {
        $id = "";
    }
}

require_once 'fw/header.php';

// Check if title and state are set in the POST data
if (isset($_POST['title'], $_POST['state'])) {
    $title = $_POST['title'];
    $state = $_POST['state'];
    $userid = $_COOKIE['userid'];

    // Validate and sanitize title and state
    $title = htmlspecialchars($title);
    $state = htmlspecialchars($state);

    // Perform database operation based on whether $id is set
    if ($id === "") {
        // Insert a new task
        $stmt = executeStatement("INSERT INTO tasks (title, state, userID) VALUES ('$title', '$state', '$userid')");
    } else {
        // Update an existing task
        $stmt = executeStatement("UPDATE tasks SET title = '$title', state = '$state' WHERE ID = $id");
    }

    // Display success message
    echo "<span class='info info-success'>Update successful</span>";
} else {
    // No update was made
    echo "<span class='info info-error'>No update was made</span>";
}

require_once 'fw/footer.php';
?>