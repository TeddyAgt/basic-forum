<?php

require_once __DIR__ . "/../../Classes/User.classe.php";

class UserAcces
{
  private PDOStatement $statementCreateOne;
  private PDOStatement $statementCreateUserSettings;
  private PDOStatement $statementReadOneByEmail;
  private PDOStatement $statementReadOneByUsername;
  private PDOStatement $statementReadOneProfile;
  private PDOStatement $statementUpdateUsername;
  private PDOStatement $statementUpdateEmail;
  private PDOStatement $statementUpdateAvatar;
  private PDOStatement $statementUpdateBannerColor;

  public function __construct(private PDO $pdo)
  {
    $this->statementCreateOne = $pdo->prepare("
      INSERT INTO users (email, username, password)
      VALUES (:email, :username, :password)
    ");

    $this->statementCreateUserSettings = $pdo->prepare("
      INSERT INTO users_settings (user_id)
      VALUES (:userId);
    ");

    $this->statementReadOneByEmail = $pdo->prepare("
      SELECT *
      FROM users
      WHERE email=:email
    ");

    $this->statementReadOneProfile = $pdo->prepare("
      SELECT users.*,
      (
        SELECT COUNT(messages.id)
        FROM messages
        WHERE messages.author_id = users.id
      ) AS nbr_of_messages,
      (
        SELECT COUNT(discussions.id)
        FROM discussions
        WHERE discussions.author_id = users.id
      ) AS nbr_of_discussions,
      (
        SELECT COUNT(user_id)
        FROM likes
        WHERE user_id = users.id
      ) AS nbr_of_likes,
      (
        SELECT COUNT(follower)
        FROM follow_ups
        WHERE follower = users.id
      ) AS nbr_of_follow_ups,
      (
        SELECT COUNT(followee)
        FROM follow_ups
        WHERE followee = users.id
      ) AS nbr_of_followers
      FROM users
      WHERE users.id = :id
    ");

    $this->statementReadOneByUsername = $pdo->prepare("
      SELECT *
      FROM users
      WHERE username=:username
    ");

    // Update Statements preparation
    $this->statementUpdateUsername = $pdo->prepare("
      UPDATE users
      SET username = :username
      WHERE id = :id;
    ");

    $this->statementUpdateEmail = $pdo->prepare("
      UPDATE users
      SET email = :email
      WHERE id = :id;
    ");

    $this->statementUpdateAvatar = $pdo->prepare("
      UPDATE users
      SET avatar = :avatar
      WHERE id = :id;
    ");

    $this->statementUpdateBannerColor = $pdo->prepare("
      UPDATE users_settings
      SET banner_color = :color
      WHERE user_id = :userId;
    ");
  }

  public function createUser(array $user)
  {
    $hashedPassword = password_hash($user["password"], PASSWORD_ARGON2I);
    $this->statementCreateOne->bindValue(":email", $user["email"]);
    $this->statementCreateOne->bindValue(":username", $user["username"]);
    $this->statementCreateOne->bindValue(":password", $hashedPassword);
    $this->statementCreateOne->execute();
    $userId = $this->pdo->lastInsertId();
    $this->statementCreateUserSettings->bindValue(":userId", $userId);
    $this->statementCreateUserSettings->execute();
  }

  public function getUserByEmail(string $email): User | false
  {
    $this->statementReadOneByEmail->bindValue(":email", $email);
    $this->statementReadOneByEmail->execute();
    $user = new User($this->statementReadOneByEmail->fetch());
    return $user ?? false;
  }

  public function getUserByUsername(string $username): User | false
  {
    $this->statementReadOneByUsername->bindValue(":username", $username);
    return $this->statementReadOneByUsername->execute();
    $user = new User($this->statementReadOneByUsername->fetch());
    return $user ?? false;
  }

  public function getUserProfile(int $id): array
  {
    $this->statementReadOneProfile->bindValue(":id", $id);
    $this->statementReadOneProfile->execute();
    return $this->statementReadOneProfile->fetch();
  }

  public function usernameExists(string $username): bool
  {
    $this->statementReadOneByUsername->bindValue(":username", $username);
    $this->statementReadOneByUsername->execute();
    if ($this->statementReadOneByUsername->fetch()) {
      return true;
    }
    return false;
  }

  public function emailExists(string $email): bool
  {
    $this->statementReadOneByEmail->bindValue(":email", $email);
    $this->statementReadOneByEmail->execute();
    if ($this->statementReadOneByEmail->fetch()) {
      return true;
    }
    return false;
  }

  // Update methods
  public function updateUsername(int $id, string $username): bool
  {
    $this->statementUpdateUsername->bindValue(":id", $id);
    $this->statementUpdateUsername->bindValue(":username", $username);
    return $this->statementUpdateUsername->execute();
  }

  public function updateEmail(int $id, string $email): bool
  {
    $this->statementUpdateEmail->bindValue(":id", $id);
    $this->statementUpdateEmail->bindValue(":email", $email);
    return $this->statementUpdateEmail->execute();
  }

  public function updateAvatar(int $id, string $avatar): bool
  {
    $this->statementUpdateAvatar->bindValue(":id", $id);
    $this->statementUpdateAvatar->bindValue(":avatar", $avatar);
    return $this->statementUpdateAvatar->execute();
  }

  public function updateBannerColor(int $userId, string $color): void
  {
    $this->statementUpdateBannerColor->bindValue(":userId", $userId);
    $this->statementUpdateBannerColor->bindValue(":color", $color);
    $this->statementUpdateBannerColor->execute();
  }
}

return new UserAcces($pdo);
