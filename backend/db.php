<?php

global $db;

function getDB()
{
  $host = 'localhost';
  $dbname = 'test_s4_web';
  $username = 'razherana';
  $password = '';

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
