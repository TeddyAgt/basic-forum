<?php
require_once __DIR__ . "/database/db_access.php";
$sessionAccess = require_once __DIR__ . "/database/models/db_sessions.php";

$user = $sessionAccess->isLoggedIn();

if ($user) {
  $sessionAccess->deleteSession();
}

header("Location: /");
