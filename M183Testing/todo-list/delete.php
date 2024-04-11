<?php
require_once 'config.php';

// Check if the task ID is provided
if (isset($_GET['id'])) {
    // Get task ID from the request
    $taskId = $_GET['id'];

    // Connect to the database
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Prepare SQL statement to delete task
    $stmt = $conn->prepare("DELETE FROM tasks WHERE id=?");
    $stmt->bind_param("i", $taskId); // 'i' specifies the variable type => 'integer'

    // Execute the statement
    $stmt->execute();

    // Close statement and connection
    $stmt->close();
    $conn->close();
}

// Redirect to task list
header("Location: index.php");
exit();
?>