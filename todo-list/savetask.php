<?php
    // Check if the user is logged in
    if (!isset($_COOKIE['userid'])) {
        header("Location: /");
        exit();
    }
    $taskid = "";
    require_once 'fw/db.php';
    // see if the id exists in the database

    if (isset($_POST['id']) && strlen($_POST['id']) != 0){
        $taskid = $_POST["id"];
        $stmt = executeStatement("select ID, title, state from tasks where ID = $taskid");
        if ($stmt->num_rows == 0) {
            $taskid = "";
        }
    }
  
  require_once 'fw/header.php';
  if (isset($_POST['title']) && isset($_POST['state'])){
    $state = $_POST['state'];
    $title = $_POST['title'];
    $userid = $_COOKIE['userid'];  

    if ($taskid == ""){
      $stmt = executeStatement("insert into tasks (title, state, userID) values ('$title', '$state', '$userid')");
    }
    else {
      $stmt = executeStatement("update tasks set title = '$title', state = '$state' where ID = $taskid");
    }

    echo "<span class='info info-success'>Update successfull</span>";
  }
  else {
    echo "<span class='info info-error'>No update was made</span>";
  } 

  require_once 'fw/footer.php';
?>
