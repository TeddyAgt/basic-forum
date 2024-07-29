<?php
require_once __DIR__ . "/database/db_access.php";
$userAccess = require_once __DIR__ . "/database/models/db_users.php";

// Messages d'erreur
const ERROR_REQUIRED = "Ce champs est requis";
const ERROR_USERNAME_TOO_SHORT = "Le nom d'utilisateur doit faire 5 caractères minimum";
const ERROR_USERNAME_ALREADY_EXISTS = "Ce nom d'utilisateur n'est pas disponnible";
const ERROR_EMAIL_INVALID = "L'adresse mail n'est pas valide";
const ERROR_EMAIL_ALREADY_EXISTS = "Il y a déjà un compte avec cette adresse mail";
const ERROR_PASSWORD_TOO_SHORT = "Le mot de passe doit faire 8 caractères minimum";
const ERROR_PASSWORD_WRONG_CONFIRMATION = "Le mot de passe de confirmation ne correspond pas";

$errors = [
  'username' => '',
  'email' => '',
  'password' => '',
  'confirmation' => ''
];

// Gestion du POST du formulaire
if ($_SERVER["REQUEST_METHOD"] === "POST") {

  $email = filter_input(INPUT_POST, "email", FILTER_SANITIZE_EMAIL) ?? "";
  $username = filter_input(INPUT_POST, "username", FILTER_SANITIZE_FULL_SPECIAL_CHARS) ?? "";
  $password = $_POST["password"] ?? "";
  $confirmation = $_POST["confirmation"] ?? "";

  if (!$email) {
    $errors["email"] = ERROR_REQUIRED;
  } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors["email"] = ERROR_EMAIL_INVALID;
  } elseif ($userAccess->getUserByEmail($email)) {
    $errors["email"] = ERROR_EMAIL_ALREADY_EXISTS;
  }

  if (!$username) {
    $errors["username"] = ERROR_REQUIRED;
  } elseif (mb_strlen($username) < 5) {
    $errors["username"] = ERROR_USERNAME_TOO_SHORT;
  } elseif ($userAccess->getUserByUsername($username)) {
    $errors["username"] = ERROR_USERNAME_ALREADY_EXISTS;
  }

  if (!$password) {
    $errors["password"] = ERROR_REQUIRED;
  } elseif (mb_strlen($password) < 8) {
    $errors["password"] = ERROR_PASSWORD_TOO_SHORT;
  }

  if (!$confirmation) {
    $errors["confirmation"] = ERROR_REQUIRED;
  } elseif ($confirmation !== $password) {
    $errors["confirmation"] = ERROR_PASSWORD_WRONG_CONFIRMATION;
  }

  if (empty(array_filter($errors, fn ($e) => $e !== ""))) {
    // Création de l'utilisateur
    $userAccess->createUser([
      "email" => $email,
      "username" => $username,
      "password" => $password
    ]);
  }
}

?>

<!DOCTYPE html>
<html lang="en">

<head> <!-- ᓚᘏᗢ -->
  <?php require_once "./includes/head.php"; ?>
  <link rel="stylesheet" href="./public/css/forms.css">
  <title>Forum - Inscription</title>
</head>

<body>
  <?php require_once "./includes/header.php"; ?>

  <main>

    <section class="signup-section section-800">
      <h1>Inscription</h1>

      <form action="/signup.php" method="POST">

        <div class="input-group">
          <label for="email">Email</label>
          <input type="email" name="email" id="email">
          <?php if ($errors["email"]) : ?>
            <p class="form-error"><?= $errors["email"]; ?></p>
          <?php endif; ?>
        </div>

        <div class="input-group">
          <label for="username">Nom d'utilisateur</label>
          <input type="text" name="username" id="username">
          <?php if ($errors["username"]) : ?>
            <p class="form-error"><?= $errors["username"]; ?></p>
          <?php endif; ?>
        </div>

        <div class="input-group">
          <label for="password">Mot de passe</label>
          <div class="password-input-box">
            <input type="password" name="password" id="password">
            <button type="button" id="show-password" aria-label="Afficher le mot de passe" title="Afficher le mot de passe">
              <i class="fa-regular fa-eye" aria-hidden="true"></i>
            </button>
          </div>
          <?php if ($errors["password"]) : ?>
            <p class="form-error"><?= $errors["password"]; ?></p>
          <?php endif; ?>
        </div>

        <div class="input-group">
          <label for="confirmation">Confirmer le mot de passe</label>
          <input type="password" name="confirmation" id="confirmation">
          <?php if ($errors["confirmation"]) : ?>
            <p class="form-error"><?= $errors["confirmation"]; ?></p>
          <?php endif; ?>
        </div>

        <button type="submit" class="btn btn--primary">Valider</button>
        <p class="form-link">Déjà un compte ? <a href="./login.php">Connexion</a>.</p>
      </form>
    </section>

  </main>

  <?php require_once "./includes/footer.php"; ?>
  <script src="./public/js/app.js"></script>
  <script src="./public/js/form-utils.js"></script>
</body>

</html>
<!-- (👉ﾟヮﾟ)👉 -->