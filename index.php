<?php
require_once __DIR__ . "/database/db_access.php";
$userAccess = require_once __DIR__ . "/database/models/db_users.php";
$sessionAccess = require_once __DIR__ . "/database/models/db_sessions.php";
$discussionAccess = require_once __DIR__ . "/database/models/db_discussions.php";

$categoriesList = $discussionAccess->getAllCategories() ?? [];

$latests = [
  "discussions" => $discussionAccess->getLastNDiscussions(),
  "messages" => $discussionAccess->getLastNMessages()
];

// echo "<pre>";
// var_dump($latests);
// echo "</pre>";
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
      <a href="./discussion-form.php" class="btn btn--primary">Commencer un nouveau sujet</a>
    </section>

    <!-- Catégories -->
    <section class="topics-section black-card section-1200">
      <h2 class="section-title">Catégories</h2>
      <div class="separator--horizontal"></div>

      <ul class="categories-list">

        <?php foreach ($categoriesList as $i => $category) : ?>

          <li class="categories-list__item index-list__item">
            <a href="">
              <i class="<?= $category["icon"] ?>" aria-hidden="true"></i>
              <?= $category["name"]; ?>
            </a>
          </li>

        <?php endforeach; ?>

      </ul>
    </section>

    <!-- Récents -->
    <section class="latests-section black-card section-1200">
      <button type="button" class="btn btn--inline see-latests-btn see-latests-btn--active" data-latests="discussions">Derniers sujets</button>
      <button type="button" class="btn btn--inline see-latests-btn" data-latests="messages">Derniers messages</button>
      <div class="separator--horizontal"></div>

      <!-- Dernières discussions -->
      <article class="latests-article latests-article--discussions active">
        <h2 class="section-title">Derniers sujets</h2>

        <ul class="latests-list">

          <?php foreach ($latests["discussions"] as $discussion) : ?>

            <li class="latests-list__item index-list__item">
              <h3 class="latests-list__item-title">
                <a href="/discussion.php?id=<?= $discussion["id"]; ?>&page=1&limit=10"><i class="<?= $discussion["category_icon"]; ?>" aria-hidden="true"></i><?= $discussion["title"]; ?></a>
              </h3>
              <div class="">
                <p>par <a href="#"><?= $discussion["username"]; ?></a></p>
                <p>le <?= $discussion["creation_date"]; ?></p>
                <p><?= $discussion["nb_responses"], $discussion["nb_responses"] > 1 ? " messages" : " message"; ?></p>
              </div>
              <div class="">
                <p>Dernière réponse: <?= $discussion["latest_response"]; ?></p>
              </div>
            </li>

          <?php endforeach; ?>
        </ul>

      </article>

      <!-- Derniers messages -->
      <article class="latests-article latests-article--messages">
        <h2 class="section-title">Derniers messages</h2>

        <ul class="latests-list">

          <?php foreach ($latests["messages"] as $message) : ?>

            <li class="latests-list__item index-list__item">
              <h3 class="latests-list__item-title">
                <a href="/discussion.php?id=<?= $message["discussion_id"]; ?>&page=1&limit=10"><?= $message["discussion_title"]; ?></a>
              </h3>
              <div class="">
                <p>par <a href="#"><?= $message["username"]; ?></a></p>
                <p>le <?= $message["creation_date"]; ?></p>
              </div>
            </li>

          <?php endforeach; ?>

        </ul>

      </article>

    </section>
  </main>

  <?php require_once "./includes/footer.php"; ?>
  <script src="./public/js/app.js"></script>
  <script src="./public/js/index.js"></script>
</body>

</html>
<!-- (👉ﾟヮﾟ)👉 -->