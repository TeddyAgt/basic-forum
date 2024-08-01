<?php

$discussionId = filter_input(INPUT_GET, "id", FILTER_SANITIZE_NUMBER_INT);
$replyToMessage = filter_input(INPUT_GET, "replyto", FILTER_SANITIZE_NUMBER_INT) ?? "";

if (!$discussionId) {
  header("Location: /");
}

require_once __DIR__ . "/database/db_access.php";
$userAccess = require_once __DIR__ . "/database/models/db_users.php";
$sessionAccess = require_once __DIR__ . "/database/models/db_sessions.php";
$discussionAccess = require_once __DIR__ . "/database/models/db_discussions.php";

$discussion = $discussionAccess->getDiscussionById($discussionId);
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

  if (empty(array_filter($errors, fn ($e) => $e !== ""))) {
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

      <?php if (count($discussion->messages)) : ?>
        <?php foreach ($discussion->messages as $message) : ?>
          <article class="black-card discussion-section__message" id="<?= $message->id; ?>">

            <div class="message__head">
              <div class="head__profile-picture">
                <img src="<?= $message->author["profilePicture"]; ?>" alt="">
              </div>

              <a href="#" class="head__profile-username"><?= $message->author["username"]; ?></a>

              <p class="head__message-date"><?= $message->creationDate; ?></p>
            </div>

            <div class="message__body">

              <?php if ($message->respondsTo) :
                $originalMessage = [...array_filter($discussion->messages, fn ($m) => $m->id === $message->respondsTo)][0];
              ?>

                <div class="body__responds-to-message">
                  <p class="responds-to-message__user">Réponse à
                    <a href="/discussion.php?id=5#<?= $originalMessage->id; ?>" class="body__profile-username"><?= $originalMessage->author["username"]; ?></a>:
                  </p>
                  <p class="body__message-text body__message-text--response">
                    <?= $originalMessage->text; ?>
                  </p>
                </div>

              <?php endif; ?>

              <p class="body__message-text"><?= $message->text; ?></p>

              <div class="body__like-container">
                <button class="like-btn" aria-label="Liker ce message" title="Liker ce message" data-message="<?= $message->id; ?>">
                  <i class="fa-solid fa-heart" aria-hidden="true"></i>
                </button>
                <p class="nbr-of-likes"><?= $message->likes; ?></p>
              </div>

              <a href="discussion.php?id=<?= $discussionId; ?>&replyto=<?= $message->id; ?>#send-message-form" class="body__reply-link" aria-label="Répondre à ce message" title="Répondre">
                <i class="fa-solid fa-reply" aria-hidden="true"></i>
              </a>
            </div>
          </article>
        <?php endforeach; ?>
      <?php endif; ?>

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

    </section>

  </main>

  <?php require_once "./includes/footer.php"; ?>
  <script src="./public/js/app.js"></script>
  <script src="./public/js/discussion.js"></script>
</body>

</html>
<!-- (👉ﾟヮﾟ)👉 -->