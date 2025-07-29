<?php
require_once(__DIR__ . "/trail_api.php");

function isFullyCached(PDO $db, string $state, ?string $city): bool {
    $state = ucwords(strtolower($state));

    // If entire state is cached, return true 
    $stmt = $db->prepare("SELECT 1 FROM `Trail-Cache-Log` WHERE `state` = :state AND `city` IS NULL LIMIT 1");
    $stmt->execute([":state" => $state]);
    if ($stmt->fetchColumn()) {
        return true;
    }

    $sql = "SELECT 1 FROM `Trail-Cache-Log` 
            WHERE `state` = :state 
              AND (`city` = :city OR (`city` IS NULL AND :city IS NULL))
            LIMIT 1";
    $stmt = $db->prepare($sql);
    $stmt->execute([
        ":state" => $state,
        ":city" => $city
    ]);
    return (bool)$stmt->fetchColumn();
}

function markFullyCached(PDO $db, string $state, ?string $city) {
    $state = ucwords(strtolower($state));
    
    $sql = "INSERT INTO `Trail-Cache-Log` (`state`, `city`) 
            VALUES (:state, :city)
            ON DUPLICATE KEY UPDATE `modified` = CURRENT_TIMESTAMP";
    $stmt = $db->prepare($sql);
    $stmt->execute([
        ":state" => $state,
        ":city" => $city
    ]);
}

//nmp33 7/28
function get_trails_or_fetch(string $state, ?string $city = null) {
    $db = getDB();

    if (isFullyCached($db, $state, $city)) {
        error_log("Cache hit for $state, $city");
        return queryCachedTrails($db, $state, $city);
    }

    error_log("Cache miss for $state, $city — fetching from API");
    $apiResults = fetch_trail_data($db, $state, $city);

    if (!empty($apiResults)) {
        // Mark whole state as cached if no city is used
        markFullyCached($db, $state, $city);
    }

    return queryCachedTrails($db, $state, $city);
}

function queryCachedTrails(PDO $db, string $state, ?string $city) {
    $sql = "SELECT * FROM `IT202-M25-Trails` WHERE `state` = :state";
    $params = [":state" => ucwords(strtolower($state))];

    if (!empty($city)) {
        $sql .= " AND `city` = :city";
        $params[":city"] = $city;
    }

    $sql .= " ORDER BY `modified` DESC";

    try {
        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("DB query error: " . $e->getMessage());
        return [];
    }
}
