<?php

class UserAcces
{
  private PDOStatement $statementCreateOne;
  private PDOStatement $statementReadOneByEmail;
  private PDOStatement $statementReadOneByUsername;

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
}

return new UserAcces($pdo);
