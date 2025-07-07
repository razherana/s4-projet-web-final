<?php

namespace models;

use PDO;

class Clients
{
  public static function getAll()
  {
    $db = getDB();
    $stmt = $db->query("SELECT * FROM s4_clients WHERE user_id != 1");
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
    
    // Build dynamic update query based on non-null values
    $updateFields = [];
    $updateValues = [];
    
    if (isset($data->email) && $data->email !== null) {
      $updateFields[] = "email = ?";
      $updateValues[] = $data->email;
    }
    
    if (isset($data->password) && $data->password !== null) {
      $updateFields[] = "password = ?";
      $updateValues[] = $data->password;
    }
    
    if (isset($data->nom) && $data->nom !== null) {
      $updateFields[] = "nom = ?";
      $updateValues[] = $data->nom;
    }
    
    if (isset($data->prenom) && $data->prenom !== null) {
      $updateFields[] = "prenom = ?";
      $updateValues[] = $data->prenom;
    }
    
    if (isset($data->user_id) && $data->user_id !== null) {
      $updateFields[] = "user_id = ?";
      $updateValues[] = $data->user_id;
    }
    
    // Only proceed if there are fields to update
    if (empty($updateFields)) {
      return; // No fields to update
    }
    
    // Add the ID to the end of values array
    $updateValues[] = $id;
    
    $sql = "UPDATE s4_clients SET " . implode(", ", $updateFields) . " WHERE id = ?";
    $stmt = $db->prepare($sql);
    $stmt->execute($updateValues);
  }

  public static function delete($id)
  {
    $db = getDB();
    $stmt = $db->prepare("DELETE FROM s4_clients WHERE id = ?");
    $stmt->execute([$id]);
  }
}
