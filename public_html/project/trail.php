<?php
require(__DIR__ . "/../../partials/nav.php");
//nmp33 8/4

if (!is_logged_in()) {
    flash("Must be logged in!", "danger");
    die(header("Location: login.php"));
}

$db = getDB();
$trail = null;
$user_id = get_user_id();
$isFavorited = false;

$id = $_GET["id"] ?? null;

if ($id && is_numeric($id)) {
    $stmt = $db->prepare("SELECT * FROM `IT202-M25-Trails` WHERE id = :id");
    $stmt->bindValue(":id", $id, PDO::PARAM_INT);
    $stmt->execute();
    $trail = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($trail) {
        // Check if this trail is already favorited by the current user
        $stmt = $db->prepare("SELECT 1 FROM user_trail_favorites WHERE user_id = :uid AND trail_id = :tid");
        $stmt->execute([":uid" => $user_id, ":tid" => $trail["id"]]);
        $isFavorited = $stmt->fetchColumn() !== false;
    }
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

        <div class="favorite-form">
            <?php if (!$isFavorited): ?>
                <form method="POST" action="favorite_trail.php">
                    <input type="hidden" name="trail_id" value="<?php echo $trail['id']; ?>">
                    <input type="hidden" name="redirect" value="trail.php?id=<?php echo $trail['id']; ?>">
                    <button type="submit" class="favorite-btn">❤ Favorite</button>
                </form>
            <?php else: ?>
                <form method="POST" action="unfavorite_trail.php">
                    <input type="hidden" name="trail_id" value="<?php echo $trail['id']; ?>">
                    <input type="hidden" name="redirect" value="trail.php?id=<?php echo $trail['id']; ?>">
                    <button type="submit" class="unfavorite-btn">✖ Unfavorite</button>
                </form>
            <?php endif; ?>
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
.favorite-form {
    display: flex;
    justify-content: center;
    gap: 1rem;
    margin-top: 2rem;
}
.favorite-btn, .unfavorite-btn {
    padding: 0.75rem 1.5rem;
    font-size: 1rem;
    border: none;
    border-radius: 8px;
    box-shadow: 0 4px 10px rgba(0,0,0,0.4);
    cursor: pointer;
    transition: background-color 0.2s ease, transform 0.2s ease;
}
.favorite-btn {
    background-color: #2d7dff;
    color: white;
}
.favorite-btn:hover {
    background-color: #1b5edb;
}
.unfavorite-btn {
    background-color: #e04848;
    color: white;
}
.unfavorite-btn:hover {
    background-color: #c0392b;
}
.favorite-btn:active, .unfavorite-btn:active {
    transform: scale(0.97);
}
</style>
