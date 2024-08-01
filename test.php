<?php
// $timestamp = strtotime("2024-07-29 19:49:12");
// echo "<br>";
// echo $timestamp;
// echo "<br>";
// echo date("d/m/y", $timestamp);
// echo "<br>";
// $date = new DateTime("2024-07-29 19:49:12");
// var_dump($date);
// echo "<br>";
// $diff = $date->diff(new DateTimeImmutable(date("Y-m-d H:i:s", time())));

// var_dump($diff);
// echo "<br>";
// echo "<br>";
// echo "<br>";
// echo "<br>";
require_once __DIR__ . "/database/db_access.php";
$userAccess = require_once __DIR__ . "/database/models/db_users.php";
$sessionAccess = require_once __DIR__ . "/database/models/db_sessions.php";
$discussionAccess = require_once __DIR__ . "/database/models/db_discussions.php";

$latests = $discussionAccess->getLast10Discussions();

echo "<pre>";
var_dump($latests);
echo "</pre>";
