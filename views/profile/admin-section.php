<?php

/**
 * @var User $user
 */
?>

<section class="admin-section black-card section-1200">
  <h2 class="main-title">Administration</h2>

  <?php if ($user->role === "administrator"): ?>
    <article class="add-category__article" id="add-category">
      <h2 class="section-title">Ajouter une nouvelle catégorie</h2>

      <form action="./actions/add-category.php" method="POST" class="add-category__form">
        <div class="input-group">
          <label for="name">Nom</label>
          <input type="text" name="name" id="name">
          <p class="form-error" id="form-error-category-name"></p>
        </div>
        <div class="input-group">
          <label for="icon">Icône Fontawsome</label>
          <input type="text" name="icon" id="icon">
          <p class="form-error" id="form-error-category-icon"></p>
        </div>
        <button type="submit" class="btn btn--primary">Valider</button>
      </form>
    </article>
  <?php endif; ?>

  <div class="toast-container"></div>
</section>