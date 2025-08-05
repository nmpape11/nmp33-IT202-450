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

$db = getDB();

$city = $_GET["city"] ?? null;
$state = $_GET["state"] ?? null;
$limit = isset($_GET["limit"]) && is_numeric($_GET["limit"]) && $_GET["limit"] > 0 && $_GET["limit"] <= 100
    ? (int)$_GET["limit"]
    : 10;
$order = $_GET["order"] ?? "id"; // options: id, most, least

$orderBy = match ($order) {
    "most" => "favorite_count DESC",
    "least" => "favorite_count ASC",
    default => "t.id ASC"
};

$params = [];
$conditions = [];

if ($city) {
    $conditions[] = "t.city LIKE :city";
    $params[":city"] = "%$city%";
}
if ($state) {
    $conditions[] = "t.state LIKE :state";
    $params[":state"] = "%$state%";
}

$whereSQL = $conditions ? "WHERE " . implode(" AND ", $conditions) : "";

$query = "
    SELECT t.*, COUNT(f.user_id) AS favorite_count
    FROM `IT202-M25-Trails` t
    LEFT JOIN user_trail_favorites f ON f.trail_id = t.id
    $whereSQL
    GROUP BY t.id
    ORDER BY $orderBy
    LIMIT :limit
";

$stmt = $db->prepare($query);
foreach ($params as $k => $v) {
    $stmt->bindValue($k, $v);
}
$stmt->bindValue(":limit", $limit, PDO::PARAM_INT);
$stmt->execute();
$trails = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="filter-bar">
    <form method="GET" class="inline-form">
        <div class="form-item">
            <label for="city">City</label>
            <input type="text" name="city" id="city" value="<?php se($_POST, 'city'); ?>">
        </div>
        <div class="form-item">
            <label for="state">State</label>
            <input type="text" name="state" id="state" value="<?php se($_POST, 'state'); ?>">
        </div>
        <div class="form-item">
            <label for="limit">Result Limit (1–100)</label>
            <input type="number" name="limit" id="limit" min="1" max="100" value="<?php echo $limit; ?>">
        </div>
        <div class="form-item">
            <label class="hidden"></label>
            <input type="submit" value="Search">
        </div>
        <button type="submit" name="order" value="most" class="filter-btn">Most Favorited</button>
        <button type="submit" name="order" value="least" class="filter-btn">Least Favorited</button>
    </form>
</div>

<h1>Manage Trails</h1>
<p><a href="create_trail.php" class="btn">+ Create New Trail</a></p>

<table class="admin-table">
    <thead>
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>City</th>
            <th>State</th>
            <th>Favorited By</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($trails as $trail): ?>
            <tr>
                <td><?php se($trail, 'id'); ?></td>
                <td><?php se($trail, 'name'); ?></td>
                <td><?php se($trail, 'city'); ?></td>
                <td><?php se($trail, 'state'); ?></td>
                <td><?= (int)$trail["favorite_count"] ?> user<?= $trail["favorite_count"] == 1 ? "" : "s" ?></td>
                <td class="action-buttons">
                    <a href="edit_trail.php?id=<?php se($trail, 'id'); ?>" class="btn edit">Edit</a>

                    <form method="POST" action="delete_trail.php" style="all:unset;" onsubmit="return confirm('Delete this trail?');">
                        <input type="hidden" name="id" value="<?php se($trail, 'id'); ?>">
                        <button type="submit" class="btn delete">Delete</button>
                    </form>

                    <form method="POST" action="remove_trail_associations.php" style="all:unset;" onsubmit="return confirm('Remove all user associations?');">
                        <input type="hidden" name="trail_id" value="<?php se($trail, 'id'); ?>">
                        <button type="submit" class="btn warn">Clear Assoc.</button>
                    </form>

                    <a href="user_associations.php?trail_id=<?php se($trail, 'id'); ?>" class="btn link">View Users</a>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<style>
.admin-table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 2rem;
}

.admin-table th, .admin-table td {
    padding: 0.75rem 1rem;
    border-bottom: 1px solid #444;
    text-align: left;
    color: #ddd;
}

.admin-table th {
    background-color: #1a1a1a;
}

.btn {
    all: unset;
    padding: 5px 12px;
    font-size: 0.9rem;
    border-radius: 4px;
    color: white;
    cursor: pointer;
    transition: background 0.2s ease-in-out;
}

.btn.edit {
    background-color: #4caf50;
}

.btn.edit:hover {
    background-color: #45a049;
}

.btn.delete {
    background-color: #e53935;
}

.btn.delete:hover {
    background-color: #c62828;
}

.btn.warn {
    background-color: #f39c12;
}

.btn.warn:hover {
    background-color: #e67e22;
}

.btn.link {
    background-color: #3498db;
}

.btn.link:hover {
    background-color: #2980b9;
}

.filter-bar {
    display: flex;
    flex-direction: row;
    justify-content: center;
    margin: 1.5rem 0;
}

.hidden {
    padding-top: 20px;
}

.filter-btn {
    padding: 5px 12px;
    font-size: 0.9rem;
    border-radius: 4px;
    color: white;
    cursor: pointer;
    background-color: #444;
}
</style>

<?php require(__DIR__ . "/../../../partials/flash.php"); ?>
