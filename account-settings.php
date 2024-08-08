<?php
require_once __DIR__ . "/database/db_access.php";
$userAccess = require_once __DIR__ . "/database/models/db_users.php";
$sessionAccess = require_once __DIR__ . "/database/models/db_sessions.php";

$user = $sessionAccess->isLoggedIn();

if (!$user) {
  header("Location: /login.php");
} else {
  $requestUser = (int) filter_input(INPUT_GET, "id", FILTER_SANITIZE_NUMBER_INT) ?? "";
  if (!$requestUser || $requestUser !== $user->id) {
    header("Location: /profile.php");
  }
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
  $method = (int) filter_input(INPUT_GET, "method", FILTER_SANITIZE_NUMBER_INT) ?? "";
  if (!$method) {
    header("Location: /profile.php");
  }

  switch ($method) {
      // Modification du nom d'utilisateur
    case 1:
      $username = filter_input(INPUT_POST, "username", FILTER_SANITIZE_FULL_SPECIAL_CHARS) ?? $user->username;
      $userAccess->updateUsername($user->id, $username);
      break;
      // Modification de l'adresse mail
    case 2:
      # code...
      break;
      // Modification du mot de passe
    case 3:
      # code...
      break;

    default:
      # code...
      break;
  }
}

?>

<!DOCTYPE html>
<html lang="en">

<head> <!-- ᓚᘏᗢ -->
  <?php require_once "./includes/head.php"; ?>
  <link rel="stylesheet" href="./public/css/forms.css">
  <title>Forum - Profil</title>
</head>

<body>
  <?php require_once "./includes/header.php"; ?>

  <main class="center">

    <section class="account-settings-section black-card section-1200">
      <h1 class="main-title">Modifier mes données</h1>

      <form action="./account-settings.php?id=<?= $user->id; ?>&method=1" method="POST" class="account-settings-form" id="change-username-form">
        <div class="input-group">
          <label for="username">Nom d'utilisateur</label>
          <input type="text" name="username" id="username" value="<?= $user->username; ?>">
        </div>
        <button type="submit" aria-label="Sauvegarder les modifications" title="Sauvegarder les modifications" class="btn btn--primary">
          <i class="fa-regular fa-floppy-disk" aria-hidden="true"></i>
        </button>
      </form>

      <form action="./account-settings.php?id=<?= $user->id; ?>&method=2" method="POST" class="account-settings-form" id="change-email-form">
        <div class="input-group">
          <label for="email">Email</label>
          <input type="email" name="email" id="email" value="<?= $user->email; ?>">
        </div>
        <button type="submit" aria-label="Sauvegarder les modifications" title="Sauvegarder les modifications" class="btn btn--primary">
          <i class="fa-regular fa-floppy-disk" aria-hidden="true"></i>
        </button>
      </form>

      <form action="./account-settings.php?id=<?= $user->id; ?>&method=3" method="POST" class="account-settings-form" id="change-password-form">
        <div class="input-group">
          <label for="password">Mot de passe</label>
          <div class="password-input-box">
            <input type="password" name="password" id="password">
            <button type="button" id="show-password" aria-label="Afficher le mot de passe" title="Afficher le mot de passe">
              <i class="fa-regular fa-eye" aria-hidden="true"></i>
            </button>
          </div>
        </div>
        <div class="input-group">
          <label for="confirmation">Confirmer le mot de passe</label>
          <input type="password" name="confirmation" id="confirmation">
        </div>
        <div class="input-group">
          <label for="current-password">Mot de passe actuel</label>
          <input type="password" name="current-password" id="current-password">
        </div>

        <button type="submit" aria-label="Sauvegarder les modifications" title="Sauvegarder les modifications" class="btn btn--primary">
          <i class="fa-regular fa-floppy-disk" aria-hidden="true"></i>
        </button>
      </form>



    </section>

  </main>

  <?php require_once "./includes/footer.php"; ?>
  <script src="./public/js/app.js"></script>
  <script src="./public/js/form-utils.js"></script>
</body>

</html>
<!-- (👉ﾟヮﾟ)👉 -->