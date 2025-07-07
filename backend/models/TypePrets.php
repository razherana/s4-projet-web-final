<?php

namespace models;

use PDO;

class TypePrets
{
  public static function getAll()
  {
    $db = getDB();
    $stmt = $db->query("SELECT * FROM s4_type_prets");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }

  public static function getById($id)
  {
    $db = getDB();
    $stmt = $db->prepare("SELECT * FROM s4_type_prets WHERE id = ?");
    $stmt->execute([$id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
  }

  public static function create($data)
  {
    $db = getDB();
    $stmt = $db->prepare("INSERT INTO s4_type_prets (nom, taux_interet, duree_min, duree_max, taux_assurance) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([$data->nom, $data->taux_interet, $data->duree_min, $data->duree_max, $data->taux_assurance]);
    return $db->lastInsertId();
  }

  public static function update($id, $data)
  {
    $db = getDB();
    
    // Build dynamic update query based on non-null values
    $updateFields = [];
    $updateValues = [];
    
    if (isset($data->nom) && $data->nom !== null) {
      $updateFields[] = "nom = ?";
      $updateValues[] = $data->nom;
    }
    
    if (isset($data->taux_interet) && $data->taux_interet !== null) {
      $updateFields[] = "taux_interet = ?";
      $updateValues[] = $data->taux_interet;
    }
    
    if (isset($data->duree_min) && $data->duree_min !== null) {
      $updateFields[] = "duree_min = ?";
      $updateValues[] = $data->duree_min;
    }
    
    if (isset($data->duree_max) && $data->duree_max !== null) {
      $updateFields[] = "duree_max = ?";
      $updateValues[] = $data->duree_max;
    }
    
    if (isset($data->taux_assurance) && $data->taux_assurance !== null) {
      $updateFields[] = "taux_assurance = ?";
      $updateValues[] = $data->taux_assurance;
    }
    
    // Only proceed if there are fields to update
    if (empty($updateFields)) {
      return; // No fields to update
    }
    
    // Add the ID to the end of values array
    $updateValues[] = $id;
    
    $sql = "UPDATE s4_type_prets SET " . implode(", ", $updateFields) . " WHERE id = ?";
    $stmt = $db->prepare($sql);
    $stmt->execute($updateValues);
  }

  public static function delete($id)
  {
    $db = getDB();
    $stmt = $db->prepare("DELETE FROM s4_type_prets WHERE id = ?");
    $stmt->execute([$id]);
  }
}
