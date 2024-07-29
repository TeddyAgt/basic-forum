<?php

class SessionAccess
{
  private string $secret;
  private PDOStatement $statementCreateOne;

  public function __construct(private PDO $pdo)
  {
    $this->secret = getenv("secret");

    $this->statementCreateOne = $pdo->prepare("
      INSERT INTO sessions
      VALUES (:id, :userId)
    ");
  }

  public function createSession(int $userId): void
  {
    $sessionId = bin2hex(random_bytes(32));
    $this->statementCreateOne->bindValue(":id", $sessionId);
    $this->statementCreateOne->bindValue(":userId", $userId);
    $this->statementCreateOne->execute();

    $signature = hash_hmac("sha256", $sessionId, $this->secret);
    setcookie("session", $sessionId, time() + 1209600, "", "");
    setcookie("signature", $signature, time() + 1209600, "", "");
  }
}

return new SessionAccess($pdo);
