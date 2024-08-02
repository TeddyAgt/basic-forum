<?php

class UserAcces
{
  private PDOStatement $statementCreateOne;
  private PDOStatement $statementReadOneByEmail;
  private PDOStatement $statementReadOneByUsername;
  private PDOStatement $statementReadOneProfile;

  public function __construct(private PDO $pdo)
  {
    $this->statementCreateOne = $pdo->prepare("
      INSERT INTO users (email, username, password)
      VALUES (:email, :username, :password)
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
  }

  public function createUser(array $user): bool
  {
    $hashedPassword = password_hash($user["password"], PASSWORD_ARGON2I);
    $this->statementCreateOne->bindValue(":email", $user["email"]);
    $this->statementCreateOne->bindValue(":username", $user["username"]);
    $this->statementCreateOne->bindValue(":password", $hashedPassword);
    return $this->statementCreateOne->execute();
  }

  public function getUserByEmail(string $email): array | bool
  {
    $this->statementReadOneByEmail->bindValue(":email", $email);
    $this->statementReadOneByEmail->execute();
    return $this->statementReadOneByEmail->fetch();
  }

  public function getUserByUsername(string $username): array | bool
  {
    $this->statementReadOneByUsername->bindValue(":username", $username);
    $this->statementReadOneByUsername->execute();
    return $this->statementReadOneByUsername->fetch();
  }

  public function  getUserProfile(int $id): array
  {
    $this->statementReadOneProfile->bindValue(":id", $id);
    $this->statementReadOneProfile->execute();
    return $this->statementReadOneProfile->fetch();
  }
}

return new UserAcces($pdo);
