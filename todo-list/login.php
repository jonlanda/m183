<?php
require_once 'config.php';

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['username'], $_POST['password'])) {
    $username = isset($_POST['username']);
    $password = isset($_POST['password']);

    // Connect to the database
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Check for brute force protection
    $checkAttempt = $conn->prepare("SELECT failed_login_attempts, last_failed_login FROM users WHERE username=?");
    $checkAttempt->bind_param("s", $username);
    $checkAttempt->execute();
    $checkAttempt->bind_result($failed_attempts, $last_attempt);
    $checkAttempt->fetch();
    $checkAttempt->close();

    // Assuming a lockout after 5 failed attempts and a timeout of 30 minutes
    if ($failed_attempts >= 5 && (time() - strtotime($last_attempt)) < 1800) {
        die("Account locked due to too many failed login attempts. Please try again later.");
    }

    // Prepare SQL statement to retrieve user from database
    $stmt = $conn->prepare("SELECT id, username, password, failed_login_attempts, last_failed_login FROM users WHERE username=?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->bind_result($db_id, $db_username, $db_password, $failed_attempts, $last_attempt);
        $stmt->fetch();

        if (password_verify($password, $db_password)) {
            // Reset failed login attempts on successful login
            $resetAttempt = $conn->prepare("UPDATE users SET failed_login_attempts = 0, last_failed_login = NULL WHERE username=?");
            $resetAttempt->bind_param("s", $username);
            $resetAttempt->execute();
            $resetAttempt->close();

            setcookie("username", $username, -1, "/");
            setcookie("userid", $db_id, -1, "/");
            header("Location: index.php");
            exit();
        } else {
            // Update failed login attempts
            $updateAttempt = $conn->prepare("UPDATE users SET failed_login_attempts = failed_login_attempts + 1, last_failed_login = NOW() WHERE username=?");
            $updateAttempt->bind_param("s", $username);
            $updateAttempt->execute();
            $updateAttempt->close();

            echo "Incorrect password";
        }
    } else {
        echo "Username does not exist";
    }

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
    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
        <label for="username">Username:</label>
        <input type="text" id="username" name="username" required><br><br>
        <label for="password">Password:</label>
        <input type="password" id="password" name="password" required><br><br>
        <button type="submit">Login</button>
        <p><a href="enter_email.php">Forgot your password?</a></p>
    </form>
</body>

</html>