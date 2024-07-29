<?php
require_once __DIR__ . "/database/db_access.php";
$userAccess = require_once __DIR__ . "/database/models/db_users.php";
$sessionAccess = require_once __DIR__ . "/database/models/db_sessions.php";
$subjectAccess = require_once __DIR__ . "/database/models/db_subjects.php";

$categoriesList = $subjectAccess->getAllCategories() ?? [];
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

    <section class="black-card index-header section-1200">
      <h1 class="main-title">Forum</h1>
      <a href="./subject-form.php" class="btn btn--primary">Commencer un nouveau sujet</a>
    </section>

    <!-- Catégories -->
    <section class="topics-section black-card section-1200">
      <h2 class="section-title">Catégories</h2>
      <div class="separator--horizontal"></div>

      <ul class="categories-list">

        <?php foreach ($categoriesList as $i => $category) : ?>

          <li class="categories-list__item <?= $i % 2 === 0 ? "categories-list__item--even" : "categories-list__item--odd" ?>">
            <a href="" class="">
              <i class="<?= $category["icon"] ?>" aria-hidden="true"></i>
              <?= $category["name"]; ?>
            </a>
          </li>

        <?php endforeach; ?>

      </ul>
    </section>

    <!-- Récents -->
    <section class="latests-section black-card section-1200">
      <button type="button" class="btn btn--inline see-latests-btn" data-latests="subjects">Derniers sujets</button>
      <button type="button" class="btn btn--inline see-latests-btn" data-latests="messages">Derniers messages</button>
      <div class="separator--horizontal"></div>

      <!-- Derniers sujets -->
      <article class="latests-article latests-article--subjects active">
        <h2 class="section-title">Derniers sujets</h2>
      </article>

      <!-- Derniers messages -->
      <article class="latests-article latests-article--messages">
        <h2 class="section-title">Derniers messages</h2>
      </article>

    </section>
  </main>

  <?php require_once "./includes/footer.php"; ?>
  <script src="./public/js/app.js"></script>
  <script src="./public/js/index.js"></script>
</body>

</html>
<!-- (👉ﾟヮﾟ)👉 -->