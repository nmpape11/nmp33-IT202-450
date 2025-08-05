<?php
require(__DIR__ . "/../../../partials/nav.php");

if (!is_logged_in() || !has_role("Admin")) {
    flash("You don't have permission to access this page.", "danger");
    die(header("Location: " . get_url("login.php")));
}
?>

<div class="role-dashboard">
    <h1>Role Management</h1>
    <div class="button-group">
        <a href="create_role.php" class="role-btn">Create Role</a>
        <a href="list_roles.php" class="role-btn">List Roles</a>
        <a href="assign_roles.php" class="role-btn">Assign Roles</a>
    </div>
</div>

<style>
.role-dashboard {
    max-width: 600px;
    margin: 3rem auto;
    text-align: center;
}

.button-group {
    display: flex;
    flex-direction: column;
    gap: 1.5rem;
    margin-top: 2rem;
}

.role-btn {
    background-color: #444;
    color: white;
    padding: 1rem;
    border-radius: 8px;
    text-decoration: none;
    font-size: 1.1rem;
    font-weight: bold;
    transition: background-color 0.2s ease;
}

.role-btn:hover {
    background-color: #666;
}
</style>
