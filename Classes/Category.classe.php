<?php

class Category
{
  public int $id;
  public string $name;
  public string $icon;
  public int $nbrOfDiscussions;
  public int $pages;
  public array $discussions;

  public function __construct(array $category)
  {
    $this->id = $category["id"];
    $this->name = $category["name"];
    $this->nbrOfDiscussions = $category["nbr_of_discussions"];
    $this->discussions = [];
    $this->pages = ceil($this->nbrOfDiscussions / 10);
  }

  public function setDiscussions($discussions)
  {
    $this->discussions = $discussions;
  }
}
