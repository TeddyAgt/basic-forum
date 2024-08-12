<?php

if ($_SERVER["REQUEST_METHOD"] !== "GET") {
  http_response_code(405);
  exit;
}

require_once __DIR__ . "/../database/db_access.php";
$sessionAccess = require_once __DIR__ . "/../database/models/db_sessions.php";
$user = $sessionAccess->isLoggedIn();
$action = filter_input(INPUT_GET, "action", FILTER_SANITIZE_FULL_SPECIAL_CHARS) ?? "";
$userId = (int) filter_input(INPUT_GET, "id", FILTER_SANITIZE_NUMBER_INT) ?? false;

if (!$action) {
  http_response_code(400);
  exit;
}


if (!$userId) {
  $userId = $user->id;
}

$followUpsAccess = require_once "./../database/models/db_follow_ups.php";

if ($action === "follower") {
  $data = $followUpsAccess->getFollowUpsByFollower($userId);
} else {
  $data = $followUpsAccess->getFollowUpsByFollowee($userId);
}

// if (!count($data)) {
//   http_response_code(404);
//   exit;
// }

echo json_encode($data);
