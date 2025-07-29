<?php

require(__DIR__ . "/trail_insert.php");

//nmp33 7/28

function fetch_trail_data(PDO $db, $state, $city = null, $activity = null)
{
    $data = [
        "limit" => 100,
        "q-state_cont" => $state
    ];

    if (!empty($city)) {
        $data["q-city_cont"] = $city;
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
            $state = $trail["state"] ?? '';
            $cityVal = $trail["city"] ?? '';
            $description = $trail["description"] ?? '';
            $is_api = 1;

            error_log("name: $name");
            error_log("state: $state");
            error_log("city: $cityVal");
            error_log("description: $description");
            error_log("is_api: $is_api");

            // Insert using the passed DB handle
            insert_trail($db, [
                "name" => $name,
                "state" => $state,
                "city" => $cityVal,
                "description" => $description,
                "is_api" => $is_api
            ]);
        }

        return $decoded;
    }

    return [];
}
