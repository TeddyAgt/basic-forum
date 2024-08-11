<?php
require_once __DIR__ . "/database/db_access.php";
$userAccess = require_once __DIR__ . "/database/models/db_users.php";
$sessionAccess = require_once __DIR__ . "/database/models/db_sessions.php";
require_once __DIR__ . "/actions/utilities.php";

$user = $sessionAccess->isLoggedIn();

if (!$user) {
  header("Location: /login.php");
} else {
  $requestUser = (int) filter_input(INPUT_GET, "id", FILTER_SANITIZE_NUMBER_INT) ?? "";
  if (!$requestUser || $requestUser !== $user->id) {
    header("Location: /profile.php");
  }
}

// Messages d'erreur
const ERROR_REQUIRED = "Ce champs est requis";
const ERROR_UNKNOWN = "Une erreur est survenue, veuillez réessayer";
const ERROR_USERNAME_TOO_SHORT = "Le nom d'utilisateur doit faire 5 caractères minimum";
const ERROR_USERNAME_ALREADY_EXISTS = "Ce nom d'utilisateur n'est pas disponnible";
const ERROR_AVATAR_TYPE = "Les formats autorisés pour l'avatar sont: png, jpg, jpeg.";
const ERROR_ABOUT_TOO_LONG = "La description doit faire 250 caractères maximum";
const ERROR_EMAIL_INVALID = "L'adresse mail n'est pas valide";
const ERROR_EMAIL_ALREADY_EXISTS = "Il y a déjà un compte avec cette adresse mail";
const ERROR_PASSWORD_TOO_SHORT = "Le mot de passe doit faire 8 caractères minimum";
const ERROR_PASSWORD_WRONG_CONFIRMATION = "Le mot de passe de confirmation ne correspond pas";
const ERROR_PASSWORD_WRONG = "Le mot de passe est incorrect";
const ERROR_CONFIRM_DELETE_ACCOUNT = "Vous devez cocher la case pour confirmer la suppression de votre compte";

$errors = [
  'username' => "",
  'email' => "",
  'newPassword' => "",
  'confirmation' => "",
  "currentPassword" => "",
  "about" => "",
  "avatar" => "",
  "banner-color" => "",
  "mentions-color" => "",
  "delete-account-password" => "",
  "confirm-delete-account" => ""
];

