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

$state = $_GET["state"] ?? null;
$city = $_GET["city"] ?? null;

$whereClause = "WHERE f.user_id = :uid";
$params = [":uid" => $user_id];

if ($state) {
    $whereClause .= " AND t.state LIKE :state";
    $params[":state"] = "%" . $state . "%";
}

if ($city) {
    $whereClause .= " AND t.city LIKE :city";
    $params[":city"] = "%" . $city . "%";
}

// Count total matching records
$countStmt = $db->prepare("
    SELECT COUNT(*) as total
    FROM `IT202-M25-Trails` t
    JOIN user_trail_favorites f ON f.trail_id = t.id
    $whereClause
");
$countStmt->execute($params);
$total = $countStmt->fetchColumn();

$stmt = $db->prepare("
    SELECT t.*
    FROM `IT202-M25-Trails` t
    JOIN user_trail_favorites f ON f.trail_id = t.id
    $whereClause
    LIMIT :limit
");
foreach ($params as $key => $val) {
    $stmt->bindValue($key, $val);
}
$stmt->bindValue(":limit", $limit, PDO::PARAM_INT);
$stmt->execute();
$favorites = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<h1 style="text-align:center;">Your Favorited Trails</h1>
<p style="text-align:center;">You have <?= $total ?> trail<?= $total != 1 ? 's' : '' ?> favorited<?= $state || $city ? " (filtered)" : "" ?>.</p>

<div class="filter-bar">
    <form method="POST" onsubmit="return validate(this)" class="inline-form">
    <div class="form-item">
        <label for="city">City</label>
        <input type="text" name="city" id="city" value="<?php se($_POST, 'city'); ?>">
    </div>
    <div class="form-item">
        <label for="state">State <span style="color:red">*</span></label>
        <input type="text" name="state" id="state" required value="<?php se($_POST, 'state'); ?>">
    </div>
    <div class="form-item">
        <label for="limit">Result Limit (1–100)</label>
        <input type="number" name="limit" id="limit" min="1" max="100" value="<?php echo $limit; ?>">
    </div>
    <div class="form-item">
        <label class="hidden"></label>
        <input type="submit" value="Search">
    </div>
</form>
</div>

<div style="text-align:center; margin-bottom: 1rem;">
    <form method="POST" action="clear_favorites.php" onsubmit="return confirm('Are you sure you want to remove all favorites?');">
        <button type="submit" class="unfavorite-btn">Clear All Favorites</button>
    </form>
</div>

<?php if ($favorites): ?>
    <div class="favorites-grid">
        <?php foreach ($favorites as $trail): ?>
            <div class="trail-card">
                <a class="card-link" href="trail.php?id=<?= se($trail['id']) ?>">
                    <h2><?= se($trail["name"]) ?></h2>
                    <p><?= se($trail["city"]) ?>, <?= se($trail["state"]) ?></p>
                </a>
                <form method="POST" action="unfavorite_trail.php" class="unfavorite-form">
                    <input type="hidden" name="trail_id" value="<?= se($trail["id"]) ?>">
                    <input type="hidden" name="redirect" value="user_favorites.php">
                    <button type="submit" class="unfavorite-btn">Unfavorite</button>
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
