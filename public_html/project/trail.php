<?php
require(__DIR__ . "/../../partials/nav.php");
if (!is_logged_in()) {
    flash("Must be logged in!", "danger");
    die(header("Location: login.php"));
}

$db = getDB();
$trail = null;

$id = $_GET["id"] ?? null;

if ($id && is_numeric($id)) {
    $stmt = $db->prepare("SELECT * FROM `IT202-M25-Trails` WHERE id = :id");
    $stmt->bindValue(":id", $id, PDO::PARAM_INT);
    $stmt->execute();
    $trail = $stmt->fetch(PDO::FETCH_ASSOC);
}
?>

<?php if ($trail): ?>
    <div class="trail-detail-container">
        <h1><?php se($trail, "name"); ?></h1>
        <p class="trail-location"><?php se($trail, "city"); ?>, <?php se($trail, "state"); ?></p>
        <div class="trail-description">
            <h2>Description</h2>
            <p><?php se($trail, "description"); ?></p>
        </div>
    </div>
<?php else: ?>
    <p style="text-align: center; color: #ccc;">Trail not found.</p>
<?php endif; ?>

<?php require(__DIR__ . "/../../partials/flash.php"); ?>

<style>
.trail-detail-container {
    max-width: 800px;
    margin: 2rem auto;
    padding: 2rem;
    border-radius: 12px;
    background-color: #1e1e1e;
    box-shadow: 0 4px 12px rgba(0,0,0,0.5);
    color: white;
}

.trail-detail-container h1 {
    font-size: 2rem;
    margin-bottom: 0.5rem;
    color: #fff;
}

.trail-location {
    font-size: 1.1rem;
    color: #aaa;
    margin-bottom: 1.5rem;
}

.trail-description h2 {
    font-size: 1.3rem;
    margin-bottom: 0.5rem;
}

.trail-description p {
    line-height: 1.6;
    font-size: 1rem;
    color: #ddd;
}
</style>
