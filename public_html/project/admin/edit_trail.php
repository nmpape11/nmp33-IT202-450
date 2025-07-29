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
$id = $_GET['id'] ?? $_POST['id'] ?? null;

if (!$id) {
    flash("No trail ID provided.", "danger");
    die(header("Location: admin_trails.php"));
}

// Fetch current data
$stmt = $db->prepare("SELECT * FROM `IT202-M25-Trails` WHERE id = :id");
$stmt->bindValue(":id", $id, PDO::PARAM_INT);
$stmt->execute();
$trail = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$trail) {
    flash("Trail not found.", "danger");
    die(header("Location: admin_trails.php"));
}

// Handle update
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = trim($_POST["name"] ?? "");
    $city = trim($_POST["city"] ?? "");
    $state = trim($_POST["state"] ?? "");
    $description = trim($_POST["description"] ?? "");

    if (empty($name) || empty($city) || empty($state)) {
        flash("Name, city, and state are required.", "warning");
    } else {
        $stmt = $db->prepare("UPDATE `IT202-M25-Trails` SET name = :name, city = :city, state = :state, description = :description WHERE id = :id");
        $stmt->bindValue(":name", $name);
        $stmt->bindValue(":city", $city);
        $stmt->bindValue(":state", $state);
        $stmt->bindValue(":description", $description);
        $stmt->bindValue(":id", $id, PDO::PARAM_INT);

        try {
            $stmt->execute();
            flash("Trail updated successfully.", "success");
            die(header("Location: admin_trails.php"));
        } catch (Exception $e) {
            flash("Error updating trail: " . $e->getMessage(), "danger");
        }
    }
}
?>

<h1>Edit Trail #<?php echo htmlspecialchars($id); ?></h1>

<form method="POST" onsubmit="return validateForm(this)">
    <input type="hidden" name="id" value="<?php echo htmlspecialchars($id); ?>">

    <div class="form-item">
        <label for="name">Trail Name</label>
        <input type="text" id="name" name="name" value="<?php se($trail, 'name'); ?>" required>
    </div>

    <div class="form-item">
        <label for="city">City</label>
        <input type="text" id="city" name="city" value="<?php se($trail, 'city'); ?>" required>
    </div>

    <div class="form-item">
        <label for="state">State</label>
        <input type="text" id="state" name="state" value="<?php se($trail, 'state'); ?>" required>
    </div>

    <div class="form-item">
        <label for="description">Description</label>
        <textarea id="description" name="description" rows="5"><?php se($trail, 'description'); ?></textarea>
    </div>

    <input type="submit" value="Update Trail" class="btn">
</form>

<script>
function validateForm(form) {
    const name = form.name.value.trim();
    const city = form.city.value.trim();
    const state = form.state.value.trim();

    if (!name || !city || !state) {
        alert("Trail name, city, and state are required.");
        return false;
    }

    return true;
}
</script>

<?php require(__DIR__ . "/../../../partials/flash.php"); ?>
