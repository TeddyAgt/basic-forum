<?php
require_once __DIR__ . "/database/db_access.php";
$userAccess = require_once __DIR__ . "/database/models/db_users.php";
$sessionAccess = require_once __DIR__ . "/database/models/db_sessions.php";

// Messages d'erreur
const ERROR_REQUIRED = "Ce champs est requis";
const ERROR_EMAIL_INVALID = "L'adresse mail n'est pas valide";
const ERROR_EMAIL_UNKNOWN = "L'adresse mail est inconnue";
const ERROR_PASSWORD_WRONG = "Le mot de passe est incorrect";

$errors = [
  "email" => "",
  "password" => ""
];

// Gestion du POST du formulaire
if ($_SERVER["REQUEST_METHOD"] === "POST") {
  $email = filter_input(INPUT_POST, "email", FILTER_SANITIZE_EMAIL) ?? "";
  $password = $_POST["password"] ?? "";

  // Vérification des champs
  if (!$email) {
    $errors["email"] = ERROR_REQUIRED;
  } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors["email"] = ERROR_EMAIL_INVALID;
  }

  if (!$password) {
    $errors["password"] = ERROR_REQUIRED;
  }

  if (empty(array_filter($errors, fn ($e) => $e !== ""))) {
    $user = $userAccess->getUserByEmail($email);

    // Vérification des credentials
    if (!$user) {
      $errors["email"] = ERROR_EMAIL_UNKNOWN;
    } elseif (!password_verify($password, $user["password"])) {
      $errors["password"] = ERROR_PASSWORD_WRONG;
    } else {
      // Création de la session
      $sessionAccess->createSession($user["id"]);
      header("Location: /");
    }
  }
}
?>

<!DOCTYPE html>
<html lang="en">

<head> <!-- ᓚᘏᗢ -->
  <?php require_once "./includes/head.php"; ?>
  <link rel="stylesheet" href="./public/css/forms.css">
  <title>Forum - Connexion</title>
</head>

<body>
  <?php require_once "./includes/header.php"; ?>

  <main class="center">

    <section class="login-section section-600 black-card">
      <h1 class="main-title">Connexion</h1>

      <form action="/login.php" method="POST">

        <div class="input-group">
          <label for="email">Email</label>
          <input type="email" name="email" id="email">
          <?php if ($errors["email"]) : ?>
            <p class="form-error"><?= $errors["email"]; ?></p>
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
          <a href="#" class="form-link">Mot de passe oublié</a>
          <?php if ($errors["password"]) : ?>
            <p class="form-error"><?= $errors["password"]; ?></p>
          <?php endif; ?>
        </div>

        <button type="submit" class="btn btn--primary">Connexion</button>
        <p class="form-link">Pas encore de compte ? <a href="./signup.php">Inscription</a>.</p>
      </form>
    </section>

  </main>

  <?php require_once "./includes/footer.php"; ?>
  <script src="./public/js/app.js"></script>
  <script src="./public/js/form-utils.js"></script>
</body>

</html>
<!-- (👉ﾟヮﾟ)👉 -->