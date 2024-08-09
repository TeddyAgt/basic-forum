<?php

$discussionId = filter_input(INPUT_GET, "id", FILTER_SANITIZE_NUMBER_INT);
$page = filter_input(INPUT_GET, "page", FILTER_SANITIZE_NUMBER_INT) ?? 1;
$limit = filter_input(INPUT_GET, "limit", FILTER_SANITIZE_NUMBER_INT) ?? 10;
$replyToMessage = filter_input(INPUT_GET, "replyto", FILTER_SANITIZE_NUMBER_INT) ?? "";

if (!$discussionId) {
  header("Location: /");
}

require_once __DIR__ . "/database/db_access.php";
$userAccess = require_once __DIR__ . "/database/models/db_users.php";
$sessionAccess = require_once __DIR__ . "/database/models/db_sessions.php";
$discussionAccess = require_once __DIR__ . "/database/models/db_discussions.php";

$discussion = $discussionAccess->getDiscussionPageById($discussionId, $page, $limit);
$user = $sessionAccess->isLoggedIn();
// echo "<pre>";
// var_dump($discussion);
// echo "</pre>";

// Messages d'erreur
const ERROR_REQUIRED = "Ce champs est requis";
const ERROR_CONTENT_TOO_SHORT = "Le message doit faire 30 caractères minimum";

$errors = [
  "content" => ""
];

// Gestion du POST du formulaire
if ($_SERVER["REQUEST_METHOD"] === "POST") {
  var_dump($_POST);
  $content = filter_input(INPUT_POST, "content", FILTER_SANITIZE_FULL_SPECIAL_CHARS, ["flags" => FILTER_FLAG_NO_ENCODE_QUOTES]) ?? "";
  $respondsTo = (int) filter_input(INPUT_POST, "responds-to", FILTER_SANITIZE_NUMBER_INT) ?? 0;
  var_dump($respondsTo);
  if (!$content) {
    $errors["content"] = ERROR_REQUIRED;
  } elseif (mb_strlen($content) < 30) {
    // $errors["content"] = ERROR_CONTENT_TOO_SHORT;
  }

  if (empty(array_filter($errors, fn($e) => $e !== ""))) {
    $discussionAccess->createOneMessage([
      "authorId" => $user["id"],
      "discussionId" => $discussionId,
      "text" => $content,
      "respondsTo" => $respondsTo
    ]);
  }
  header("Location: /discussion.php?id=$discussionId");
}

?>

<!DOCTYPE html>
<html lang="en">

<head> <!-- ᓚᘏᗢ -->
  <?php require_once "./includes/head.php"; ?>
  <link rel="stylesheet" href="./public/css/forms.css">
  <link rel="stylesheet" href="./public/css/discussion.css">
  <title>Forum - Accueil</title>
</head>

