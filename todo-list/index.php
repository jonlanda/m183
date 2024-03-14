<?php
// Check if the user is logged in
if (!isset($_COOKIE['username'])) {
    header("Location: login.php");
    exit();
}

// Include header
require_once 'fw/header.php';
?>

<h2>Welcome, <?php echo htmlspecialchars($_COOKIE['username']); ?>!</h2>

<?php if (isset($_COOKIE['userid'])) : ?>
<?php require_once 'user/tasklist.php'; ?>
<hr />
<?php require_once 'user/backgroundsearch.php'; ?>
<?php endif; ?>

<?php
// Include footer
require_once 'fw/footer.php';
?>