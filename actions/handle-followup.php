<?php

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
  http_response_code(405);
  exit;
}

require_once __DIR__ . "/../database/db_access.php";
$sessionAccess = require_once __DIR__ . "/../database/models/db_sessions.php";
$followUpsAccess = require_once __DIR__ . "/../database/models/db_follow_ups.php";
$discussionAccess = require_once __DIR__ . "/../database/models/db_discussions.php";

$user = $sessionAccess->isLoggedIn();
$data = json_decode(file_get_contents("php://input"), true);

$followee = (int) filter_var($data["followeeId"], FILTER_SANITIZE_NUMBER_INT) ?? "";

if ($data["isFollowing"] === "true") {
  $followUpsAccess->unfollowUser($user->id, $followee);
} else {
  $followUpsAccess->followUser($user->id, $followee);
}
var_dump($data);
