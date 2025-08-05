<?php
require(__DIR__ . "/../../partials/nav.php");
error_log("Session: " . var_export($_SESSION, true));
?>

<?php if(is_logged_in(true)):?>
    <h1>Welcome, <?php echo get_username() ?>!</h1>
<?php endif;?>

<?php
require(__DIR__."/../../partials/flash.php");
?>