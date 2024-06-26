<?php
require_once 'config.php';

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['username']) && isset($_POST['password'])) {
    // Get username and password from the form
    $username = $_POST['username'];
    $password = $_POST  ['password'];
    
    // Connect to the database
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    // Prepare SQL statement to retrieve user from database
    $stmt = $conn->prepare("SELECT id, username, password FROM users WHERE username=?");

    // Bind parameters and execute the statement
    $stmt->bind_param("s", $username);
    $stmt->execute();

    // Store the result
    $stmt->store_result();

    // Check if username exists
    if ($stmt->num_rows > 0) {
        // Bind the result variables
        $stmt->bind_result($db_id, $db_username, $db_password);
        
        // Fetch the result
        $stmt->fetch();

        // Verify the password
        if (password_verify($password, $db_password)) {
            // Password is correct, store username in session
            setcookie("username", $username, -1, "/"); // 86400 = 1 day
            setcookie("userid", $db_id, -1, "/"); // 86400 = 1 day
            // Redirect to index.php
            header("Location: index.php");
            exit();
        } else {
            // Password is incorrect
            echo "Incorrect password";
        }
    } else {
        // Username does not exist
        echo "Username does not exist";
    }

    // Close statement
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
</head>
<body>
    <h2>Login</h2>
    <form action="<?php $_SERVER["PHP_SELF"]; ?>" method="post">
        <label for="username">Username:</label>
        <input type="text" id="username" name="username" required><br><br>
        <label for="password">Password:</label>
        <input type="password" id="password" name="password" required><br><br>   
        <button type="submit">Login</button>
        <p>Noch keinen Account? <a href="register.php">Registrieren</a></p>
    </form>
</body>
</html>
