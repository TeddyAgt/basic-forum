<?php
require_once __DIR__ . "/database/db_access.php";
$userAccess = require_once __DIR__ . "/database/models/db_users.php";
$sessionAccess = require_once __DIR__ . "/database/models/db_sessions.php";

$user = $sessionAccess->isLoggedIn();
$profileUserId = (int) filter_input(INPUT_GET, "id", FILTER_SANITIZE_NUMBER_INT) ?? false;

if (!$user) {
  header("Location: /login.php");
}

if ($profileUserId && $user->id !== $profileUserId) {
  $userProfile = $userAccess->getUserProfile($profileUserId);
  $visitor = true;
  $isFollowing = $followUpsAccess->isFollowing($user->id, $profileUserId);
} else {
  $userProfile = $userAccess->getUserProfile($user->id);
  $visitor = false;
}

?>

<!DOCTYPE html>
<html lang="en">

<head> <!-- ᓚᘏᗢ -->
  <?php require_once "./includes/head.php"; ?>
  <link rel="stylesheet" href="./public/css/profile.css">
  <title><?= $userProfile["username"]; ?> - Forum</title>
</head>

<body>
  <?php require_once "./includes/header.php"; ?>

  <main class="center">

    <section class="profile-section black-card section-1200">
      <header class="profile-section__header" style="background-color: <?= $userProfile["banner_color"]; ?>;">
        <?php if ($visitor): ?>
          <?php if ($isFollowing): ?>
            <button class="btn btn--follow btn--follow-true" data-following="true" data-followee-id="<?= $userProfile["id"] ?>">Suivi</button>
          <?php else: ?>
            <button class="btn btn--follow btn--follow-false" data-following="false" data-followee-id="<?= $userProfile["id"] ?>">Suivre</button>
          <?php endif; ?>
        <?php endif; ?>
        <h1 class="main-title"><?= $userProfile["username"]; ?></h1>

        <?php if ($visitor): ?>
          <div class="header__profile-picture">
            <img class="profile-picture__img" src="<?= $userProfile["avatar"]; ?>" alt="">
          </div>
        <?php else: ?>
          <a href="./user-settings.php?id=<?= $userProfile["id"]; ?>" class="header__profile-picture">
            <i class="profile-picture__overlay fa-solid fa-gear" aria-hidden="true"></i>
            <img class="profile-picture__img" src="<?= $userProfile["avatar"]; ?>" alt="">
          </a>
        <?php endif; ?>
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