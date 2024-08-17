<?php

if ($_SERVER["REQUEST_METHOD"] !== "GET") {
    http_response_code(405);
    exit;
}

require_once __DIR__ . "/../database/db_access.php";
// $userAccess = require_once __DIR__ . "/../database/models/db_users.php";
$sessionAccess = require_once __DIR__ . "/../database/models/db_sessions.php";
$discussionAccess = require_once __DIR__ . "/../database/models/db_discussions.php";

$categoryId = filter_input(INPUT_GET, "id", FILTER_SANITIZE_NUMBER_INT) ?? "";

if ($categoryId) {
    $category = [$discussionAccess->getCategoryById($categoryId)];
    if (count($category)) {
        header("Content-type: application/json; charset=utf-8");
        echo json_encode($category);
    }
} else {
    // 
}
