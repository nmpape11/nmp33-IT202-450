<?php
require(__DIR__ . "/../../partials/nav.php");
//nmp33 8/4

if (!is_logged_in()) {
    flash("Must be logged in to favorite trails.", "danger");
    die(header("Location: login.php"));
}

$user_id = get_user_id();
$trail_id = $_POST["trail_id"] ?? null;
$redirect = $_POST["redirect"] ?? "trails.php";

if (!$trail_id || !is_numeric($trail_id)) {
    flash("Invalid trail ID.", "danger");
    die(header("Location: $redirect"));
}

$db = getDB();

// Check if already favorited to prevent duplicates
$stmt = $db->prepare("SELECT 1 FROM user_trail_favorites WHERE user_id = :uid AND trail_id = :tid");
$stmt->execute([":uid" => $user_id, ":tid" => $trail_id]);

if ($stmt->fetch()) {
    flash("Trail already favorited.", "warning");
    die(header("Location: $redirect"));
}

// Insert the favorite
$stmt = $db->prepare("INSERT INTO user_trail_favorites (user_id, trail_id) VALUES (:uid, :tid)");
try {
    $stmt->execute([":uid" => $user_id, ":tid" => $trail_id]);
    flash("Trail favorited!", "success");
} catch (Exception $e) {
    flash("Failed to favorite trail: " . $e->getMessage(), "danger");
}

die(header("Location: $redirect"));
?>
