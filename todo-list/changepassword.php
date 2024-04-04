<?php
require_once 'config.php';
session_start();

$message = '';
$username = $_COOKIE['username'];
if (!isset($username)) {
    header("Location: login.php");
    exit();
}
// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['currentPassword']) && isset($_POST['newPassword'])) {
    // Get passwords from the form
    $currentPassword = $_POST['currentPassword'];
    $newPassword = $_POST['newPassword'];

    // Connect to the database
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Prepare SQL statement to retrieve the current password from the database
    $stmt = $conn->prepare("SELECT password FROM users WHERE username=?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->bind_result($db_password);
        $stmt->fetch();

        if ($currentPassword == $db_password) {
            $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

            $update_stmt = $conn->prepare("UPDATE users SET password=? WHERE username=?");
            $update_stmt->bind_param("ss", $hashedPassword, $username);
            $update_stmt->execute();

            if ($update_stmt->affected_rows > 0) {
                $message = "Password changed successfully.";
            } else {
                $message = "An error occurred. Please try again.";
            }
            $update_stmt->close();
        } else {
            $message = "Current password is incorrect.";
        }
    } else {
        $message = "User not found.";
    }
    $stmt->close();
    $conn->close();
}
require_once 'fw/header.php';
?>

<h2>Change Password</h2>
<?php if ($message != '') echo "<p>$message</p>"; ?>
<form id="form" method="post" action="<?php echo $_SERVER["PHP_SELF"]; ?>">
    <div class="form-group">
        <label for="currentPassword">Current Password</label>
        <input type="password" class="form-control size-medium" name="currentPassword" id="currentPassword" required>
    </div>
    <div class="form-group">
        <label for="newPassword">New Password</label>
        <input type="password" class="form-control size-medium" name="newPassword" id="newPassword" required>
    </div>
    <div class="form-group">
        <input type="submit" class="btn size-auto" value="Change Password" />
    </div>
</form>

<?php
require_once 'fw/footer.php';
?>