<?php

namespace models;

use PDO;

class Clients
{
  public static function getAll()
  {
    $db = getDB();
    $stmt = $db->query("SELECT * FROM s4_clients");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }

  public static function getById($id)
  {
    $db = getDB();
    $stmt = $db->prepare("SELECT * FROM s4_clients WHERE id = ?");
    $stmt->execute([$id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
  }

  public static function create($data)
  {
    $db = getDB();
    $stmt = $db->prepare("INSERT INTO s4_clients (email, password, nom, prenom, user_id) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([$data->email, $data->password, $data->nom, $data->prenom, $data->user_id]);
    return $db->lastInsertId();
  }

  public static function update($id, $data)
  {
    $db = getDB();
    $stmt = $db->prepare("UPDATE s4_clients SET email = ?, password = ?, nom = ?, prenom = ?, user_id = ? WHERE id = ?");
    $stmt->execute([$data->email, $data->password, $data->nom, $data->prenom, $data->user_id, $id]);
  }

  public static function delete($id)
  {
    $db = getDB();
    $stmt = $db->prepare("DELETE FROM s4_clients WHERE id = ?");
    $stmt->execute([$id]);
  }
}
