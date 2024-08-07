<?php

require_once __DIR__ . "/../../Classes/User.classe.php";

class SessionAccess
{
  private string $secret;
  private PDOStatement $statementCreateOne;
  private PDOStatement $statementReadOneSession;
  private PDOStatement $statementReadOneUser;
  private PDOStatement $statementDeleteOne;

  public function __construct(private PDO $pdo)
  {
    $this->secret = getenv("secret");

    $this->statementCreateOne = $pdo->prepare("
      INSERT INTO sessions
      VALUES (:id, :userId)
    ");

    $this->statementReadOneSession = $pdo->prepare("
      SELECT *
      FROM sessions
      WHERE id = :sessionId
    ");

    $this->statementReadOneUser = $pdo->prepare("
      SELECT *
      FROM users
      WHERE id = :userId
    ");

    $this->statementDeleteOne = $pdo->prepare("
      DELETE FROM sessions
      WHERE id=:sessionId
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

  public function isLoggedIn(): User | false
  {
    $sessionId = $_COOKIE["session"] ?? "";
    $signature = $_COOKIE["signature"] ?? "";

    if ($sessionId && $signature) {
      $hash = hash_hmac("sha256", $sessionId, $this->secret);

      if (hash_equals($hash, $signature)) {
        $this->statementReadOneSession->bindValue(":sessionId", $sessionId);
        $this->statementReadOneSession->execute();
        $session = $this->statementReadOneSession->fetch();

        if ($session) {
          $this->statementReadOneUser->bindValue(":userId", $session["user_id"]);
          $this->statementReadOneUser->execute();
          $user = new User($this->statementReadOneUser->fetch());
        }
      }
    }
    return $user ?? false;
  }

  public function deleteSession(): void
  {
    $sessionId = $_COOKIE["session"] ?? "";
    if ($sessionId) {
      $this->statementDeleteOne->bindValue(":sessionId", $sessionId);
      $this->statementDeleteOne->execute();
      setcookie("session", "", time() - 1);
      setcookie("signature", "", time() - 1);
    }
  }
}

return new SessionAccess($pdo);
