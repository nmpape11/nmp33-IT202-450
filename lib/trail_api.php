<?php

$TRAIL_ACTIVITIES = [
    "hiking",
    "mountain biking",
    "camping",
    "caving",
    "trail running",
    "snow sports",
    "atv",
    "horseback riding"
];

require(__DIR__ . "/trail_insert.php");

// Add back $db param when cache function created
function fetch_trail_data($state, $city = null, $activity = null)
{
    global $TRAIL_ACTIVITIES;

    $db = getDB();

    $data = [
        "limit" => 1,
        "q-state_cont" => $state
    ];

    if (!empty($city)) {
        $data["q-city_cont"] = $city;
    }

    if (!empty($activity)) {
        $data["q-activities_activity_type_name_eq"] = $activity;
    }

    $endpoint = "https://trailapi-trailapi.p.rapidapi.com/activity/";
    $isRapidAPI = true;
    $rapidAPIHost = "trailapi-trailapi.p.rapidapi.com";

    $result = get($endpoint, "TRAIL_API_KEY", $data, $isRapidAPI, $rapidAPIHost);

    if ($result["status"] === 200 && isset($result["response"])) {
        $decoded = json_decode($result["response"], true);

        foreach ($decoded as $trailId => $trail) {
            error_log("== Parsed Trail Data ==");

            $name = $trail["name"] ?? '';
            $stateAbbr = strtoupper(substr($trail["state"] ?? '', 0, 2));
            $cityVal = $trail["city"] ?? '';
            $description = $trail["description"] ?? '';
            $is_api = 1;

            error_log("name: $name");
            error_log("state: $stateAbbr");
            error_log("city: $cityVal");
            error_log("description: $description");

            $activityFlags = [];
            foreach ($TRAIL_ACTIVITIES as $act) {
                $col = str_replace(" ", "_", strtolower($act));
                $activityFlags[$col] = 0;
            }

            if (isset($trail["activities"]) && is_array($trail["activities"])) {
                foreach ($trail["activities"] as $actDetails) {
                    $actName = $actDetails["activity_type_name"] ?? '';
                    $key = str_replace(" ", "_", strtolower($actName));
                    if (array_key_exists($key, $activityFlags)) {
                        $activityFlags[$key] = 1;
                    }
                }
            }

            foreach ($activityFlags as $k => $v) {
                error_log("$k: " . ($v ? 'true' : 'false'));
            }

            error_log("is_api: $is_api");

            // Insert using the passed DB handle
            insert_trail($db, array_merge([
                "name" => $name,
                "state" => $stateAbbr,
                "city" => $cityVal,
                "description" => $description,
                "is_api" => $is_api,
                "activities" => $trail["activities"] ?? []
            ], $activityFlags));
        }

        return $decoded;
    }

    return [];
}
