<?php
require_once 'config.php';

// Constants for brute force protection
$bad_login_limit = 3; // Number of allowed failed attempts
$lockout_time = 5; // Lockout period in seconds (10 minutes)

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['username']) && isset($_GET['password'])) {
    // Get username and password from the form
    $username = $_GET['username'];
    $password = $_GET['password'];
    
    // Connect to the database
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Prepare SQL statement to retrieve user from database
    $stmt = $conn->prepare("SELECT id, username, password, first_failed_login, failed_login_count FROM users WHERE username=?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->bind_result($db_id, $db_username, $db_password, $first_failed_login, $failed_login_count);
        $stmt->fetch();

        if ($failed_login_count >= $bad_login_limit && (time() - $first_failed_login < $lockout_time)) {
            echo "You are currently locked out.";
            exit;
        }

        if (password_verify($password, $db_password)) {
            $reset_stmt = $conn->prepare("UPDATE users SET first_failed_login = 0, failed_login_count = 0 WHERE username=?");
            $reset_stmt->bind_param("s", $username);
            $reset_stmt->execute();
            $reset_stmt->close();

            setcookie("username", $username, time() + 86400, "/"); // 86400 = 1 day
            setcookie("userid", $db_id, time() + 86400, "/"); // 86400 = 1 day
            header("Location: index.php");
            exit();
        } else {
            if (time() - $first_failed_login > $lockout_time || $first_failed_login == 0) {
                $first_failed_login = time();
                $failed_login_count = 1;
            } else {
                $failed_login_count++;
            }

            $update_stmt = $conn->prepare("UPDATE users SET first_failed_login = ?, failed_login_count = ? WHERE username=?");
            $update_stmt->bind_param("iis", $first_failed_login, $failed_login_count, $username);
            $update_stmt->execute();
            $update_stmt->close();

            echo "Incorrect password";
        }
    } else {
        echo "Username does not exist";
    }

    $stmt->close();
}
require_once 'fw/header.php';
?>

<!-- Login Form -->
<h2>Login</h2>
<form id="form" method="get" action="<?php echo $_SERVER["PHP_SELF"]; ?>">
    <div class="form-group">
        <label for="username">Username</label>
        <input type="text" class="form-control size-medium" name="username" id="username">
    </div>
    <div class="form-group">
        <label for="password">Password</label>
        <input type="password" class="form-control size-medium" name="password" id="password"> <!-- Changed type to password -->
    </div>
    <div class="form-group">
        <input type="submit" class="btn size-auto" value="Login" />
    </div>
</form>

<?php
require_once 'fw/footer.php';
?>