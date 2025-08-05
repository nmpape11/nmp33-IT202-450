<?php
require(__DIR__ . "/../../partials/nav.php");

if (!is_logged_in()) {
    flash("Must be logged in to clear favorites.", "danger");
    die(header("Location: login.php"));
}

$user_id = get_user_id();

$db = getDB();
$stmt = $db->prepare("DELETE FROM user_trail_favorites WHERE user_id = :uid");

try {
    $stmt->execute([":uid" => $user_id]);
    flash("All favorites cleared.", "success");
} catch (Exception $e) {
    flash("Failed to clear favorites: " . $e->getMessage(), "danger");
}

die(header("Location: " . get_url("user_favorites.php")));
?>