<?php
require(__DIR__ . "/../../partials/nav.php");

if (!isset($_GET["id"]) || !is_numeric($_GET["id"])) {
    flash("Invalid user", "danger");
    die(header("Location: index.php"));
}

$db = getDB();
$user_id = (int)$_GET["id"];

$user_stmt = $db->prepare("SELECT username FROM Users WHERE id = :uid");
$user_stmt->execute([":uid" => $user_id]);
$user = $user_stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    flash("User not found", "danger");
    die(header("Location: index.php"));
}

$stmt = $db->prepare("
    SELECT t.*
    FROM `IT202-M25-Trails` t
    JOIN user_trail_favorites f ON f.trail_id = t.id
    WHERE f.user_id = :uid
");
$stmt->execute([":uid" => $user_id]);
$trails = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<h1 style="text-align: center;"><?= se($user["username"]) ?>'s Favorited Trails</h1>

<?php if ($trails): ?>
    <div class="favorites-grid">
        <?php foreach ($trails as $trail): ?>
            <a class="trail-card" href="trail.php?id=<?= se($trail['id']) ?>">
                <h2><?= se($trail["name"]) ?></h2>
                <p><?= se($trail["city"]) ?>, <?= se($trail["state"]) ?></p>
            </a>
        <?php endforeach; ?>
    </div>
<?php else: ?>
    <p style="text-align: center; margin-top: 2rem;">No favorites yet.</p>
<?php endif; ?>

<style>

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
    color: white;
    transition: background-color 0.2s ease;
}

.trail-card:hover {
    background-color: #2a2a2a;
}

.trail-card h2 {
    font-size: 1.2rem;
    margin-bottom: 0.25rem;
}

.trail-card p {
    margin: 0 0 1rem 0;
    color: #aaa;
}

a.trail-card {
    text-decoration: none;
    color: inherit;
}

</style>
