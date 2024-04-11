<?php
    // Check if the user is logged in
    if (!isset($_COOKIE['userid'])) {
        header("Location: /");
        exit();
    }

    $options = array("Open", "In Progress", "Done");

    // read task if possible
    $title="";
    $state="";
    $id = "";

    if (isset($_GET['id'])){
        $id = $_GET["id"];
        include 'fw/db.php';
        $stmt = executeStatement("select ID, title, state from tasks where ID = $id");
        if ($stmt->num_rows > 0) {
            $stmt->bind_result($db_id, $db_title, $db_state);
            $stmt->fetch();
            $title = $db_title;
            $state = $db_state;
        }
    }

    require_once 'fw/header.php';
?>

<?php if (isset($_GET['id'])) { ?>
    <h1>Edit Task</h1>
<?php }else { ?>
    <h1>Create Task</h1>
<?php } ?>

<form id="form" method="post" action="savetask.php">
    <input type="hidden" name="id" value="<?php echo $id ?>" />
    <div class="form-group">
        <label for="title">Description</label>
        <input type="text" class="form-control size-medium" name="title" id="title" value="<?php echo $title ?>">
    </div>
    <div class="form-group">
        <label for="state">State</label>
        <select name="state" id="state" class="size-auto">
            <?php for ($i = 0; $i < count($options); $i++) : ?>
                <span><?php $options[1] ?></span>
                <option value='<?= strtolower($options[$i]); ?>' <?= $state == strtolower($options[$i]) ? 'selected' : '' ?>><?= $options[$i]; ?></option>
            <?php endfor; ?>
        </select>
    </div>
    <div class="form-group">
        <label for="submit" ></label>
        <input id="submit" type="submit" class="btn size-auto" value="Submit" />
    </div>
</form>
<script>
  $(document).ready(function () {
    $('#form').validate({
      rules: {
        title: {
          required: true
        }
      },
      messages: {
        title: 'Please enter a description.',
      },
      submitHandler: function (form) {
        form.submit();
      }
    });
  });
</script>

<?php
    require_once 'fw/footer.php';
?>