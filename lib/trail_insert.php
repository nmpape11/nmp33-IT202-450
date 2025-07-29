<?php

function insert_trail(PDO $db, array $data): bool
{
    // Required fields
    if (empty($data["name"]) || empty($data["state"])) {
        error_log("Missing required fields for trail insert");
        return false;
    }

    // Final insert array
    $insertData = [
        "name" => $data["name"],
        "state" => $data["state"],
        "city" => $data["city"] ?? null,
        "description" => $data["description"] ?? null,
        "is_api" => (int)($data["is_api"] ?? 0)
    ];

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
