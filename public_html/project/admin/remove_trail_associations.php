<?php
require(__DIR__ . "/../../../partials/nav.php");

if (!is_logged_in()) {
    flash("Must be logged in", "danger");
    die(header("Location: " . get_url("login.php")));
}

if (!has_role("Admin")) {
    flash("You don't have permission to do that", "warning");
    die(header("Location: " . get_url("landing.php")));
}

$trail_id = $_POST["trail_id"] ?? null;

if (!$trail_id || !is_numeric($trail_id)) {
    flash("Invalid trail ID", "danger");
    die(header("Location: admin_trails.php"));
}

$db = getDB();
$stmt = $db->prepare("DELETE FROM user_trail_favorites WHERE trail_id = :tid");

try {
    $stmt->execute([":tid" => $trail_id]);
    $count = $stmt->rowCount();
    flash("Removed $count user association" . ($count !== 1 ? "s" : "") . " for trail ID $trail_id", "success");
} catch (PDOException $e) {
    error_log("Error removing associations: " . var_export($e->errorInfo, true));
    flash("Failed to remove associations", "danger");
}

die(header("Location: admin_trails.php"));
