<?php

require_once __DIR__ . "/../../Classes/Discussion.classe.php";

class DiscussionAccess
{

  // Categories PDO Statements
  private PDOStatement $statementGetAllCategories;

  // Discussions PDO Statements
  private PDOStatement $statementCreateOneDiscussion;
  private PDOStatement $statementGetLast10Discussions;
  private PDOStatement $statementGetDiscussionById;

  // Messages PDO Statements
  private PDOStatement $statementCreateOneMessage;
  private PDOStatement $statementGetLast10Messages;
  private PDOStatement $statementGetMessagesByDiscussionId;

  public function __construct(private PDO $pdo)
  {
    // Categories Statements Preparation
    $this->statementGetAllCategories = $pdo->prepare("
      SELECT *
      FROM categories
      ORDER BY name ASC
    ");

    // Discussions Statements Preparation
    $this->statementCreateOneDiscussion = $pdo->prepare("
      INSERT INTO discussions (title, author_id, category_id)
      VALUES (:title, :authorId, :categoryId)
    ");

    $this->statementGetDiscussionById = $pdo->prepare("
      SELECT *
      FROM discussions
      WHERE id = :discussionId
    ");

    $this->statementGetLast10Discussions = $pdo->prepare("
      SELECT 
        discussions.*,
        categories.name AS category_name,
        categories.icon AS category_icon,
        (
          SELECT COUNT(discussion_id)
          FROM messages
          WHERE discussions.id = messages.discussion_id
        ) AS nb_responses, 
        (
          SELECT creation_date
          FROM messages
          WHERE discussions.id = messages.discussion_id
          ORDER BY creation_date DESC
          LIMIT 1
        ) AS latest_response,
        users.username
        FROM discussions
        INNER JOIN categories ON discussions.category_id = categories.id
        INNER JOIN users ON discussions.author_id = users.id
        GROUP BY discussions.id
        ORDER BY creation_date DESC
        LIMIT 10;
    ");

    // Messages Statements Preparation
    $this->statementCreateOneMessage = $pdo->prepare("
      INSERT INTO messages (author_id, discussion_id, text, responds_to)
      VALUES (:authorId, :discussionId, :text, :respondsTo)
    ");

    $this->statementGetMessagesByDiscussionId = $pdo->prepare("
      SELECT messages.*, username, profile_picture, (
        SELECT COUNT(user_id)
        FROM likes
        WHERE message_id = messages.id
      ) AS likes
      FROM messages
      INNER JOIN users ON messages.author_id = users.id
      WHERE discussion_id = :discussionId
      ORDER BY id ASC
    ");

    $this->statementGetLast10Messages = $pdo->prepare("
      SELECT 
        m1.*,
        discussions.title AS discussion_title,
        categories.name AS category_name,
        categories.icon AS category_icon,
        (
          SELECT COUNT(discussion_id)
          FROM messages AS m2
          WHERE m1.discussion_id = m2.discussion_id
        ) AS nb_responses,
        users.username
        FROM messages AS m1
        INNER JOIN discussions ON m1.discussion_id = discussions.id
        INNER JOIN categories ON discussions.category_id = categories.id
        INNER JOIN users ON m1.author_id = users.id
        GROUP BY m1.id
        ORDER BY creation_date DESC
        LIMIT 10;
    ");
  }

  // Categories CRUD Methods
  public function getAllCategories(): array
  {
    $this->statementGetAllCategories->execute();
    return $this->statementGetAllCategories->fetchAll();
  }

  // Discussions CRUD Methods
  public function createOneDiscussion(array $discussion): void
  {
    $this->statementCreateOneDiscussion->bindValue(":title", $discussion["title"]);
    $this->statementCreateOneDiscussion->bindValue(":authorId", $discussion["authorId"]);
    $this->statementCreateOneDiscussion->bindValue(":categoryId", $discussion["categoryId"]);
    $this->statementCreateOneDiscussion->execute();

    $discussionId = $this->pdo->lastInsertId();

    $this->createOneMessage([
      "authorId" => $discussion["authorId"],
      "discussionId" => $discussionId,
      "text" => $discussion["text"]
    ]);
  }

  public function getDiscussionById(int $discussionId): Discussion | bool
  {
    $this->statementGetDiscussionById->bindValue(":discussionId", $discussionId);
    if ($this->statementGetDiscussionById->execute()) {
      $discussion = new Discussion($this->statementGetDiscussionById->fetch());

      $messageList = $this->getMessagesByDiscussionId($discussionId);
      if ($messageList) {
        foreach ($messageList as $m) {
          $discussion->addMessage(new Message($m));
        }
      }
    }

    return $discussion ?? false;
  }

  public function getLast10Discussions(): array
  {
    $this->statementGetLast10Discussions->execute();
    return $this->statementGetLast10Discussions->fetchAll() ?? [];
  }

  // Messages CRUD Methods
  public function createOneMessage(array $message): void
  {
    $this->statementCreateOneMessage->bindValue(":authorId", $message["authorId"]);
    $this->statementCreateOneMessage->bindValue(":discussionId", $message["discussionId"]);
    $this->statementCreateOneMessage->bindValue(":text", $message["text"]);
    $this->statementCreateOneMessage->bindValue(":respondsTo", $message["respondsTo"]);
    $this->statementCreateOneMessage->execute();
  }

  public function getMessagesByDiscussionId(int $discussionId): array | bool
  {
    $this->statementGetMessagesByDiscussionId->bindValue(":discussionId", $discussionId);
    $this->statementGetMessagesByDiscussionId->execute();
    return $this->statementGetMessagesByDiscussionId->fetchAll();
  }

  public function getLast10Messages(): array
  {
    $this->statementGetLast10Messages->execute();
    return $this->statementGetLast10Messages->fetchAll() ?? [];
  }
}

return new DiscussionAccess($pdo);
