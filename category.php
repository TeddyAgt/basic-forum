<?php

$categoryId = filter_input(INPUT_GET, "id", FILTER_SANITIZE_NUMBER_INT) ?? "";
$page = filter_input(INPUT_GET, "page", FILTER_SANITIZE_NUMBER_INT) ?? 1;
$limit = filter_input(INPUT_GET, "limit", FILTER_SANITIZE_NUMBER_INT) ?? 10;

if (!$categoryId) {
  header("Location: /");
}

require_once __DIR__ . "/database/db_access.php";
// $userAccess = require_once __DIR__ . "/database/models/db_users.php";
$sessionAccess = require_once __DIR__ . "/database/models/db_sessions.php";
$discussionAccess = require_once __DIR__ . "/database/models/db_discussions.php";

$category = $discussionAccess->getCategoryById($categoryId);
$discussions = $discussionAccess->getDiscussionsByCategory($categoryId);
// $messages = $discussionAccess->getMessagesByCategory($categoryId);
// echo "<pre>";
// var_dump($category);
// var_dump($discussions);
// var_dump($messages);
// echo "</pre>";

?>

<!DOCTYPE html>
<html lang="en">

<head> <!-- ᓚᘏᗢ -->
  <?php require_once "./includes/head.php"; ?>
  <link rel="stylesheet" href="./public/css/index.css">
  <title><?= $category->name; ?> - Forum</title>
</head>

<body>
  <?php require_once "./includes/header.php"; ?>

  <main>

    <section class="black-card section-header section-1200">
      <h1 class="main-title"><?= $category->name; ?></h1>
      <a href="./discussion-form.php" class="btn btn--primary">Commencer un nouveau sujet</a>
    </section>

    <!-- Liste des discussions -->
    <section class="category-discussions section-1200">

      <!-- Pagination haut -->
      <article class="black-card">
        <h2 class="section-title">Discussions</h2>
        <div class="pagination">
          <span>Pages: </span>
          <?php if ($category->pages === 1) : ?>
            <span class="pagination__link">1</span>
          <?php elseif ($category->pages === 2) : ?>
            <a href="/category.php?id=<?= $category->id; ?>&page=1&limit=10" class="pagination__link default-link">1</a>
            <a href="/category.php?id=<?= $category->id; ?>&page=2&limit=10" class="pagination__link default-link">2</a>
          <?php else : ?>
            <a href="/category.php?id=<?= $category->id; ?>&page=1&limit=10" class="pagination__link default-link">Première page</a>
            <a href="/category.php?id=<?= $category->id; ?>&page=<?= $page - 1; ?>&limit=10" class="pagination__link default-link">Précédente</a>
            <span class="pagination__link pagination__link--active"><?= $page; ?></span>
            <a href="/category.php?id=<?= $category->id; ?>&page=<?= $page + 1 ?>&limit=10" class="pagination__link default-link">Suivante</a>
            <a href="/category.php?id=<?= $category->id; ?>&page=2&limit=10" class="pagination__link default-link">Dernière page</a>
          <?php endif; ?>
        </div>
        <ul class="latests-list">
          <?php if (count($category->discussions)) : ?>
            <?php foreach ($category->discussions as $discussion) : ?>
              <li class="latests-list__item">
                <h3 class="latests-list__teim-title">
                  <a href="/discussion.php?id=<?= $discussion["id"]; ?>&page=1&limit=10">
                    <i class="<?= $discussion["category_icon"]; ?>" aria-hidden="true"></i><?= $discussion["title"]; ?>
                  </a>
                </h3>
                <div>
                  <p>par <a href="#"><?= $discussion["username"]; ?></a></p>
                  <p>le <?= $discussion["creation_date"]; ?></p>
                  <p><?= $discussion["nb_responses"], $discussion["nb_responses"] > 1 ? " messages" : " message"; ?></p>
                </div>
                <div>
                  <p>Denière réponse: <?= $discussion["latest_response"]; ?></p>
                </div>
              </li>
            <?php endforeach; ?>
          <?php else : ?>
            <li>Il n'y a pas encore de discussions dans cette catégorie</li>
          <?php endif; ?>
        </ul>
        <!-- Pagination bas -->
        <div class="pagination">
          <span>Pages: </span>
          <?php if ($category->pages === 1) : ?>
            <span class="pagination__link">1</span>
          <?php elseif ($category->pages === 2) : ?>
            <a href="/category.php?id=<?= $category->id; ?>&page=1&limit=10" class="pagination__link default-link">1</a>
            <a href="/category.php?id=<?= $category->id; ?>&page=2&limit=10" class="pagination__link default-link">2</a>
          <?php else : ?>
            <a href="/category.php?id=<?= $category->id; ?>&page=1&limit=10" class="pagination__link default-link">Première page</a>
            <a href="/category.php?id=<?= $category->id; ?>&page=<?= $page - 1; ?>&limit=10" class="pagination__link default-link">Précédente</a>
            <span class="pagination__link pagination__link--active"><?= $page; ?></span>
            <a href="/category.php?id=<?= $category->id; ?>&page=<?= $page + 1 ?>&limit=10" class="pagination__link default-link">Suivante</a>
            <a href="/category.php?id=<?= $category->id; ?>&page=2&limit=10" class="pagination__link default-link">Dernière page</a>
          <?php endif; ?>
        </div>
      </article>

    </section>

  </main>

  <?php require_once "./includes/footer.php"; ?>
  <script src="./public/js/app.js"></script>
  <script src="./public/js/category.js"></script>
</body>

</html>
<!-- (👉ﾟヮﾟ)👉 -->