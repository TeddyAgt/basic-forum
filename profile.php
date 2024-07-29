<?php
require_once __DIR__ . "/database/db_access.php";
$userAccess = require_once __DIR__ . "/database/models/db_users.php";
$sessionAccess = require_once __DIR__ . "/database/models/db_sessions.php";
?>


<!DOCTYPE html>
<html lang="en">

<head> <!-- ᓚᘏᗢ -->
  <?php require_once "./includes/head.php"; ?>
  <link rel="stylesheet" href="./public/css/profile.css">
  <title>Forum - Profil</title>
</head>

<body>
  <?php require_once "./includes/header.php"; ?>

  <main>Profil</main>

  <?php require_once "./includes/footer.php"; ?>
  <script src="./public/js/app.js"></script>
</body>

</html>
<!-- (👉ﾟヮﾟ)👉 -->