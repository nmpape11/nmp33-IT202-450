<?php
require(__DIR__ . "/../../../partials/nav.php");

if (!is_logged_in()) {
    flash("Must be logged in!", "danger");
    die(header("Location: login.php"));
}

if (!has_role("Admin")) {
    flash("You don't have permission to view this page", "warning");
    die(header("Location: " . get_url("landing.php")));
}

$trail_id = $_GET["trail_id"] ?? null;

if (!$trail_id || !is_numeric($trail_id)) {
    flash("Invalid or missing trail ID.", "danger");
    die(header("Location: " . get_url("admin/trails.php")));
}

$db = getDB();

// Get trail info (optional but nice)
$trailStmt = $db->prepare("SELECT name FROM `IT202-M25-Trails` WHERE id = :id");
$trailStmt->execute([":id" => $trail_id]);
$trail = $trailStmt->fetch(PDO::FETCH_ASSOC);

if (!$trail) {
    flash("Trail not found.", "danger");
    die(header("Location: " . get_url("admin/trails.php")));
}

// Get all users who favorited this trail
$stmt = $db->prepare("
    SELECT u.id, u.username, u.email
    FROM user_trail_favorites f
    JOIN Users u ON u.id = f.user_id
    WHERE f.trail_id = :trail_id
");
$stmt->execute([":trail_id" => $trail_id]);
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<h1>Users Who Favorited: <?= se($trail["name"]) ?></h1>
<p><?= count($users) ?> user<?= count($users) !== 1 ? 's' : '' ?> found.</p>

<?php if ($users): ?>
    <div class="user-grid">
        <?php foreach ($users as $user): ?>
            <div class="user-card">
                <a href="../user.php?id=<?= se($user["id"]) ?>">
                    <h3><?= se($user["username"]) ?></h3>
                </a>
                <p>Email: <?= se($user["email"]) ?></p>
                <p>User ID: <?= se($user["id"]) ?></p>
            </div>
        <?php endforeach; ?>
    </div>
<?php else: ?>
    <p>No users have favorited this trail yet.</p>
<?php endif; ?>

<?php require(__DIR__ . "/../../../partials/flash.php"); ?>

<style>
.user-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
    gap: 1rem;
    padding: 1rem;
    max-width: 1200px;
    margin: auto;
}
.user-card {
    background-color: #1e1e1e;
    border-radius: 10px;
    padding: 1rem;
    box-shadow: 0 0 8px rgba(0,0,0,0.2);
}
.user-card h3 {
    margin-top: 0;
    color: #fff;
}
.user-card p {
    margin: 0.3rem 0;
    color: #ccc;
}
</style>
