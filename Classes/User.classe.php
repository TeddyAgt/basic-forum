<?php

class User
{
  public int $id;
  public string $username;
  public string $email;
  public string $password;
  public string $role;
  public string $signupDate;
  public string $profilePicture;

  public function __construct(array $user)
  {
    $this->id = $user["id"];
    $this->username = $user["username"];
    $this->email = $user["email"];
    $this->password = $user["password"];
    $this->role = $user["role"];
    $this->signupDate = $user["signup_date"];
    $this->profilePicture = $user["profile_picture"];
  }
}
