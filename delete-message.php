<?php
echo "<pre>";
var_dump($_SERVER);
echo "</pre>";
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
  http_response_code(405);
  exit;
}

require_once __DIR__ . "/database/db_access.php";
$userAccess = require_once __DIR__ . "/database/models/db_users.php";
$sessionAccess = require_once __DIR__ . "/database/models/db_sessions.php";
$discussionAccess = require_once __DIR__ . "/database/models/db_discussions.php";

$user = $sessionAccess->isLoggedIn();

if (!$user) {
  http_response_code(403);
  exit;
} else {
  // $password = $_POST["password"] ?? "";
  $messageId = filter_input(INPUT_POST, "message-id", FILTER_SANITIZE_NUMBER_INT) ?? "";

  // if (!$password) {
  //   http_response_code(403);
  //   exit;
  // }
  if (!$messageId) {
    http_response_code(403);
    exit;
  }

  if ($discussionAccess->GetMessageAuthor($messageId) === $user["id"]) {
    $discussionAccess->deleteMessage("Ce message a été supprimé par l'utilisateur.", $messageId);
  } elseif ($user["role"] === "administrator" || $user["role"] === "moderator") {
    $discussionAccess->deleteMessage("Ce message a été supprimé par un modérateur.", $messageId);
  }

  header("Location: " . $_SERVER["HTTP_REFERER"]);
}
