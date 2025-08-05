<?php
require(__DIR__ . "/../../partials/nav.php");

$db = getDB();
$results = [];

if (isset($_GET["query"])) {
    $search = "%" . $_GET["query"] . "%";
    $stmt = $db->prepare("SELECT id, username FROM Users WHERE username LIKE :query LIMIT 25");
    $stmt->execute([":query" => $search]);
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>

<h1 style="text-align:center;">Search Users</h1>

<form method="GET" class="search-form">
    <input type="text" name="query" placeholder="Enter username..." value="<?= se($_GET["query"] ?? "") ?>">
    <button type="submit">Search</button>
</form>

<?php if (!empty($results)): ?>
    <div class="results-grid">
        <?php foreach ($results as $user): ?>
            <a class="user-card" href="user.php?id=<?= se($user["id"]) ?>">
                <h2><?= se($user["username"]) ?></h2>
            </a>
        <?php endforeach; ?>
    </div>
<?php elseif (isset($_GET["query"])): ?>
    <p style="text-align:center;">No users found.</p>
<?php endif; ?>

<?php require(__DIR__ . "/../../partials/flash.php"); ?>

<style>
a {
    text-decoration: none;
    color: inherit;
}

.search-form {
    display: flex;
    justify-content: center;
    align-items: center;
    gap: 1rem;
    margin: 2rem 0;
}

.search-form input[type="text"] {
    padding: 0.5rem 1rem;
    border-radius: 6px;
    border: 1px solid #444;
    background-color: #2a2a2a;
    color: white;
    width: 300px;
}

.search-form button {
    padding: 0.5rem 1.2rem;
    background-color: #444;
    color: white;
    border: none;
    border-radius: 6px;
    cursor: pointer;
    font-weight: bold;
    transition: background-color 0.2s ease;
}

.search-form button:hover {
    background-color: #666;
}

.results-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
    gap: 1rem;
    padding: 1rem;
    max-width: 1000px;
    margin: auto;
}

.user-card {
    background-color: #1e1e1e;
    border-radius: 10px;
    padding: 1rem;
    box-shadow: 0 0 8px rgba(0, 0, 0, 0.3);
    color: white;
    text-align: center;
    transition: background-color 0.2s ease;
}

.user-card:hover {
    background-color: #2a2a2a;
}
</style>
