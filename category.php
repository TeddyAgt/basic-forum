<?php

$categoryId = filter_id(INPUT_GET, "id", FILTER_SANITIZE_NUMBER_INT) ?? "";

if (!$categoryId) {
  header("Location: /");
}

require_once __DIR__ . "/database/db_access.php";
// $userAccess = require_once __DIR__ . "/database/models/db_users.php";
// $sessionAccess = require_once __DIR__ . "/database/models/db_sessions.php";
$discussionAccess = require_once __DIR__ . "/database/models/db_discussions.php";

?>

<!DOCTYPE html>
<html lang="en">

<head> <!-- ᓚᘏᗢ -->
  <?php require_once "./includes/head.php"; ?>
  <link rel="stylesheet" href="./public/css/index.css">
  <title>Forum - Accueil</title>
</head>

<body>
  <?php require_once "./includes/header.php"; ?>

  <main>

    <section class="black-card section-1200">

    </section>

  </main>

  <?php require_once "./includes/footer.php"; ?>
  <script src="./public/js/app.js"></script>
</body>

</html>
<!-- (👉ﾟヮﾟ)👉 -->