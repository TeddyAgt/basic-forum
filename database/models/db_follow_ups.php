<?php

require_once __DIR__ . "/../../Classes/User.classe.php";

class FollowUpAccess
{
  private PDOStatement $statementIsFollowing;
  private PDOStatement $statementCreateOne;
  private PDOStatement $statementDeleteOne;
  private PDOStatement $statementGetFollowUpsByFollower;
  private PDOStatement $statementGetFollowUpsByFollowee;

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

    $this->statementGetFollowUpsByFollower = $pdo->prepare("
      SELECT users.id, users.username, users.avatar
      FROM follow_ups
      JOIN users ON users.id = follow_ups.followee
      WHERE follower = :userId;
    ");

    $this->statementGetFollowUpsByFollowee = $pdo->prepare("
      SELECT users.id, users.username, users.avatar
      FROM follow_ups
      JOIN users ON users.id = follow_ups.follower
      WHERE followee = :userId;
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

  public function getFollowUpsByFollower(int $userId): array
  {
    $this->statementGetFollowUpsByFollower->bindValue(":userId", $userId);
    $this->statementGetFollowUpsByFollower->execute();
    return $this->statementGetFollowUpsByFollower->fetchAll();
  }

  public function getFollowUpsByFollowee(int $userId): array
  {
    $this->statementGetFollowUpsByFollowee->bindValue(":userId", $userId);
    $this->statementGetFollowUpsByFollowee->execute();
    return $this->statementGetFollowUpsByFollowee->fetchAll();
  }
}

return new FollowUpAccess($pdo);
