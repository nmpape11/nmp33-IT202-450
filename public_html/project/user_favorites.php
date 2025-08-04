<?php
require(__DIR__ . "/../../partials/nav.php");

if (!is_logged_in()) {
    flash("Must be logged in", "danger");
    die(header("Location: login.php"));
}

$db = getDB();
$user_id = get_user_id();

$limit = isset($_GET["limit"]) && is_numeric($_GET["limit"]) && $_GET["limit"] > 0 && $_GET["limit"] <= 100
    ? (int)$_GET["limit"]
    : 10;

$countStmt = $db->prepare("SELECT COUNT(*) as total FROM user_trail_favorites WHERE user_id = :uid");
$countStmt->execute([":uid" => $user_id]);
$total = $countStmt->fetchColumn();

$stmt = $db->prepare("
    SELECT t.*
    FROM `IT202-M25-Trails` t
    JOIN user_trail_favorites f ON f.trail_id = t.id
    WHERE f.user_id = :uid
    LIMIT :limit");
$stmt->bindValue(":uid", $user_id, PDO::PARAM_INT);
$stmt->bindValue(":limit", $limit, PDO::PARAM_INT);
$stmt->execute();
$favorites = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<h1 style="text-align:center;">Your Favorited Trails</h1>
<p style="text-align:center;">You have <?= $total ?> trail<?= $total != 1 ? 's' : '' ?> favorited.</p>

<div class="filter-bar">
    <form method="GET" class="filter-form">
        <label for="limit">Results per page (1–100):</label>
        <input type="number" name="limit" min="1" max="100" value="<?= $limit ?>">
        <button type="submit">Apply</button>
    </form>
</div>

<?php if ($favorites): ?>
    <div class="favorites-grid">
        <?php foreach ($favorites as $trail): ?>
            <div class="trail-card">
                <a class="card-link" href="trail.php?id=<?= se($trail['id'], null, false) ?>">
                    <h2><?= se($trail["name"]) ?></h2>
                    <p><?= se($trail["city"]) ?>, <?= se($trail["state"]) ?></p>
                </a>
                <form method="POST" action="unfavorite_trail.php" class="unfavorite-form">
                    <input type="hidden" name="trail_id" value="<?= se($trail["id"], null, false) ?>">
                    <input type="hidden" name="redirect" value="user_favorites.php">
                    <button type="submit" class="unfavorite-btn">✗ Unfavorite</button>
                </form>
            </div>
        <?php endforeach; ?>
    </div>
<?php else: ?>
    <p style="text-align: center; margin-top: 2rem;">No results available.</p>
<?php endif; ?>

<?php require(__DIR__ . "/../../partials/flash.php"); ?>

<style>
a {
    text-decoration: none;
    color: inherit;
}

.card-link h2, .card-link p {
    margin: 0;
    color: inherit;
}

.filter-bar {
    display: flex;
    justify-content: center;
    margin: 1.5rem 0;
}

.filter-form {
    display: flex;
    flex-direction: row;
    align-items: center;
    gap: 1rem;
}

.filter-form label {
    font-size: 0.95rem;
    color: #ccc;
    white-space: nowrap;
}

.filter-form input[type="number"] {
    padding: 0.3rem 0.6rem;
    width: 70px;
    border: 1px solid #444;
    border-radius: 6px;
    background-color: #2a2a2a;
    color: white;
}

.filter-form button {
    background-color: #444;
    color: white;
    border: none;
    padding: 0.5rem 1rem;
    border-radius: 6px;
    cursor: pointer;
    font-weight: bold;
    transition: background-color 0.2s ease;
}

.filter-form button:hover {
    background-color: #666;
}

.favorites-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
    gap: 1rem;
    padding: 1rem;
    max-width: 1200px;
    margin: auto;
}

.trail-card {
    background-color: #1e1e1e;
    border-radius: 10px;
    padding: 1rem;
    box-shadow: 0 0 8px rgba(0,0,0,0.2);
    display: flex;
    flex-direction: column;
    justify-content: space-between;
}

.trail-card h2 {
    font-size: 1.2rem;
    margin-bottom: 0.25rem;
}

.trail-card p {
    margin: 0 0 1rem 0;
    color: #aaa;
}

.unfavorite-btn {
    background: #e74c3c;
    border: none;
    padding: 0.5rem 1rem;
    border-radius: 6px;
    color: white;
    cursor: pointer;
    font-size: 0.95rem;
    transition: background-color 0.2s ease;
    margin-top: 0.5rem;
}

.unfavorite-btn:hover {
    background: #c0392b;
}

.unfavorite-form {
    all: unset;
    display: inline;
}
</style>
