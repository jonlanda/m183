<?php

// Check if the user is logged in
if (!isset($_COOKIE['username'])) {
    header("Location: login.php");
    exit();
}

require_once 'fw/header.php';
?>
<h2>Welcome, <?php echo $_COOKIE['username']; ?>!</h2>


<?php 
    if (isset($_COOKIE['userid'])) {
        require_once 'user/tasklist.php';
        echo "<hr />";
        require_once 'user/backgroundsearch.php';
    }
?>


<?php
    require_once 'fw/footer.php';
?>