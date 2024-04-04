<?php
// Redirect to login page if user is not logged in
if (!isset($_COOKIE['username'])) {
    header("Location: ../login.php");
    exit();
}

// Include configuration file and establish database connection
require_once 'config.php';
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// Check database connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get user ID from cookie
$userid = $_COOKIE['userid'];

// Prepare SQL statement to retrieve tasks for the logged-in user
$stmt = $conn->prepare("SELECT ID, title, state FROM tasks WHERE UserID = ?");
$stmt->bind_param("i", $userid);

// Execute the statement
$stmt->execute();

// Store the result
$stmt->store_result();

// Bind the result variables
$stmt->bind_result($db_id, $db_title, $db_state);
?>
<section id="list">
    <a href="edit.php">Create Task</a>
    <table>
        <tr>
            <th>ID</th>
            <th>Description</th>
            <th>State</th>
            <th></th>
        </tr>
        <?php while ($stmt->fetch()) { ?>
        <tr>
            <td><?php echo $db_id ?></td>
            <td class="wide"><?php echo $db_title ?></td>
            <td><?php echo ucfirst($db_state) ?></td>
            <td>
                <a href="edit.php?id=<?php echo $db_id ?>">edit</a> | <a
                    href="delete.php?id=<?php echo $db_id ?>">delete</a>
            </td>
        </tr>
        <?php } ?>
    </table>
</section>