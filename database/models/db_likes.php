<?php

class LikeAccess
{

  private PDOStatement $statementCreateOneLike;
  private PDOStatement $statementSelectOneLike;
  private PDOStatement $statementSelectMessageLikeCount;
  private PDOStatement $statementDeleteOneLike;

  public function __construct(private PDO $pdo)
  {
    $this->statementCreateOneLike = $pdo->prepare("
      INSERT INTO likes
      VALUES (:messageId, :userId)
    ");

    $this->statementSelectOneLike = $pdo->prepare("
      SELECT *
      FROM likes
      WHERE message_id = :messageId AND user_id = :userId
    ");

    $this->statementDeleteOneLike = $pdo->prepare("
      DELETE FROM likes
      WHERE message_id = :messageId AND user_id = :userId
    ");

    $this->statementSelectMessageLikeCount = $pdo->prepare("
      SELECT COUNT(user_id) AS count
      FROM likes
      WHERE message_id = :messageId
    ");
  }

  public function createOneLike(int $messageId, int $userId): array
  {
    $this->statementCreateOneLike->bindValue(":messageId", $messageId);
    $this->statementCreateOneLike->bindValue(":userId", $userId);
    $this->statementCreateOneLike->execute();
    return $this->getMessageLikeCount($messageId);
  }

  public function hasLiked(int $messageId, int $userId): bool
  {
    $this->statementSelectOneLike->bindValue(":messageId", $messageId);
    $this->statementSelectOneLike->bindValue(":userId", $userId);
    $this->statementSelectOneLike->execute();
    return $this->statementSelectOneLike->fetch() ? true : false;
  }

  public function deleteOneLike(int $messageId, int $userId): array
  {
    $this->statementDeleteOneLike->bindValue(":messageId", $messageId);
    $this->statementDeleteOneLike->bindValue(":userId", $userId);
    $this->statementDeleteOneLike->execute();
    return $this->getMessageLikeCount($messageId);
  }

  public function getMessageLikeCount(int $messageId): array
  {
    $this->statementSelectMessageLikeCount->bindValue(":messageId", $messageId);
    $this->statementSelectMessageLikeCount->execute();
    return $this->statementSelectMessageLikeCount->fetch();
  }
}

return new LikeAccess($pdo);
