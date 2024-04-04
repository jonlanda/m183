<?php
    
    unset($_COOKIE['username']); 
    setcookie('username', '', -1, '/'); 
    unset($_COOKIE['userid']); 
    setcookie('userid', '', -1, '/'); 

    header("Location: /");
    exit();
?>