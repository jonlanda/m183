<?php
require_once 'config.php';

// Constants for brute force protection
$bad_login_limit = 3; // Number of allowed failed attempts
$lockout_time = 10; // Lockout period in seconds (10 seconds)
$username_error = "";
$password_error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (empty($_POST["username"])) {
        $username_error = "Username is required";
    }
    if (empty($_POST["password"])) {
        $password_error = "Password is required";
    }
}

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['username']) && isset($_POST['password'])) {
    // Get username and password from the form
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Connect to the database
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Sanitize user input
    //$username = $conn->real_escape_string($username);
    //$password = $conn->real_escape_string($password);

    


    // Prepare SQL statement to retrieve user from database
    $stmt = $conn->prepare("SELECT id, username, password, last_login, failed_login_count FROM users WHERE username=?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->bind_result($db_id, $db_username, $db_password, $last_login, $failed_login_count);
        $stmt->fetch();
        

        if ($password == $db_password) {
            $current_time = date("Y-m-d H:i:s");
            $reset_stmt = $conn->prepare("UPDATE users SET last_login = ?, failed_login_count = 0 WHERE username=?");
            $reset_stmt->bind_param("ss", $current_time, $username);
            $reset_stmt->execute();
            $reset_stmt->close();

            setcookie("username", $username, time() + 86400, "/"); // 86400 = 1 day
            setcookie("userid", $db_id, time() + 86400, "/"); // 86400 = 1 day
            header("Location: index.php");
            exit();
        } else {
            $current_time = time();
            $last_login_time = date("Y-m-d H:i:s", $current_time);



            if ($last_login == 0 || $current_time - strtotime($last_login) > $lockout_time) {
                $failed_login_count = 1;
                // Update last_login timestamp and failed_login_count
                $update_stmt = $conn->prepare("UPDATE users SET last_login = ?, failed_login_count = ? WHERE username=?");
                $update_stmt->bind_param("sis", $last_login_time, $failed_login_count, $username);
            } else {
                $failed_login_count++;
                // Only update failed_login_count
                $update_stmt = $conn->prepare("UPDATE users SET failed_login_count = ? WHERE username=?");
                $update_stmt->bind_param("is", $failed_login_count, $username);
            }

            $update_stmt->execute();
            $update_stmt->close();

            if ($failed_login_count >= $bad_login_limit && (time() - strtotime($last_login) < $lockout_time)) {
                echo "You are currently locked out. Please try again later.";
                echo "<script>alert('You are currently locked out. Please try again later.');</script>";
                exit;
            }

            $password_error = "Invalid password";
        
        }
    } else {
        $username_error = "Username does not exist";
    }

    $stmt->close();
}
require_once 'fw/header.php';
?>

<!-- Login Form -->
<h2>Login</h2>
<form id="form" method="post" action="<?php echo $_SERVER["PHP_SELF"]; ?>">
    <div class="flex">
        <div class="form-group">
            <label for="username">Username</label>
            <input type="text" class="form-control size-medium" name="username" id="username">
        </div>
        <span class="error"><?php echo $username_error;?></span>
    </div>
    <div class="form-group">
        <label for="password">Password</label>
        <input type="password" class="form-control size-medium" name="password" id="password">
        <span class="error"><?php echo $password_error;?></span>
    </div>
    <div class="form-group">
        <input type="submit" class="btn size-auto" value="Login" />
    </div>
</form>

<?php
require_once 'fw/footer.php';
?>