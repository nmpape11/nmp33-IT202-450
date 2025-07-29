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
$stmt = $db->prepare("SELECT * FROM `IT202-M25-Trails` ORDER BY `modified` DESC");
$stmt->execute();
$trails = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<h1>Manage Trails</h1>
<p><a href="create_trail.php" class="btn">+ Create New Trail</a></p>

<table class="admin-table">
    <thead>
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>City</th>
            <th>State</th>
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
                <td class="action-buttons">
                    <a href="edit_trail.php?id=<?php se($trail, 'id'); ?>" class="btn edit">Edit</a>
                    <form method="POST" action="delete_trail.php" style="display: inline;" onsubmit="return confirm('Are you sure?');">
                        <input type="hidden" name="id" value="<?php se($trail, 'id'); ?>">
                        <button type="submit" class="btn delete">Delete</button>
                    </form>
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
    padding: 5px 12px;
    font-size: 0.9rem;
    border: none;
    border-radius: 4px;
    text-decoration: none;
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

form {
    margin: 0;
    padding: 0;
}

form button {
    display: inline-block;
}

</style>

<?php require(__DIR__ . "/../../../partials/flash.php"); ?>
