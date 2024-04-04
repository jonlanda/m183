<?php
// Unset and expire the 'username' cookie
unset($_COOKIE['username']); 
setcookie('username', '', time() - 3600, '/'); // Set the expiration time in the past to expire the cookie immediately

// Unset and expire the 'userid' cookie
unset($_COOKIE['userid']); 
setcookie('userid', '', time() - 3600, '/'); // Set the expiration time in the past to expire the cookie immediately

// Redirect to the homepage
header("Location: /");
exit();
?>