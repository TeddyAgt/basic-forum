<?php

class User
{
  public int $id;
  public string $username;
  public string $email;
  private string $_password;
  public string $role;
  public string $signupDate;
  public string $avatar;
  public string $about;
  public array $settings;

  public function __construct(array $user)
  {
    $this->id = $user["id"];
    $this->username = $user["username"];
    $this->email = $user["email"];
    $this->_password = $user["password"];
    $this->role = $user["role"];
    $this->signupDate = $user["signup_date"];
    $this->avatar = $user["avatar"];
    $this->about = $user["about"] ?? "";
  }

  public function get_password(): string
  {
    return $this->_password;
  }
}
