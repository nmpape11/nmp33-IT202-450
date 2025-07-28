<?php
require_once(__DIR__ . "/trail_api.php"); // for $TRAIL_ACTIVITIES

/**
 * Inserts a single trail record into the DB.
 *
 * @param PDO $db
 * @param array $data Trail data (can be API or manual form)
 * @return bool True on success, false on failure
 */
function insert_trail(PDO $db, array $data): bool
{
    global $TRAIL_ACTIVITIES;

    // Required fields
    if (empty($data["name"]) || empty($data["state"])) {
        error_log("Missing required fields for trail insert");
        return false;
    }

    // Initialize all activity flags to 0 (false)
    $activityFlags = [];
    foreach ($TRAIL_ACTIVITIES as $act) {
        $col = str_replace(" ", "_", strtolower($act)); // DB column
        $activityFlags[$col] = 0;
    }

    // If this came from API: parse activities array
    if (isset($data["activities"]) && is_array($data["activities"])) {
        foreach (array_keys($data["activities"]) as $activityName) {
            $key = str_replace(" ", "_", strtolower($activityName));
            if (array_key_exists($key, $activityFlags)) {
                $activityFlags[$key] = 1;
            }
        }
    }

    // If from manual form (checkboxes): use existing boolean flags
    foreach ($activityFlags as $key => $_) {
        if (isset($data[$key])) {
            $activityFlags[$key] = (int)(bool)$data[$key];
        }
    }

    // Final insert array
    $insertData = [
        "name" => $data["name"],
        "state" => strtoupper(substr($data["state"], 0, 2)),
        "city" => $data["city"] ?? null,
        "description" => $data["description"] ?? null,
        "is_api" => (int)($data["is_api"] ?? 0)
    ] + $activityFlags;

    // Build the query
    $columns = array_map(fn($col) => "`$col`", array_keys($insertData));
    $placeholders = array_map(fn($col) => ":$col", array_keys($insertData));
    $params = array_combine($placeholders, array_values($insertData));

    $sql = "INSERT INTO `IT202-M25-Trails` (" . implode(", ", $columns) . ") VALUES (" . implode(", ", $placeholders) . ")";
    error_log("Trail insert query: $sql");
    error_log("Trail insert params: " . var_export($params, true));

    try {
        $stmt = $db->prepare($sql);
        return $stmt->execute($params);
    } catch (PDOException $e) {
        error_log("Insert failed: " . $e->getMessage());
        return false;
    }
}