if ($_SERVER["REQUEST_METHOD"] === "POST") {
  $method = (int) filter_input(INPUT_GET, "method", FILTER_SANITIZE_NUMBER_INT) ?? "";
  if (!$method) {
    header("Location: /profile.php");
  }

  switch ($method) {
      // Modification du nom d'utilisateur
    case 1:
      $username = filter_input(INPUT_POST, "username", FILTER_SANITIZE_FULL_SPECIAL_CHARS) ?? "";

      if (!$username) {
        $errors["username"] = ERROR_REQUIRED;
      } else if (mb_strlen($username) < 5) {
        $errors["username"] = ERROR_USERNAME_TOO_SHORT;
      } else if ($userAccess->usernameExists($username)) {
        $errors["username"] = ERROR_USERNAME_ALREADY_EXISTS;
      } else {
        $userAccess->updateUsername($user->id, $username);
      }

      break;
      // Modification de l'avatar
    case 2:
      $avatar = $_FILES["avatar"] ?? "";

      if (!$avatar) {
        $errors["avatar"] = ERROR_REQUIRED;
      } elseif (!verifyAvatarExtension($avatar["name"])) {
        $errors["avatar"] = ERROR_AVATAR_TYPE;
      } else {
        // replace in folders
        if (is_uploaded_file($avatar["tmp_name"])) {
          $avatar["name"] = renameAvatar($user, $avatar);
          move_uploaded_file($avatar["tmp_name"], __DIR__ . $avatar["name"]);
          $userAccess->updateAvatar($user->id, $avatar["name"]);
        } else {
          $errors["avatar"] = ERROR_UNKNOWN;
        }
      }
      break;
      // Modification de la couleur de bannière
    case 3:
      $color = filter_input(INPUT_POST, "banner-color", FILTER_SANITIZE_SPECIAL_CHARS) ?? "";

      if (!$color) {
        $errors["banner-color"] = ERROR_REQUIRED;
      } else {
        $userAccess->updateBannerColor($user->id, $color);
      }
      break;
      // Modification de la couleur des mentions
    case 4:
      $color = filter_input(INPUT_POST, "mentions-color", FILTER_SANITIZE_SPECIAL_CHARS) ?? "";

      if (!$color) {
        $errors["mentions-color"] = ERROR_REQUIRED;
      } else {
        $userAccess->updateMentionsColor($user->id, $color);
      }
      break;
      // Modification de la description
    case 5:
      $about = filter_input(INPUT_POST, "about", FILTER_SANITIZE_FULL_SPECIAL_CHARS) ?? "";

      if (mb_strlen($about) > 250) {
        $errors["about"] = ERROR_ABOUT_TOO_LONG;
      } else {
        $userAccess->updateAbout($user->id, $about);
      }
      break;
      // Modification de l'adresse mail
    case 6:
      $email = filter_input(INPUT_POST, "email", FILTER_SANITIZE_EMAIL) ?? "";

      if (!$email) {
        $errors["email"] = ERROR_REQUIRED;
      } else if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors["email"] = ERROR_EMAIL_INVALID;
      } else if ($userAccess->emailExists($email)) {
        $errors["email"] = ERROR_EMAIL_ALREADY_EXISTS;
      } else {
        $userAccess->updateEmail($user->id, $email);
      }
      break;
      // Modification du mot de passe
    case 7:
      $newPassword = $_POST["password"] ?? "";
      $confirmation = $_POST["confirmation"] ?? "";
      $currentPassword = $_POST["current-password"] ?? "";

      if (!$newPassword) {
        $errors["newPassword"] = ERROR_REQUIRED;
      } elseif (mb_strlen($newPassword) < 8) {
        $errors["newPassword"] = ERROR_PASSWORD_TOO_SHORT;
      }

      if (!$confirmation) {
        $errors["confirmation"] = ERROR_REQUIRED;
      } elseif ($confirmation !== $newPassword) {
        $errors["confirmation"] = ERROR_PASSWORD_WRONG_CONFIRMATION;
      }

      if (!$currentPassword) {
        $errors["currentPassword"] = ERROR_REQUIRED;
      } elseif (!password_verify($currentPassword, $user->get_password())) {
        $errors["currentPassword"] = ERROR_PASSWORD_WRONG;
      }

      if (empty(array_filter($errors, fn($e) => $e !== ""))) {
        $userAccess->updatePassword($user->id, $newPassword);
      }
      // Suppression de compte
    case 8:
      $password = $_POST["delete-account-password"] ?? "";
      $confirmation = $_POST["confirm-delete-account"] ?? "";

      if (!$password) {
        $errors["delete-account-password"] = ERROR_REQUIRED;
      } elseif (!password_verify($password, $user->get_password())) {
        $errors["delete-account-password"] = ERROR_PASSWORD_WRONG;
      }

      if (!$confirmation) {
        $errors["confirm-delete-account"] = ERROR_CONFIRM_DELETE_ACCOUNT;
      }
      var_dump($errors);

      if (empty(array_filter($errors, fn($e) => $e !== ""))) {
        $userAccess->deleteAccount($user->id);
        header("Location: /");
      }

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
  <link rel="stylesheet" href="./public/css/user-settings.css">
  <title>Forum - Profil</title>
</head>

<body>
  <?php require_once "./includes/header.php"; ?>

  <main class="center">

    <section class="account-settings-section black-card section-1200">

      <!-- navigation des paramètres -->
      <aside class="account-settings__aside">
        <h2 class="section-title">Paramètres utilisateur</h2>
        <nav class="account-settings__navigation">
          <button class="aside-navigation-link aside-navigation-link--active" type="button" aria-controls="profile-settings" aria-expanded="true">Profil</button>
          <button class="aside-navigation-link" type="button" aria-controls="account-settings" aria-expanded="false">Compte</button>
        </nav>

        <h2 class="section-title">Paramètres du forum</h2>
        <nav class="account-settings__navigation">
          <button class="aside-navigation-link" type="button" aria-controls="account-PLACEHOLDER" aria-expanded="false">Apparence</button>
        </nav>

        <h2 class="section-title">Zone rouge</h2>
        <nav class="account-settings__navigation">
          <button class="aside-navigation-link" type="button" aria-controls="account-PLACEHOLDER" aria-expanded="false">Supprimer mon compte</button>
        </nav>

      </aside>

      <div class="account-settings-section__container">

        <!-- <h1 class="main-title">Modifier mes données</h1> -->
        <!-- Paramètres du profil -->
        <article class="account-settings-article account-settings-article--profile account-settings-article--active" id="profile-settings" aria-hidden="false">

          <h2 class="section-title">Profil</h2>

          <!-- Modifier username -->
          <form action="./user-settings.php?id=<?= $user->id; ?>&method=1" method="POST" class="account-settings-form" id="change-username-form">
            <div class="input-group">
              <label for="username">Nom d'utilisateur</label>
              <div class="input-flex-container">
                <input type="text" name="username" id="username" value="<?= $user->username; ?>">
                <button type="submit" aria-label="Sauvegarder les modifications" title="Sauvegarder les modifications" class="btn btn--primary">
                  <i class="fa-regular fa-floppy-disk" aria-hidden="true"></i>
                </button>
              </div>
            </div>
            <?php if ($errors["username"]) : ?>
              <p class="form-error"><?= $errors["username"]; ?></p>
            <?php endif; ?>
          </form>

          <!-- Modifier avatar -->
          <form action="./user-settings.php?id=<?= $user->id; ?>&method=2" method="POST" enctype="multipart/form-data" class="account-settings-form" id="change-username-form">
            <div class="input-group">
              <label for="avatar">Modifier l'avatar</label>
              <div class="input-flex-container input-flex-container--up">
                <input type="file" name="avatar" id="avatar">
                <button type="submit" aria-label="Sauvegarder les modifications" title="Sauvegarder les modifications" class="btn btn--primary">
                  <i class="fa-regular fa-floppy-disk" aria-hidden="true"></i>
                </button>
              </div>
            </div>
            <?php if ($errors["avatar"]) : ?>
              <p class="form-error"><?= $errors["avatar"]; ?></p>
            <?php endif; ?>
          </form>

          <!-- Modifier la couleur de la bannière -->
          <form action="./user-settings.php?id=<?= $user->id; ?>&method=3" method="POST" enctype="multipart/form-data" class="account-settings-form" id="change-username-form">
            <div class="input-group">
              <label for="banner-color">Couleur de la bannière</label>
              <div class="input-flex-container">
                <input type="color" name="banner-color" id="banner-color" value="<?= $user->settings["banner_color"]; ?>">
                <button type="submit" aria-label="Sauvegarder les modifications" title="Sauvegarder les modifications" class="btn btn--primary">
                  <i class="fa-regular fa-floppy-disk" aria-hidden="true"></i>
                </button>
              </div>
            </div>
            <?php if ($errors["banner-color"]) : ?>
              <p class="form-error"><?= $errors["banner-color"]; ?></p>
            <?php endif; ?>
          </form>

          <!-- Modifier la couleur des mentions -->
          <form action="./user-settings.php?id=<?= $user->id; ?>&method=4" method="POST" enctype="multipart/form-data" class="account-settings-form" id="change-username-form">
            <div class="input-group">
              <label for="mentions-color">Couleur de ton nom dans les mentions</label>
              <div class="input-flex-container">
                <input type="color" name="mentions-color" id="mentions-color" value="<?= $user->settings["mentions_color"]; ?>">
                <button type="submit" aria-label="Sauvegarder les modifications" title="Sauvegarder les modifications" class="btn btn--primary">
                  <i class="fa-regular fa-floppy-disk" aria-hidden="true"></i>
                </button>
              </div>
            </div>
            <?php if ($errors["mentions-color"]) : ?>
              <p class="form-error"><?= $errors["mentions-color"]; ?></p>
            <?php endif; ?>
          </form>

          <!-- Modifier à propos -->
          <form action="./user-settings.php?id=<?= $user->id; ?>&method=5" method="POST" enctype="multipart/form-data" class="account-settings-form" id="change-username-form">
            <div class="input-group">
              <label for="about">À propos de moi</label>
              <div class="textarea-container">
                <textarea name="about" id="about" maxlength="250"><?= $user->about; ?></textarea>
                <div class="textarea-counter"></div>
              </div>
            </div>
            <?php if ($errors["about"]) : ?>
              <p class="form-error"><?= $errors["about"]; ?></p>
            <?php endif; ?>
            <button type="submit" aria-label="Sauvegarder les modifications" title="Sauvegarder les modifications" class="btn btn--primary">
              <i class="fa-regular fa-floppy-disk" aria-hidden="true"></i>
            </button>
          </form>

        </article>

        <!-- Paramètres du compte -->
        <article class="account-settings-article account-settings-article--account" id="profile-settings" aria-hidden="true">

          <h2 class="section-title">Compte</h2>

          <!-- Modifier email -->
          <form action="./user-settings.php?id=<?= $user->id; ?>&method=6" method="POST" class="account-settings-form" id="change-email-form">
            <div class="input-group">
              <label for="email">Email</label>
              <div class="input-flex-container">
                <input type="email" name="email" id="email" value="<?= $user->email; ?>">
                <button type="submit" aria-label="Sauvegarder les modifications" title="Sauvegarder les modifications" class="btn btn--primary">
                  <i class="fa-regular fa-floppy-disk" aria-hidden="true"></i>
                </button>
              </div>
            </div>
            <?php if ($errors["email"]) : ?>
              <p class="form-error"><?= $errors["email"]; ?></p>
            <?php endif; ?>
          </form>

          <!-- Modifier password -->
          <form action="./user-settings.php?id=<?= $user->id; ?>&method=7" method="POST" class="account-settings-form" id="change-password-form">
            <div class="input-group">
              <label for="password">Mot de passe</label>
              <div class="password-input-box">
                <input type="password" name="password" id="password">
                <button type="button" id="show-password" aria-label="Afficher le mot de passe" title="Afficher le mot de passe">
                  <i class="fa-regular fa-eye" aria-hidden="true"></i>
                </button>
              </div>
              <?php if ($errors["newPassword"]) : ?>
                <p class="form-error"><?= $errors["newPassword"]; ?></p>
              <?php endif; ?>
            </div>
            <div class="input-group">
              <label for="confirmation">Confirmer le mot de passe</label>
              <input type="password" name="confirmation" id="confirmation">
            </div>
            <?php if ($errors["confirmation"]) : ?>
              <p class="form-error"><?= $errors["confirmation"]; ?></p>
            <?php endif; ?>
            <div class="input-group">
              <label for="current-password">Mot de passe actuel</label>
              <div class="input-flex-container">
                <input type="password" name="current-password" id="current-password">
                <button type="submit" aria-label="Sauvegarder les modifications" title="Sauvegarder les modifications" class="btn btn--primary">
                  <i class="fa-regular fa-floppy-disk" aria-hidden="true"></i>
                </button>
              </div>
            </div>
            <?php if ($errors["currentPassword"]) : ?>
              <p class="form-error"><?= $errors["currentPassword"]; ?></p>
            <?php endif; ?>
          </form>

        </article>

        <!-- Apparence du forum -->
        <article class="account-settings-article account-settings-article--appearance">
          <h2 class="section-title">Apparence</h2>
        </article>

        <!-- Suppression de compte -->
        <article class="account-settings-article account-settings-article--delete-account">
          <h2 class="section-title">Supprimer mon compte</h2>

          <!-- On va gérer la soumission de ce form en JS -->
          <form action="./user-settings.php?id=<?= $user->id; ?>&method=8" method="POST" class="account-settings-form" id="change-password-form">

            <button type="button" class="btn btn--danger" id="delete-account-btn">Supprimer mon compte</button>

            <div class="delete-account-container">

              <div class="input-group">
                <label for="delete-account-password">Entrez votre mot de passe pour confirmer la suppression de votre compte</label>
                <div class="password-input-box">
                  <input type="password" name="delete-account-password" id="delete-account-password">
                  <button type="button" id="show-password" aria-label="Afficher le mot de passe" title="Afficher le mot de passe">
                    <i class="fa-regular fa-eye" aria-hidden="true"></i>
                  </button>
                </div>
                <?php if ($errors["delete-account-password"]) : ?>
                  <p class="form-error"><?= $errors["delete-account-password"]; ?></p>
                <?php endif; ?>
              </div>

              <label for="confirm-delete-account" class="checkbox-label">
                <input type="checkbox" name="confirm-delete-account" id="confirm-delete-account"><span>J'ai compris que la suppression de mon compte est définitive.</span>
              </label>
              <?php if ($errors["confirm-delete-account"]) : ?>
                <p class="form-error"><?= $errors["confirm-delete-account"]; ?></p>
              <?php endif; ?>

              <button type="submit" aria-label="Sauvegarder les modifications" title="Sauvegarder les modifications" class="btn btn--primary">
                <i class="fa-regular fa-floppy-disk" aria-hidden="true"></i>
              </button>

            </div>

          </form>

        </article>

      </div>

    </section>

  </main>

  <?php require_once "./includes/footer.php"; ?>
  <script src="./public/js/app.js"></script>
  <script src="./public/js/form-utils.js"></script>
  <script src="./public/js/user-settings.js"></script>
</body>

</html>
<!-- (👉ﾟヮﾟ)👉 -->