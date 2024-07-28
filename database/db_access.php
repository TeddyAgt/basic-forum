<?php

$dns = "mysql:host=localhost;dbname=forum";
$usr = getenv("usr");
$pwd = getenv("pwd");

try {
  $pdo = new PDO($dns, $usr, $pwd, [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
  ]);
} catch (PDOException $e) {
  throw new Exception($e->getMessage());
}

return $pdo;
