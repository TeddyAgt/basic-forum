<?php

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
  http_response_code(405);
  exit;
}

require_once __DIR__ . "/database/db_access.php";
$sessionAccess = require_once __DIR__ . "/database/models/db_sessions.php";
$likeAccess = require_once __DIR__ . "/database/models/db_likes.php";
$discussionAccess = require_once __DIR__ . "/database/models/db_discussions.php";

$user = $sessionAccess->isLoggedIn();
$messageId = (int) json_decode(file_get_contents("php://input"), true)["messageId"];

if (!$user) {
  http_response_code(403);
} elseif ($discussionAccess->GetMessageAuthor($messageId) == $user->id) {
  http_response_code(403);
} elseif (!$messageId) {
  http_response_code(400);
} elseif ($likeAccess->hasLiked($messageId, $user->id)) {
  $nbrOfLike = $likeAccess->deleteOneLike($messageId, $user->id);
  header("Content-type: application/json; charset=utf-8");
  echo json_encode($nbrOfLike);
} else {
  $nbrOfLike = $likeAccess->createOneLike($messageId, $user->id);
  header("Content-type: application/json; charset=utf-8");
  echo json_encode($nbrOfLike);
}
