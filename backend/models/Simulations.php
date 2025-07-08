<?php

namespace models;

use PDO;

class Simulations
{
  public static function getAll()
  {
    $db = getDB();
    $stmt = $db->query("SELECT * FROM s4_simulations");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }

  public static function getById($id)
  {
    $db = getDB();
    $stmt = $db->prepare("SELECT * FROM s4_simulations WHERE id = ?");
    $stmt->execute([$id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
  }

  public static function getAllByClient($clientId)
  {
    $db = getDB();
    $stmt = $db->prepare("SELECT * FROM s4_simulations WHERE client_id = ?");
    $stmt->execute([$clientId]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }

  public static function create($data)
  {
    $db = getDB();
    $stmt = $db->prepare("INSERT INTO s4_simulations (client_id, type_pret_id, montant, duree, date_creation, delai) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->execute([$data->client_id, $data->type_pret_id, $data->montant, $data->duree, $data->date_creation, $data->delai ?? 0]);
    return $db->lastInsertId();
  }

  public static function update($id, $data)
  {
    $db = getDB();
    
    // Build dynamic update query based on non-null values
    $updateFields = [];
    $updateValues = [];
    
    if (isset($data->client_id) && $data->client_id !== null) {
      $updateFields[] = "client_id = ?";
      $updateValues[] = $data->client_id;
    }
    
    if (isset($data->type_pret_id) && $data->type_pret_id !== null) {
      $updateFields[] = "type_pret_id = ?";
      $updateValues[] = $data->type_pret_id;
    }
    
    if (isset($data->montant) && $data->montant !== null) {
      $updateFields[] = "montant = ?";
      $updateValues[] = $data->montant;
    }
    
    if (isset($data->duree) && $data->duree !== null) {
      $updateFields[] = "duree = ?";
      $updateValues[] = $data->duree;
    }
    
    if (isset($data->date_creation) && $data->date_creation !== null) {
      $updateFields[] = "date_creation = ?";
      $updateValues[] = $data->date_creation;
    }
    
    if (isset($data->delai) && $data->delai !== null) {
      $updateFields[] = "delai = ?";
      $updateValues[] = $data->delai;
    }
    
    // Only proceed if there are fields to update
    if (empty($updateFields)) {
      return; // No fields to update
    }
    
    // Add the ID to the end of values array
    $updateValues[] = $id;
    
    $sql = "UPDATE s4_simulations SET " . implode(", ", $updateFields) . " WHERE id = ?";
    $stmt = $db->prepare($sql);
    $stmt->execute($updateValues);
  }

  public static function delete($id)
  {
    $db = getDB();
    $stmt = $db->prepare("DELETE FROM s4_simulations WHERE id = ?");
    $stmt->execute([$id]);
  }

  public static function deleteByClient($clientId)
  {
    $db = getDB();
    $stmt = $db->prepare("DELETE FROM s4_simulations WHERE client_id = ?");
    $stmt->execute([$clientId]);
  }
}
