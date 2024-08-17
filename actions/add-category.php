<?php

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    http_response_code(405);
    exit;
}

require_once __DIR__ . "/../database/db_access.php";
$sessionAccess = require_once __DIR__ . "/../database/models/db_sessions.php";
$discussionAccess = require_once __DIR__ . "/../database/models/db_discussions.php";

$user = $sessionAccess->isLoggedIn();
$data = json_decode(file_get_contents("php://input"), true);

if ($user->role !== "administrator") {
    http_response_code(403);
    exit;
}

$name = filter_var($data["name"], FILTER_SANITIZE_FULL_SPECIAL_CHARS) ?? "";
$icon = filter_var($data["icon"], FILTER_SANITIZE_FULL_SPECIAL_CHARS) ?? "";

if (!$name || !$icon) {
    http_response_code(400);
    echo $icon;
    echo $name;
    exit;
}

$discussionAccess->createCategory($name, $icon);
