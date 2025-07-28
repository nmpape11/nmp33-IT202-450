<?php
require(__DIR__ . "/../../partials/nav.php");

//lists all activities
global $TRAIL_ACTIVITIES;

$result = [];



if (isset($_GET["state"])) {
    $state = $_GET["state"];
    $city = $_GET["city"] ?? null;
    $activity = $_GET["activity"] ?? null;

    $apiData = fetch_trail_data($state, $city, $activity);

    if (!empty($apiData["places"])) {
        // Only now open DB connection (we know work is needed)
        echo "here";
        $db = getDB();

        foreach ($apiData["places"] as $trail) {
            $trail["is_api"] = 1;
            insert_trail($db, $trail);
        }

        $result = $apiData["places"];
    }
}
?>

<div class="container-fluid">
    <h1>Hiking & Outdoor Activity Search</h1>
    <p>Search for outdoor activities based on city, state, and type of activity.</p>

    <form>
        <div>
            <label>City</label>
            <input name="city" value="<?php se($_GET, 'city'); ?>" />
        </div>
        <div>
            <label>State <span style="color:red;">*</span></label>
            <input name="state" value="<?php se($_GET, 'state'); ?>" required />
        </div>
        <div>
            <label>Activity</label>
            <select name="activity">
                <option value="">-- Any --</option>
                <?php foreach ($TRAIL_ACTIVITIES as $activity) : ?>
                    <option value="<?php echo $activity; ?>" <?php if (isset($_GET["activity"]) && $_GET["activity"] == $activity) echo "selected"; ?>>
                        <?php echo ucfirst($activity); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <input type="submit" value="Search" />
    </form>

    <div class="row">
        <?php if (!empty($result)) : ?>
            <h2>Results:</h2>
            <pre><?php var_export($result); ?></pre>
        <?php elseif ($_GET) : ?>
            <p>No results found or failed request.</p>
        <?php endif; ?>
    </div>
</div>

<?php require(__DIR__ . "/../../partials/flash.php"); ?>
