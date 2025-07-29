<?php
require(__DIR__ . "/../../partials/nav.php");
if (!is_logged_in()) {
    flash("Must be logged in!", "danger");
    die(header("Location: login.php"));
}

$db = getDB();
$result = [];

$limit = isset($_POST["limit"]) ? max(1, min((int)$_POST["limit"], 100)) : 10;
//nmp33 7/28

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["state"])) {
    $state = $_POST["state"];
    $city = $_POST["city"] ?? null;
    $result = array_slice(get_trails_or_fetch($state, $city), 0, $limit);
} else {
    $stmt = $db->prepare("SELECT * FROM `IT202-M25-Trails` ORDER BY `modified` DESC LIMIT :limit");
    $stmt->bindValue(":limit", $limit, PDO::PARAM_INT);
    $stmt->execute();
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>

<script>
function validate(form) {
    let isValid = true;
    const flashDiv = document.getElementById("flash");
    if (flashDiv) flashDiv.innerHTML = "";

    const state = form.state.value.trim();
    const limit = parseInt(form.limit.value.trim());

    if (!state) {
        flash("State is required.", "danger");
        isValid = false;
    }

    if (isNaN(limit) || limit < 1 || limit > 100) {
        flash("Limit must be a number between 1 and 100.", "danger");
        isValid = false;
    }

    return isValid;
}
</script>

<h1>Trail Search</h1>
<p>Search for trails by city and state.</p>

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

<style>
label.hidden {
    padding-top: 20px;
}
</style>

<?php if (!empty($result)) : ?>
    <div class="trail-grid">
        <?php foreach ($result as $trail): ?>
            <a href="trail.php?id=<?php se($trail, 'id'); ?>" class="trail-card-link">
                <div class="trail-card">
                    <h2 class="trail-name"><?php se($trail, "name"); ?></h2>
                    <p class="trail-location"><?php se($trail, "city"); ?>, <?php se($trail, "state"); ?></p>
                </div>
            </a>
        <?php endforeach; ?>
    </div>
<?php elseif ($_SERVER["REQUEST_METHOD"] === "POST") : ?>
    <p>No results found.</p>
<?php endif; ?>

<?php require(__DIR__ . "/../../partials/flash.php"); ?>
