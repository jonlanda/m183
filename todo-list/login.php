<?php
require_once 'config.php';

// Constants for brute force protection
const BAD_LOGIN_LIMIT = 3;// Number of allowed failed attempts
const max_retry_time = 10; // Lockout period in seconds (10 seconds)
$username_error = "";
$password_error = "";

$login_error = "";

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    require_once 'fw/header.php';
    require_once 'login_form.php';
    exit;
}


$username_error = empty($_POST["username"]) ? "Username is required" : null;
$password_error = empty($_POST["password"]) ? "Password is required" : null;

if ($username_error || $password_error) {
    require_once 'fw/header.php';
    require_once 'login_form.php';
    exit;
}


$username = $_POST['username'];
$password = $_POST['password'];

$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}



$stmt = $conn->prepare("SELECT id, username, password, last_login, failed_login_count, blocked_until FROM users WHERE username=?");
$stmt->bind_param("s", $username);
$stmt->execute();
$stmt->store_result();



//handle no user found
if ($stmt->num_rows <= 0) {
    $login_error = "Invalid username or password";
    $stmt->close();
    require_once 'fw/header.php';
    require_once 'login_form.php';
    exit;
}

//set vars
$stmt->bind_result($db_id, $db_username, $db_password, $last_login, $failed_login_count, $blocked_until);
$stmt->fetch();


if ($blocked_until != null && strtotime($blocked_until) > time()) {
    header("Location: blocked.php");
    exit;
}


if (!password_verify($password, $db_password)) {
    handleFailedLogin($conn, $username, $last_login, $failed_login_count);
    $stmt->close();
    require_once 'fw/header.php';
    require_once 'login_form.php';
    exit;
}


handleSuccessfulLogin($conn, $username, $db_id);



//functions 

function handleFailedLogin($conn, $username, $last_login, $failed_login_count) {
    global $login_error;

    $current_time = time();
    $last_login_time = date("Y-m-d H:i:s", $current_time);

    if ($last_login == 0 || $current_time - strtotime($last_login) > max_retry_time) {
        $failed_login_count = 1;
        $update_stmt = $conn->prepare("UPDATE users SET last_login = ?, failed_login_count = ? WHERE username=?");
        $update_stmt->bind_param("sis", $last_login_time, $failed_login_count, $username);
    } else {
        $failed_login_count++;
        $update_stmt = $conn->prepare("UPDATE users SET failed_login_count = ? WHERE username=?");
        $update_stmt->bind_param("is", $failed_login_count, $username);
    }

    $update_stmt->execute();
    $update_stmt->close();

    if ($failed_login_count >= BAD_LOGIN_LIMIT && (time() - strtotime($last_login) < max_retry_time)) {
        //block user for 24 h
        $stmt = $conn->prepare("UPDATE users SET blocked_until = DATE_ADD(NOW(), INTERVAL 24 HOUR) WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $stmt->close();
        header("Location: blocked.php");
        exit;
    }

    $login_error = "Invalid username or password";
}

function handleSuccessfulLogin($conn, $username, $db_id) {
    $current_time = date("Y-m-d H:i:s");
    $reset_stmt = $conn->prepare("UPDATE users SET last_login = ?, failed_login_count = 0 WHERE username=?");
    $reset_stmt->bind_param("ss", $current_time, $username);
    $reset_stmt->execute();
    $reset_stmt->close();

    setcookie("username", $username, time() + 86400, "/"); // 86400 = 1 day
    setcookie("userid", $db_id, time() + 86400, "/"); // 86400 = 1 day
    header("Location: index.php");
    exit();
}

require_once 'fw/header.php';
?>



<?php
require_once 'fw/footer.php';
?>