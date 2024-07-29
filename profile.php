<?php
require_once __DIR__ . "/database/db_access.php";
$userAccess = require_once __DIR__ . "/database/models/db_users.php";
$sessionAccess = require_once __DIR__ . "/database/models/db_sessions.php";

$user = $sessionAccess->isLoggedIn();

if (!$user) {
  header("Location: /login.php");
}

$date = new DateTimeImmutable("now", new DateTimeZone("Europe/Paris"));
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

  <main class="center">

    <section class="profile-section black-card">
      <h1 class="main-title"><?= (int) $date->format("h") > 8 && (int) $date->format("h") < 18 ? "Bonjour" : "Bonsoir" . " " . $user["username"]; ?></h1>


    </section>

  </main>

  <?php require_once "./includes/footer.php"; ?>
  <script src="./public/js/app.js"></script>
</body>

</html>
<!-- (👉ﾟヮﾟ)👉 -->