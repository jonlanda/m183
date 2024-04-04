<?php
// Check if the user is logged in
if (!isset($_COOKIE['username'])) {
    header("Location: ../login.php");
    exit();
}

// Include database connection
include 'fw/db.php';
?>

<section id="search">
    <h2>Search</h2>
    <form id="form" method="post" action="">
        <input type="hidden" id="searchurl" name="searchurl" value="/search/v2/">
        <div class="form-group">
            <label for="terms">Terms</label>
            <input type="text" class="form-control size-medium" name="terms" id="terms" required>
        </div>
        <div class="form-group">
            <input id="submit" type="submit" class="btn size-auto" value="Submit">
        </div>
    </form>
    <div id="messages">
        <div id="msg" class="hidden">The search is running. Results will be visible soon.</div>
        <div id="result" class="hidden"></div>
    </div>
    <script>
    $(document).ready(function() {
        $('#form').validate({
            rules: {
                terms: {
                    required: true
                }
            },
            messages: {
                terms: 'Please enter search terms.'
            },
            submitHandler: function(form) {
                var provider = $("#searchurl").val();
                var terms = $("#terms").val();
                var userid = <?php echo $_COOKIE["userid"] ?>;
                $("#msg").show();
                $("#result").html("");
                $.post("search.php", {
                    provider: provider,
                    terms: terms,
                    userid: userid
                }, function(data) {
                    console.log(data);
                    $("#result").html(data);
                    $("#msg").hide(500);
                    $("#result").show(500);
                });
                return false;
            }
        });
    });
    </script>
</section>