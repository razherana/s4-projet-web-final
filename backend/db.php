<?php

global $db;

function getDB()
{
  $host = HOST;
  $dbname = DB_NAME;
  $username = USER;
  $password = PASSWORD;

  try {
    global $db;
    if (!isset($db))
      $db = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
      ]);
    return $db;
  } catch (PDOException $e) {
    die(json_encode(['error' => $e->getMessage()]));
  }
}
