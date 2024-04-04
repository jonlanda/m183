<?php
// Check if the user is logged in
if (!isset($_COOKIE['username'])) {
    header("Location: ../login.php");
    exit();
}

// Include configuration file and establish database connection
require_once '../config.php';
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// Check database connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Prepare SQL statement to retrieve users from the database
$stmt = $conn->prepare("SELECT users.ID, users.username, roles.title FROM users INNER JOIN permissions ON users.ID = permissions.userID INNER JOIN roles ON permissions.roleID = roles.ID ORDER BY username");

// Execute the statement
$stmt->execute();

// Store the result
$stmt->store_result();

// Bind the result variables
$stmt->bind_result($db_id, $db_username, $db_title);

// Include header
require_once '../fw/header.php';
?>
<h2>User List</h2>

<table>
    <tr>
        <th>ID</th>
        <th>Username</th>
        <th>Role</th>
    </tr>
    <?php
        // Fetch the result
        while ($stmt->fetch()) {
            echo "<tr><td>$db_id</td><td>$db_username</td><td>$db_title</td></tr>";
        }
    ?>
</table>

<?php
// Include footer
require_once '../fw/footer.php';
?>