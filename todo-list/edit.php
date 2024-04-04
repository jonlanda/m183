<?php
    // Check if the user is logged in
    if (!isset($_COOKIE['userid'])) {
        header("Location: /");
        exit();
    }

    // Include database connection
    require_once 'fw/db.php';

    $options = array("Open", "In Progress", "Done");

    // Initialize variables
    $title = "";
    $state = "";
    $id = "";

    // Read task if ID is provided
    if (isset($_GET['id'])) {
        $id = intval($_GET["id"]);
        $stmt = $mysqli->prepare("SELECT ID, title, state FROM tasks WHERE ID = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->store_result();
        
        if ($stmt->num_rows > 0) {
            $stmt->bind_result($db_id, $db_title, $db_state);
            $stmt->fetch();
            $title = htmlspecialchars($db_title);
            $state = htmlspecialchars($db_state);
        }
        $stmt->close();
    }

    // Include header
    require_once 'fw/header.php';
?>

<?php if ($id !== "") { ?>
<h1>Edit Task</h1>
<?php } else { ?>
<h1>Create Task</h1>
<?php } ?>

<form id="form" method="post" action="savetask.php">
    <input type="hidden" name="id" value="<?= $id ?>" />
    <div class="form-group">
        <label for="title">Description</label>
        <input type="text" class="form-control size-medium" name="title" id="title" value="<?= $title ?>">
    </div>
    <div class="form-group">
        <label for="state">State</label>
        <select name="state" id="state" class="size-auto">
            <?php foreach ($options as $option) : ?>
            <option value="<?= strtolower($option); ?>" <?= $state == strtolower($option) ? 'selected' : '' ?>>
                <?= $option; ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="form-group">
        <label for="submit"></label>
        <input id="submit" type="submit" class="btn size-auto" value="Submit" />
    </div>
</form>
<script>
$(document).ready(function() {
    $('#form').validate({
        rules: {
            title: {
                required: true
            }
        },
        messages: {
            title: 'Please enter a description.',
        },
        submitHandler: function(form) {
            form.submit();
        }
    });
});
</script>

<?php
    // Include footer
    require_once 'fw/footer.php';
?>