<?php

require_once __DIR__ . "/../../Classes/User.classe.php";

class FollowUpAccess
{
  private PDOStatement $statementIsFollowing;
  private PDOStatement $statementCreateOne;
  private PDOStatement $statementDeleteOne;

  public function __construct(private PDO $pdo)
  {
    $this->statementIsFollowing = $pdo->prepare("
      SELECT follower
      FROM follow_ups
      WHERE follower = :followerId AND followee = :followeeId;
    ");

    $this->statementCreateOne = $pdo->prepare("
      INSERT INTO follow_ups
      VALUES (:followerId, :followeeId);
    ");

    $this->statementDeleteOne = $pdo->prepare("
      DELETE FROM follow_ups
      WHERE follower = :followerId AND followee =  :followeeId;
    ");
  }

  public function isFollowing(int $followerId, int $followeeId): bool
  {
    $this->statementIsFollowing->bindValue(":followerId", $followerId);
    $this->statementIsFollowing->bindValue(":followeeId", $followeeId);
    $this->statementIsFollowing->execute();
    if ($this->statementIsFollowing->fetch()) {
      return true;
    }
    return false;
  }

  public function followUser(int $followerId, int $followeeId): void
  {
    $this->statementCreateOne->bindValue(":followerId", $followerId);
    $this->statementCreateOne->bindValue(":followeeId", $followeeId);
    $this->statementCreateOne->execute();
  }

  public function unfollowUser(int $followerId, int $followeeId): void
  {
    $this->statementDeleteOne->bindValue(":followerId", $followerId);
    $this->statementDeleteOne->bindValue(":followeeId", $followeeId);
    $this->statementDeleteOne->execute();
  }
}

return new FollowUpAccess($pdo);
