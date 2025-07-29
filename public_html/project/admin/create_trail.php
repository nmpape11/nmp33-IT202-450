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

// Get next ID
$stmt = $db->prepare("SELECT MAX(id) as max_id FROM `IT202-M25-Trails`");
$stmt->execute();
$row = $stmt->fetch(PDO::FETCH_ASSOC);
$next_id = $row ? $row['max_id'] + 1 : 1;

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = trim($_POST["name"] ?? "");
    $city = trim($_POST["city"] ?? "");
    $state = trim($_POST["state"] ?? "");
    $description = trim($_POST["description"] ?? "");
    $id = (int)($_POST["id"] ?? 0);
    $is_api = 0;

    if (!$name || !$city || !$state || $id <= 0) {
        flash("Name, city, state, and valid ID are required.", "danger");
    } else {
        $stmt = $db->prepare("INSERT INTO `IT202-M25-Trails` 
            (id, name, city, state, description, is_api) 
            VALUES 
            (:id, :name, :city, :state, :description, :is_api)");

        $stmt->bindValue(":id", $id, PDO::PARAM_INT);
        $stmt->bindValue(":name", $name);
        $stmt->bindValue(":city", $city);
        $stmt->bindValue(":state", $state);
        $stmt->bindValue(":description", $description);
        $stmt->bindValue(":is_api", $is_api, PDO::PARAM_INT);

        try {
            $stmt->execute();
            flash("Trail created successfully.", "success");
            die(header("Location: admin_trails.php"));
        } catch (Exception $e) {
            flash("Error creating trail: " . $e->getMessage(), "danger");
        }
    }
}
?>

<h1>Create New Trail</h1>

<form method="POST" onsubmit="return validateTrailForm(this)">
    <input type="hidden" name="is_api" value="0">

    <div class="form-item">
        <label for="id">ID (auto-filled)</label>
        <input type="number" id="id" name="id" value="<?php echo $next_id; ?>" readonly>
    </div>

    <div class="form-item">
        <label for="name">Trail Name</label>
        <input type="text" id="name" name="name" required maxlength="255">
    </div>

    <div class="form-item">
        <label for="city">City</label>
        <input type="text" id="city" name="city" required maxlength="100">
    </div>

    <div class="form-item">
        <label for="state">State</label>
        <input type="text" id="state" name="state" required maxlength="100">
    </div>

    <div class="form-item">
        <label for="description">Description</label>
        <textarea id="description" name="description" rows="4" maxlength="1000"></textarea>
    </div>

    <input type="submit" value="Create Trail" class="btn">
</form>

<script>
function validateTrailForm(form) {
    const name = form.name.value.trim();
    const city = form.city.value.trim();
    const state = form.state.value.trim();

    if (!name || !city || !state) {
        alert("Name, city, and state are required.");
        return false;
    }

    return true;
}
</script>

<?php require(__DIR__ . "/../../../partials/flash.php"); ?>
