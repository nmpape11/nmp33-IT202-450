<?php
require(__DIR__ . "/../../partials/nav.php");
error_log("Session: " . var_export($_SESSION, true));

// Fetch most favorited trails
$db = getDB();
$stmt = $db->prepare("
    SELECT t.*, COUNT(f.user_id) AS favorite_count
    FROM `IT202-M25-Trails` t
    LEFT JOIN user_trail_favorites f ON f.trail_id = t.id
    GROUP BY t.id
    ORDER BY favorite_count DESC
    LIMIT 10
");
$stmt->execute();
$popular_trails = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<main>
<?php if (is_logged_in(true)): ?>
    <h1>Welcome, <?php echo get_username(); ?>!</h1>

    <h2>Popular Trails</h2>
    <?php if (count($popular_trails) > 0): ?>
        <div class="trail-grid">
            <?php foreach ($popular_trails as $trail): ?>
                <a href="trail.php?id=<?php se($trail, 'id'); ?>" class="trail-card-link">
                    <div class="trail-card">
                        <h3><?php se($trail, "name"); ?></h3>
                        <p><strong>City:</strong> <?php se($trail, "city"); ?>, <strong>State:</strong> <?php se($trail, "state"); ?></p>
                        <p><strong>Favorited By:</strong> <?= (int)$trail["favorite_count"] ?> user<?= $trail["favorite_count"] == 1 ? "" : "s" ?></p>
                    </div>
                </a>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <p>No trails have been favorited yet.</p>
    <?php endif; ?>
<?php endif; ?>
</main>

<?php require(__DIR__ . "/../../partials/flash.php"); ?>

<style>
.trail-card-link {
    all: unset;
    display: block;
    cursor: pointer;
    text-decoration: none;
    color: inherit;
}

.trail-card-link:hover .trail-card {
    background-color: #2a2a2a;
    transform: scale(1.03);
}

.trail-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 1rem;
    margin-top: 1rem;
}

.trail-card {
    background-color: #1e1e1e;
    border-radius: 8px;
    padding: 1rem;
    color: #eee;
    box-shadow: 0 2px 4px rgba(0,0,0,0.3);
    transition: transform 0.2s ease, background-color 0.2s ease;
}
</style>
