<?php

function verifyAvatarExtension(string $avatar): bool
{
  $ext = pathinfo($avatar, PATHINFO_EXTENSION);
  if (!$ext === "png" || !$ext === "jpg" || !$ext === "jpeg") {
    return false;
  } else {
    return true;
  }
}

function renameAvatar(User $user, array $avatar): string
{
  $ext = pathinfo($avatar["name"], PATHINFO_EXTENSION);
  return "/public/assets/images/avatars/" . replaceSpecialChars($user->username) . "_avatar-main." . $ext;
}

function replaceSpecialChars(string $string): string
{
  $utf8 = array(
    '/[áàâãªä]/u' => 'a',
    '/[ÁÀÂÃÄ]/u' => 'A',
    '/[ÍÌÎÏ]/u' => 'I',
    '/[íìîï]/u' => 'i',
    '/[éèêë]/u' => 'e',
    '/[ÉÈÊË]/u' => 'E',
    '/[óòôõºö]/u' => 'o',
    '/[ÓÒÔÕÖ]/u' => 'O',
    '/[úùûü]/u' => 'u',
    '/[ÚÙÛÜ]/u' => 'U',
    '/ç/' => 'c',
    '/Ç/' => 'C',
    '/ñ/' => 'n',
    '/Ñ/' => 'N',
    '/[«»]/u' => ' ', // guillemet double
    '/ /' => ' ', // espace insécable (équiv. à 0x160)
    '/[\(\)\\\;\{\}\.]/' => ''
  );

  return preg_replace(array_keys($utf8), array_values($utf8), $string);
}
