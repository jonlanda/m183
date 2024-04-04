<?php
// Check if userid and terms are set in the GET parameters
if (!isset($_GET["userid"]) || !isset($_GET["terms"])){
    die("Not enough information to search");
}

// Get userid and terms from the GET parameters
$userid = $_GET["userid"];
$terms = $_GET["terms"];

// Include database connection
include '../../fw/db.php';

// Execute SQL statement to search for tasks
$stmt = executeStatement("SELECT ID, title, state FROM tasks WHERE userID = $userid AND title LIKE '%$terms%'");

// If there are results, display them
if ($stmt->num_rows > 0) {
    $stmt->bind_result($db_id, $db_title, $db_state);
    while ($stmt->fetch()) {
        echo $db_title . ' (' . $db_state . ')<br />';
    }
}
?>