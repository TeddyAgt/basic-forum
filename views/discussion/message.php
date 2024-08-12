<?php

/**
 * @var Message $message 
 * @var Discussion $discussion 
 * */
?>

<article class="black-card discussion-section__message" id="<?= $message->id; ?>">

  <div class="message__head">
    <div class="head__profile-picture">
      <img src="<?= $message->author["avatar"]; ?>" alt="">
    </div>
    <a href="./profile.php?id=<?= $message->author["id"]; ?>" class="head__profile-username default-link" style="color: <?= $message->author["mentions_color"]; ?>;"><?= $message->author["username"]; ?></a>
    <?= $message->author["role"] === "moderator" ? "<p>(modérateur)</p>" : ""; ?>
    <p class="head__message-date"><?= $message->creationDate; ?></p>
  </div>

  <div class="message__body">

    <?php if ($message->respondsTo) :
      $originalMessage = [...array_filter($discussion->messages, fn($m) => $m->id === $message->respondsTo)][0];
    ?>

      <div class="body__responds-to-message">
        <p class="responds-to-message__user">Réponse à
          <a href="/discussion.php?id=<?= $discussion->id; ?>#<?= $originalMessage->id; ?>" class="body__profile-username default-link" style="color: <?= $originalMessage->author["mentions_color"]; ?>;"><?= $originalMessage->author["username"]; ?></a>:
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
      <?php if ($message->status && $user && ($user->role === "administrator" || $user->role === "moderator" || $user->id === $message->author["id"])) : ?>
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