<?php
// Function to execute SQL statement
function executeStatement($statement){
    // Get database connection
    $conn = getConnection();
    
    // Prepare and execute the statement
    $stmt = $conn->prepare($statement);
    $stmt->execute();
    $stmt->store_result();
    
    // Return the statement
    return $stmt;
}

// Function to establish database connection
function getConnection()
{
    // Include configuration file
    $root = realpath($_SERVER["DOCUMENT_ROOT"]);
    require_once "$root/config.php";

    // Create database connection
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    return $conn;
}
?>