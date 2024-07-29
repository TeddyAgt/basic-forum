<?php

class SubjectAccess
{

  // Categories PDO Statements
  private PDOStatement $statementGetAllCategories;

  // Subjects PDO Statements
  private PDOStatement $statementCreateOneSubject;

  // Messages PDO Statements
  private PDOStatement $statementCreateOneMessage;

  public function __construct(private PDO $pdo)
  {
    // Categories Statements Preparation
    $this->statementGetAllCategories = $pdo->prepare("
      SELECT *
      FROM categories
      ORDER BY name ASC
    ");

    // Subjects Statements Preparation
    $this->statementCreateOneSubject = $pdo->prepare("
      INSERT INTO subjects (title, author_id, category_id)
      VALUES (:title, :authorId, :categoryId)
    ");

    // Messages Statements Preparation
    $this->statementCreateOneMessage = $pdo->prepare("
      INSERT INTO messages (author_id, subject_id, text)
      VALUES (:authorId, :subjectId, :text)
    ");
  }

  // Categories CRUD Methods
  public function getAllCategories(): array
  {
    $this->statementGetAllCategories->execute();
    return $this->statementGetAllCategories->fetchAll();
  }

  // Subjects CRUD Methods
  public function createOneSubject(array $subject): void
  {
    $this->statementCreateOneSubject->bindValue(":title", $subject["title"]);
    $this->statementCreateOneSubject->bindValue(":authorId", $subject["authorId"]);
    $this->statementCreateOneSubject->bindValue(":categoryId", $subject["categoryId"]);
    $this->statementCreateOneSubject->execute();

    $subjectId = $this->pdo->lastInsertId();

    $this->createOneMessage([
      "authorId" => $subject["authorId"],
      "subjectId" => $subjectId,
      "text" => $subject["text"]
    ]);
  }

  // Messages CRUD Methods
  public function createOneMessage(array $message): void
  {
    $this->statementCreateOneMessage->bindValue(":authorId", $message["authorId"]);
    $this->statementCreateOneMessage->bindValue(":subjectId", $message["subjectId"]);
    $this->statementCreateOneMessage->bindValue(":text", $message["text"]);
    $this->statementCreateOneMessage->execute();
  }
}

return new SubjectAccess($pdo);
