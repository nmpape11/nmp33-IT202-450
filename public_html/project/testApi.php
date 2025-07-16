<?php
require(__DIR__ . "/../../partials/nav.php");

$activities = [
    "hiking",
    "mountain biking",
    "camping",
    "caving",
    "trail running",
    "snow sports",
    "atv",
    "horseback riding"
];

//nmp33 7/15
$result = [];
if (isset($_GET["city"]) && isset($_GET["state"])) {
    $data = [
        "limit" => 1, //number of results
        "q-city_cont" => $_GET["city"],
        "q-state_cont" => $_GET["state"],
        "q-activities_activity_type_name_eq" => $_GET["activity"]
    ];

    $endpoint = "https://trailapi-trailapi.p.rapidapi.com/activity/";
    $isRapidAPI = true;
    $rapidAPIHost = "trailapi-trailapi.p.rapidapi.com";
    $result = get($endpoint, "TRAIL_API_KEY", $data, $isRapidAPI, $rapidAPIHost);
    
    error_log("Response: " . var_export($result, true));

    if (se($result, "status", 400, false) == 200 && isset($result["response"])) {
    $decoded = json_decode($result["response"], true);

        // Check if the response is an error with "invalid_input" code
        if (isset($decoded["code"]) && $decoded["code"] === "invalid_input") {
            $result = ["error" => $decoded["message"] ?? "Invalid request."];
        } 
        else {
            $result = $decoded;
        }
    }
    else {
        $result = [];
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
            <label>State</label>
            <input name="state" value="<?php se($_GET, 'state'); ?>" required />
        </div>
        <div>
            <label>Activity</label>
            <select name="activity">
                <?php foreach ($activities as $activity) : ?>
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

<?php
require(__DIR__ . "/../../partials/flash.php");
?>