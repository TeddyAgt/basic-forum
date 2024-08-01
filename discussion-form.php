<?php
require_once __DIR__ . "/database/db_access.php";
$userAccess = require_once __DIR__ . "/database/models/db_users.php";
$sessionAccess = require_once __DIR__ . "/database/models/db_sessions.php";
$discussionAccess = require_once __DIR__ . "/database/models/db_discussions.php";
$categoriesList = $discussionAccess->getAllCategories() ?? [];

$user = $sessionAccess->isLoggedIn();

// Messages d'erreur
const ERROR_REQUIRED = "Ce champs est requis";
const ERROR_TITLE_TOO_SHORT = "Le titre doit faire 20 caractères minimum";
const ERROR_CONTENT_TOO_SHORT = "Le message doit faire 30 caractères minimum";

$errors = [
  "category" => "",
  "title" => "",
  "content" => ""
];

// Gestion du POST du formulaire
if ($_SERVER["REQUEST_METHOD"] === "POST") {
  $category = filter_input(INPUT_POST, "category", FILTER_SANITIZE_NUMBER_INT) ?? "";
  $title = filter_input(INPUT_POST, "title", FILTER_SANITIZE_FULL_SPECIAL_CHARS) ?? "";
  $content = filter_input(INPUT_POST, "content", FILTER_SANITIZE_FULL_SPECIAL_CHARS, ["flags" => FILTER_FLAG_NO_ENCODE_QUOTES]) ?? "";

  if (!$category) {
    $errors["category"] = ERROR_REQUIRED;
  }

  if (!$title) {
    $errors["title"] = ERROR_REQUIRED;
  } elseif (mb_strlen($title) < 20) {
    $errors["title"] = ERROR_TITLE_TOO_SHORT;
  }

  if (!$content) {
    $errors["content"] = ERROR_REQUIRED;
  } elseif (mb_strlen($content) < 30) {
    $errors["content"] = ERROR_CONTENT_TOO_SHORT;
  }

  if (empty(array_filter($errors, fn ($e) => $e !== ""))) {
    $discussionAccess->createOneDiscussion([
      "authorId" => $user["id"],
      "title" => $title,
      "categoryId" => $category,
      "text" => $content
    ]);

    header("Location: /");
  }
}

?>

<!DOCTYPE html>
<html lang="en">

<head> <!-- ᓚᘏᗢ -->
  <?php require_once "./includes/head.php"; ?>
  <link rel="stylesheet" href="./public/css/forms.css">
  <title>Créer un nouveau sujet - Forum</title>
</head>

<body>
  <?php require_once "./includes/header.php"; ?>

  <main>

    <section class="black-card index-header section-1200">
      <h1 class="main-title">Écrire un nouveau sujet</h1>

      <form action="/discussion-form.php" method="POST">

        <div class="input-group">
          <label for="category">Catégorie</label>
          <select name="category" id="category">
            <?php foreach ($categoriesList as $category) : ?>
              <option value="<?= $category["id"]; ?>"><?= $category["name"]; ?></option>
            <?php endforeach; ?>
          </select>
        </div>

        <div class="input-group">
          <label for="title">Titre</label>
          <input type="text" name="title" id="title" class="input-big">
          <?php if ($errors["title"]) : ?>
            <p class="form-error"><?= $errors["title"]; ?></p>
          <?php endif; ?>
        </div>

        <div class="input-group">
          <label for="content">Message</label>
          <textarea name="content" id="content" class="content-input"></textarea>
          <?php if ($errors["content"]) : ?>
            <p class="form-error"><?= $errors["content"]; ?></p>
          <?php endif; ?>
        </div>

        <button type="submit" class="btn btn--primary">Publier</button>

      </form>
    </section>
  </main>

  <?php if (!$user) : ?>
    <div class="overlay active">
      <section class="redirect-popup black-card">
        <h2 class="popup-title">Vous devez être connecté pour commencer une discussion</h2>

        <div class="popup-control-group">
          <a href="./login.php" class="btn btn--primary">Connexion</a>
          <a href="./signup.php" class="btn btn--primary">Inscription</a>
        </div>
      </section>
    </div>
  <?php endif; ?>

  <?php require_once "./includes/footer.php"; ?>
  <script src="./public/js/app.js"></script>
</body>

</html>
<!-- (👉ﾟヮﾟ)👉 -->