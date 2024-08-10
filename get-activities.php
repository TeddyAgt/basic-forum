<?php

if ($_SERVER["REQUEST_METHOD"] !== "GET") {
  http_response_code(405);
  exit;
}

require_once __DIR__ . "/database/db_access.php";
// $userAccess = require_once __DIR__ . "/database/models/db_users.php";
$sessionAccess = require_once __DIR__ . "/database/models/db_sessions.php";
$discussionAccess = require_once __DIR__ . "/database/models/db_discussions.php";

$user = $sessionAccess->isLoggedIn();
$profileId = (int) filter_input(INPUT_GET, "id", FILTER_SANITIZE_NUMBER_INT) ?? false;

if (!$user) {
  http_response_code(403);
} elseif ($profileId) {
  $activities = [
    "discussions" => $discussionAccess->getLastNDiscussionsByUser(userId: $profileId),
    "messages" => $discussionAccess->getLastNMessagesByUser(userId: $profileId)
  ];
  header("Content-type: application/json; charset=utf-8");
  echo json_encode($activities);
} else {
  $activities = [
    "discussions" => $discussionAccess->getLastNDiscussionsByUser(userId: $user->id),
    "messages" => $discussionAccess->getLastNMessagesByUser(userId: $user->id)
  ];
  header("Content-type: application/json; charset=utf-8");
  echo json_encode($activities);
}
