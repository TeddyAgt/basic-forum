<?php
require_once __DIR__ . "/database/db_access.php";
$userAccess = require_once __DIR__ . "/database/models/db_users.php";
$sessionAccess = require_once __DIR__ . "/database/models/db_sessions.php";

$user = $sessionAccess->isLoggedIn();

if (!$user) {
  header("Location: /login.php");
}

$userProfile = $userAccess->getUserProfile($user["id"]);
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

    <section class="profile-section black-card section-1200">
      <header class="profile-section__header">
        <h1 class="main-title"><?= (int) $date->format("h") > 8 && (int) $date->format("h") < 18 ? "Bonjour" . " " . $user["username"] : "Bonsoir" . " " . $user["username"]; ?></h1>

        <a href="#" class="header__profile-picture">
          <i class="profile-picture__overlay fa-solid fa-pencil" aria-hidden="true"></i>
          <img class="profile-picture__img" src="<?= $userProfile["profile_picture"]; ?>" alt="">
        </a>
      </header>

      <div class="profile-section__content">
        <aside class="profile-section__sidebar">
          <article class="sidebar__user-data">

            <!-- User infos -->
            <h2 class="section-title">Informations</h2>
          </article>

          <!-- User stats -->
          <article class="sidebar__stats">
            <h2 class="section-title">Statistiques</h2>

            <ul class="stats-list">
              <li class="stats-list__item">Messages: <span><?= $userProfile["nbr_of_messages"]; ?></span></li>
              <li class="stats-list__item">Discussions: <span><?= $userProfile["nbr_of_discussions"]; ?></span></li>
              <li class="stats-list__item">Réactions: <span><?= $userProfile["nbr_of_likes"]; ?></span></li>
              <li class="stats-list__item">Abonnements: <span><?= $userProfile["nbr_of_follow_ups"]; ?></span></li>
              <li class="stats-list__item">Abonnés: <span><?= $userProfile["nbr_of_followers"]; ?></span></li>
            </ul>

          </article>
        </aside>

        <!-- User activities -->
        <article class="profile-section__feed">
          <h2 class="section-title">Activités</h2>

          <button type="button" class="btn btn--white btn--inline see-latests-btn see-latests-btn--active" data-latests="discussions">Derniers sujets</button>
          <button type="button" class="btn btn--white btn--inline see-latests-btn" data-latests="messages">Derniers messages</button>
          <div class="separator--horizontal"></div>

          <!-- Dernières discussions de l'utilisateur -->
          <div class="latests-article latests-article--discussions active">
            <h3>Dernières discussions</h3>

            <ul class="latests-list">

              <!-- On repli ca en JS -->

            </ul>
          </div>

          <!-- Derniers messages de l'utilisateur -->
          <div class="latests-article latests-article--messages">
            <h3>Derniers messages</h3>

            <ul class="latests-list">

              <!-- On rempli ca en JS -->

            </ul>
          </div>
        </article>
      </div>




    </section>

  </main>

  <?php require_once "./includes/footer.php"; ?>
  <script src="./public/js/app.js"></script>
  <script src="./public/js/profile.js"></script>
</body>

</html>
<!-- (👉ﾟヮﾟ)👉 -->