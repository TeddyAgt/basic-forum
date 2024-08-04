<?php

require_once __DIR__ . "/../../Classes/Discussion.classe.php";

class DiscussionAccess
{

  // Categories PDO Statements
  private PDOStatement $statementGetAllCategories;

  // Discussions PDO Statements
  private PDOStatement $statementCreateOneDiscussion;
  private PDOStatement $statementGetLastNDiscussions;
  private PDOStatement $statementGetLastNDiscussionsByUser;
  private PDOStatement $statementGetDiscussionById;

  // Messages PDO Statements
  private PDOStatement $statementCreateOneMessage;
  private PDOStatement $statementGetLastNMessages;
  private PDOStatement $statementGetLastNMessagesByUser;
  private PDOStatement $statementGetMessagesByDiscussionId;
  private PDOStatement $statementGetMessagesPageByDiscussionId;
  private PDOStatement $statementGetMessageAuthor;
  private PDOStatement $statementDeleteMessage;

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
      SELECT *, (
        SELECT COUNT(messages.id)
        FROM messages
        WHERE discussion_id = :discussionId
      ) AS nbr_of_messages
      FROM discussions
      WHERE id = :discussionId
    ");

    $this->statementGetLastNDiscussions = $pdo->prepare("
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
        LIMIT :N;
    ");

    $this->statementGetLastNDiscussionsByUser = $pdo->prepare("
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
        WHERE discussions.author_id = :userId
        GROUP BY discussions.id
        ORDER BY creation_date DESC
        LIMIT :N;
    ");

    // Messages Statements Preparation
    $this->statementCreateOneMessage = $pdo->prepare("
      INSERT INTO messages (author_id, discussion_id, text, responds_to)
      VALUES (:authorId, :discussionId, :text, :respondsTo)
    ");

    $this->statementGetMessagesByDiscussionId = $pdo->prepare("
      SELECT messages.*, username, role, profile_picture, (
        SELECT COUNT(user_id)
        FROM likes
        WHERE message_id = messages.id
      ) AS likes
      FROM messages
      INNER JOIN users ON messages.author_id = users.id
      WHERE discussion_id = :discussionId
      ORDER BY id ASC
    ");

    $this->statementGetMessagesPageByDiscussionId = $pdo->prepare("
      SELECT messages.*, username, role, profile_picture, (
        SELECT COUNT(user_id)
        FROM likes
        WHERE message_id = messages.id
      ) AS likes
      FROM messages
      INNER JOIN users ON messages.author_id = users.id
      WHERE discussion_id = :discussionId
      ORDER BY id ASC
      LIMIT :limit
      OFFSET :page
    ");

    $this->statementGetLastNMessages = $pdo->prepare("
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
        LIMIT :N;
    ");

    $this->statementGetLastNMessagesByUser = $pdo->prepare("
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
        WHERE m1.author_id = :userId
        GROUP BY m1.id
        ORDER BY creation_date DESC
        LIMIT :N;
    ");

    $this->statementGetMessageAuthor = $pdo->prepare("
      SELECT author_id
      FROM messages
      WHERE id = :id
    ");

    $this->statementDeleteMessage = $pdo->prepare("
      UPDATE messages
      SET text = :text,
      modification_date = NOW(),
      status = 0
      WHERE id = :id  
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

  public function getDiscussionPageById(int $discussionId, int $page = 0, int $limit = 10): Discussion | bool
  {
    $this->statementGetDiscussionById->bindValue(":discussionId", $discussionId);
    if ($this->statementGetDiscussionById->execute()) {
      $discussion = new Discussion($this->statementGetDiscussionById->fetch());

      $messageList = $this->getMessagesPageByDiscussionId($discussionId, $page, $limit);
      if ($messageList) {
        foreach ($messageList as $m) {
          $discussion->addMessage(new Message($m));
        }
      }
    }

    return $discussion ?? false;
  }

  public function getLastNDiscussions(int $n = 10): array
  {
    $this->statementGetLastNDiscussions->bindValue(":N", $n, PDO::PARAM_INT);
    $this->statementGetLastNDiscussions->execute();
    return $this->statementGetLastNDiscussions->fetchAll() ?? [];
  }

  public function getLastNDiscussionsByUser(int $userId, int $n = 10): array
  {
    $this->statementGetLastNDiscussionsByUser->bindValue(":N", $n, PDO::PARAM_INT);
    $this->statementGetLastNDiscussionsByUser->bindValue(":userId", $userId);
    $this->statementGetLastNDiscussionsByUser->execute();
    return $this->statementGetLastNDiscussionsByUser->fetchAll() ?? [];
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

  public function getMessagesPageByDiscussionId(int $discussionId, int $page, int $limit): array | bool
  {
    $this->statementGetMessagesPageByDiscussionId->bindValue(":discussionId", $discussionId);
    $this->statementGetMessagesPageByDiscussionId->bindValue(":page", ($page * $limit) - $limit, PDO::PARAM_INT);
    $this->statementGetMessagesPageByDiscussionId->bindValue(":limit", $limit, PDO::PARAM_INT);
    $this->statementGetMessagesPageByDiscussionId->execute();
    return $this->statementGetMessagesPageByDiscussionId->fetchAll();
  }

  public function getLastNMessages(int $n = 10): array
  {
    $this->statementGetLastNMessages->bindValue(":N", $n, PDO::PARAM_INT);
    $this->statementGetLastNMessages->execute();
    return $this->statementGetLastNMessages->fetchAll() ?? [];
  }

  public function getLastNMessagesByUser(int $userId, int $n = 10): array
  {
    $this->statementGetLastNMessagesByUser->bindValue(":N", $n, PDO::PARAM_INT);
    $this->statementGetLastNMessagesByUser->bindValue(":userId", $userId);
    $this->statementGetLastNMessagesByUser->execute();
    return $this->statementGetLastNMessagesByUser->fetchAll() ?? [];
  }


  public function GetMessageAuthor(int $id): int
  {
    $this->statementGetMessageAuthor->bindValue(":id", $id);
    $this->statementGetMessageAuthor->execute();
    return $this->statementGetMessageAuthor->fetch()["author_id"];
  }

  public function deleteMessage(string $text, int $id): void
  {
    $this->statementDeleteMessage->bindValue(":text", $text);
    $this->statementDeleteMessage->bindValue(":id", $id);
    $this->statementDeleteMessage->execute();
  }
}

return new DiscussionAccess($pdo);
