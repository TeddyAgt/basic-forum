<?php

class Discussion
{
  public int $id;
  public string $title;
  public int $authorId;
  public int $nbrOfMessages;
  public int $pages;
  public array $messages;

  public function __construct(array $discussion)
  {
    $this->id = $discussion["id"];
    $this->title = $discussion["title"];
    $this->authorId = $discussion["author_id"];
    $this->nbrOfMessages = $discussion["nbr_of_messages"];
    $this->messages = [];
    $this->pages = ceil($this->nbrOfMessages / 10);
  }

  public function addMessage(Message $message): void
  {
    array_push($this->messages, $message);
  }
}

class Message
{
  public int $id;
  public array $author;
  public string $text;
  public string $creationDate;
  public string $modificationDate;
  public ?int $respondsTo;
  public int $status;
  public int $likes;

  public function __construct(array $message)
  {
    $this->id = $message["id"];
    $this->author = [
      "id" => $message["author_id"],
      "username" => $message["username"],
      "role" => $message["role"],
      "avatar" => $message["avatar"],
      "banner_color" => $message["banner_color"],
      "mentions_color" => $message["mentions_color"],
    ];
    $this->text = $message["text"];
    $this->creationDate = $message["creation_date"];
    $this->modificationDate = $message["modification_date"] ?? "";
    $this->respondsTo = $message["responds_to"];
    $this->status = $message["status"];
    $this->likes = $message["likes"];
  }
}
