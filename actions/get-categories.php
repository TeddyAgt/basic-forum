<?php

require_once __DIR__ . "/database/db_access.php";
// $userAccess = require_once __DIR__ . "/database/models/db_users.php";
$sessionAccess = require_once __DIR__ . "/database/models/db_sessions.php";
$discussionAccess = require_once __DIR__ . "/database/models/db_discussions.php";

$categoryId = filter_input(INPUT_GET, "id", FILTER_SANITIZE_NUMBER_INT) ?? "";
var_dump($categoryId);
if ($categoryId) {
  $category = $discussionAccess->getCategoryById($categoryId);
} else {
  // 
}
