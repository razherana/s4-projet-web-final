<?php

namespace models;

use PDO;

class Users
{
  public static function getAll()
  {
    $db = getDB();
    $stmt = $db->query("SELECT * FROM s4_users");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }

  public static function getById($id)
  {
    $db = getDB();
    $stmt = $db->prepare("SELECT * FROM s4_users WHERE id = ?");
    $stmt->execute([$id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
  }

  public static function create($data)
  {
    $db = getDB();
    $stmt = $db->prepare("INSERT INTO s4_users (nom) VALUES (?)");
    $stmt->execute([$data->nom]);
    return $db->lastInsertId();
  }

  public static function update($id, $data)
  {
    $db = getDB();
    $stmt = $db->prepare("UPDATE s4_users SET nom = ? WHERE id = ?");
    $stmt->execute([$data->nom, $id]);
  }

  public static function delete($id)
  {
    $db = getDB();
    $stmt = $db->prepare("DELETE FROM s4_users WHERE id = ?");
    $stmt->execute([$id]);
  }
}