<body>
  <?php require_once "./includes/header.php"; ?>

  <main>

    <h1 class="main-title"><?= $discussion->title; ?></h1>

    <section class="discussion-section section-1200">

      <!-- Pagination haut -->
      <div class="pagination">
        <span>Pages: </span>
        <?php if ($discussion->pages === 1) : ?>
          <span class="pagination__link">1</span>
        <?php elseif ($discussion->pages === 2) : ?>
          <a href="/discussion.php?id=<?= $discussion->id; ?>&page=1&limit=10" class="pagination__link default-link">1</a>
          <a href="/discussion.php?id=<?= $discussion->id; ?>&page=2&limit=10" class="pagination__link default-link">2</a>
        <?php else : ?>
          <a href="/discussion.php?id=<?= $discussion->id; ?>&page=1&limit=10" class="pagination__link default-link">Première page</a>
          <a href="/discussion.php?id=<?= $discussion->id; ?>&page=<?= $page - 1; ?>&limit=10" class="pagination__link default-link">Précédente</a>
          <span class="pagination__link pagination__link--active"><?= $page; ?></span>
          <a href="/discussion.php?id=<?= $discussion->id; ?>&page=<?= $page + 1 ?>&limit=10" class="pagination__link default-link">Suivante</a>
          <a href="/discussion.php?id=<?= $discussion->id; ?>&page=2&limit=10" class="pagination__link default-link">Dernière page</a>
        <?php endif; ?>

      </div>

      <!-- Liste des messages -->
      <?php if (count($discussion->messages)) : ?>
        <?php foreach ($discussion->messages as $message) : ?>
          <article class="black-card discussion-section__message" id="<?= $message->id; ?>">

            <div class="message__head">
              <div class="head__profile-picture">
                <img src="<?= $message->author["avatar"]; ?>" alt="">
              </div>

              <a href="#" class="head__profile-username default-link <?= $message->author["role"] === "moderator" ? "moderator" : ""; ?>"><?= $message->author["username"]; ?></a>
              <?= $message->author["role"] === "moderator" ? "<p>(modérateur)</p>" : ""; ?>
              <p class="head__message-date"><?= $message->creationDate; ?></p>
            </div>

            <div class="message__body">

              <?php if ($message->respondsTo) :
                $originalMessage = [...array_filter($discussion->messages, fn($m) => $m->id === $message->respondsTo)][0];
              ?>

                <div class="body__responds-to-message">
                  <p class="responds-to-message__user">Réponse à
                    <a href="/discussion.php?id=<?= $discussion->id; ?>#<?= $originalMessage->id; ?>" class="body__profile-username default-link"><?= $originalMessage->author["username"]; ?></a>:
                  </p>
                  <p class="body__message-text body__message-text--response <?= !$message->status ? "body-message-text--deleted" : ""; ?>">
                    <?= $originalMessage->text; ?>
                  </p>
                </div>

              <?php endif; ?>

              <p class="body__message-text <?= !$message->status ? "body-message-text--deleted" : ""; ?>"><?= $message->text; ?></p>

              <div class="body__action-group">
                <a href="discussion.php?id=<?= $discussionId; ?>&replyto=<?= $message->id; ?>#send-message-form" class="body__reply-link" aria-label="Répondre à ce message" title="Répondre">
                  <i class="fa-solid fa-reply" aria-hidden="true"></i>
                </a>
                <?php if ($message->status && ($user->role === "administrator" || $user->role === "moderator" || $user->id === $message->author["id"])) : ?>
                  <button class="body__delete-btn" aria-label="Supprimer ce message" title="Supprimer" data-message="<?= $message->id; ?>">
                    <i class="fa-solid fa-trash-can" aria-hidden="true"></i>
                  </button>
                <?php endif; ?>
              </div>

              <div class="body__like-container">
                <button class="like-btn" aria-label="Liker ce message" title="Liker ce message" data-message="<?= $message->id; ?>">
                  <i class="fa-solid fa-heart" aria-hidden="true"></i>
                </button>
                <p class="nbr-of-likes"><?= $message->likes; ?></p>
              </div>

            </div>
          </article>
        <?php endforeach; ?>
      <?php endif; ?>

      <!-- Pagination bas -->
      <div class="pagination">
        <span>Pages: </span>
        <?php if ($discussion->pages === 1) : ?>
          <span class="pagination__link">1</span>
        <?php elseif ($discussion->pages === 2) : ?>
          <a href="/discussion.php?id=<?= $discussion->id; ?>&page=1&limit=10" class="pagination__link default-link">1</a>
          <a href="/discussion.php?id=<?= $discussion->id; ?>&page=2&limit=10" class="pagination__link default-link">2</a>
        <?php else : ?>
          <a href="/discussion.php?id=<?= $discussion->id; ?>&page=1&limit=10" class="pagination__link default-link">Première page</a>
          <a href="/discussion.php?id=<?= $discussion->id; ?>&page=<?= $page - 1; ?>&limit=10" class="pagination__link default-link">Précédente</a>
          <span class="pagination__link pagination__link--active"><?= $page; ?></span>
          <a href="/discussion.php?id=<?= $discussion->id; ?>&page=<?= $page + 1 ?>&limit=10" class="pagination__link default-link">Suivante</a>
          <a href="/discussion.php?id=<?= $discussion->id; ?>&page=2&limit=10" class="pagination__link default-link">Dernière page</a>
        <?php endif; ?>

      </div>

      <article class="discussion-section__send-response-article black-card">
        <h2 class="section-title">Envoyer une réponse</h2>

        <?php if ($user) : ?>
          <form action="discussion.php?id=<?= $discussionId; ?>" method="POST" id="send-message-form">
            <div class="input-group">
              <label for="content">Message</label>
              <textarea name="content" id="content" class="content-input"><?= $content ?? ""; ?></textarea>
              <?php if ($errors["content"]) : ?>
                <p class="form-error"><?= $errors["content"]; ?></p>
              <?php endif; ?>
            </div>
            <input type="number" id="responds-to" name="responds-to" hidden value="<?= $replyToMessage; ?>">

            <button type="submit" class="btn btn--primary">Publier</button>

          </form>
        <?php else : ?>
          <p>Vous devez être connecté pour poster une réponse</p>
          <div class="popup-control-group">
            <a href="./login.php" class="btn btn--primary">Connexion</a>
            <a href="./signup.php" class="btn btn--primary">Inscription</a>
          </div>
        <?php endif; ?>
      </article>

      <!-- Popup de confirmation de suppression de message -->
      <div class="overlay">
        <form class="delete-message-confirmation-popup black-card" action="" method="POST">
          <h2 class="popup-title">Souhaitez-vous vraiment supprimer ce message ?</h2>

          <div class="popup-control-group">
            <button type="button" class="btn btn--primary" id="cancel-delete-message-btn">Annuler</button>
            <button type="submit" class="btn btn--warning">Supprimer</button>
          </div>
          <div class="input-group">
            <label for="password">Mot de passe:</label>
            <!-- <input type="password" name="password" id="password"> -->
            <!-- <p class="form-error" id="delete-message-popup-error"></p> -->
            <input type="number" id="message-id" name="message-id" hidden value="">
            <input type="text" id="url" name="url" hidden value="">
          </div>
        </form>
      </div>
    </section>

  </main>

  <?php require_once "./includes/footer.php"; ?>
  <script src="./public/js/app.js"></script>
  <script src="./public/js/discussion.js"></script>
</body>

</html>
<!-- (👉ﾟヮﾟ)👉 -->