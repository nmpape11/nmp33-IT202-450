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

//nmp33 7/28

$db = getDB();

$id = $_GET['id'] ?? $_POST['id'] ?? null;
if (!$id) {
    flash("No trail ID provided.", "danger");
    die(header("Location: admin_trails.php"));
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $confirm = $_POST['confirm'] ?? '';
    if (strtolower(trim($confirm)) === "delete") {
        $stmt = $db->prepare("DELETE FROM `IT202-M25-Trails` WHERE id = :id");
        $stmt->bindValue(":id", $id, PDO::PARAM_INT);
        try {
            $stmt->execute();
            flash("Trail deleted successfully.", "success");
        } catch (Exception $e) {
            flash("Error deleting trail: " . $e->getMessage(), "danger");
        }
        die(header("Location: admin_trails.php"));
    } else {
        flash("You must type 'delete' to confirm.", "warning");
    }
}
?>

<h1>Delete Trail #<?php echo htmlspecialchars($id); ?></h1>
<p>To confirm deletion of this trail, type <strong>delete</strong> below and submit.</p>

<form method="POST" onsubmit="return validateDelete(this);">
    <input type="hidden" name="id" value="<?php echo htmlspecialchars($id); ?>">
    <div class="form-item">
        <label for="confirm">Type 'delete' to confirm:</label>
        <input type="text" id="confirm" name="confirm" required pattern="[Dd][Ee][Ll][Ee][Tt][Ee]">
    </div>
    <input type="submit" value="Confirm Delete" class="btn delete">
</form>

<script>
function validateDelete(form) {
    const val = form.confirm.value.trim().toLowerCase();
    if (val !== "delete") {
        alert("You must type 'delete' exactly to confirm.");
        return false;
    }
    return true;
}
</script>

<?php require(__DIR__ . "/../../../partials/flash.php"); ?>
