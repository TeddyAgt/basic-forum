<?php
require_once "./database/db_access.php";

$statementTest = $pdo->prepare("
  INSERT INTO TEST (name) VALUES (:name);
");

$statementTest->bindValue(":name", "toto");
$statementTest->execute();
$bite =  $pdo->lastInsertId();
echo $bite;
