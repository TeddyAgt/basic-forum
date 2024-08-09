<?php
$user = $sessionAccess->isLoggedIn();
?>

<header class="main-header">
  <a href="./index.php" title="Retour à l'accueil" class="header__logo">Forum</a>

  <div class="header-search" role="search">
    <form action="" method="GET" class="header-search__form">
      <input type="text" name="search" id="main-search-input" placeholder="Rechercher sur le forum">
      <button type="submit" title="Rechercher..." aria-label="Rechercher">
        <i class="fa-solid fa-magnifying-glass" aria-hidden="true"></i>
      </button>
    </form>
  </div>

  <nav class="main-navigation">

    <a href="/" class="main-navigation__link <?= $_SERVER["REQUEST_URI"] === "/" || $_SERVER["REQUEST_URI"] === "/index.php" ? "main-navigation__link--active" : ""; ?>" title="Accueil">Accueil</a>

    <?php if ($user) : ?>

      <a href="./profile.php" class="main-navigation__link main-navigation__link--profile <?= $_SERVER["REQUEST_URI"] === "/profile.php" ? "main-navigation__link--active" : ""; ?>" title="Profil" aria-label="Profil">
        <img src="<?= $user->avatar; ?>" alt="profile">
      </a>
      <a href="./logout.php" class="main-navigation__link <?= $_SERVER["REQUEST_URI"] === "/logout.php" ? "main-navigation__link--active" : ""; ?>" title="Déconnexion">Déconnexion</a>

    <?php else : ?>

      <a href="./signup.php" class="main-navigation__link <?= $_SERVER["REQUEST_URI"] === "/signup.php" ? "main-navigation__link--active" : ""; ?>" title="Inscription">Inscription</a>
      <a href="./login.php" class="main-navigation__link <?= $_SERVER["REQUEST_URI"] === "/login.php" ? "main-navigation__link--active" : ""; ?>" title="Connexion">Connexion</a>

    <?php endif; ?>
  </nav>
</header>