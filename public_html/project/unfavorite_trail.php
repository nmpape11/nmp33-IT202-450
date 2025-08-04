<?php
require(__DIR__ . "/../../partials/nav.php");
//nmp33 8/4

if (!is_logged_in()) {
    flash("Must be logged in!", "danger");
    die(header("Location: login.php"));
}

$db = getDB();
$user_id = get_user_id();
$trail_id = $_POST["trail_id"] ?? null;
$redirect = $_POST["redirect"] ?? "trails.php";

if ($trail_id && is_numeric($trail_id)) {
    $stmt = $db->prepare("DELETE FROM user_trail_favorites WHERE user_id = :uid AND trail_id = :tid");
    try {
        $stmt->execute([
            ":uid" => $user_id,
            ":tid" => $trail_id
        ]);
        flash("Trail removed from favorites.", "info");
    } catch (Exception $e) {
        flash("Error unfavoriting trail: " . $e->getMessage(), "danger");
    }
} else {
    flash("Invalid trail ID.", "danger");
}

die(header("Location: " . get_url($redirect)));
