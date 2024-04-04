<?php
if (basename($_SERVER['PHP_SELF']) == 'login_form.php') {
    exit;
}
?>


<!-- Login Form -->
<h2>Login</h2>
<form id="form" method="post" action="<?php echo $_SERVER["PHP_SELF"]; ?>">
    <div class="flex">
        <div class="form-group">
            <label for="username">Username</label>
            <input type="text" class="form-control size-medium" name="username" id="username">
        </div>
        <span class="error"><?php echo $username_error . $login_error; ?></span>
    </div>
    <div class="form-group">
        <label for="password">Password</label>
        <input type="password" class="form-control size-medium" name="password" id="password">
        <span class="error"><?php echo $password_error . $login_error; ?></span>
    </div>
    <div class="form-group">
        <input type="submit" class="btn size-auto" value="Login" />
    </div>
</form>