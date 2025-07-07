<?php

namespace models;

use PDO;

class FondHistoriques
{
  public static function getAll()
  {
    $db = getDB();
    $stmt = $db->query("SELECT * FROM s4_fond_historiques");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }

  public static function getById($id)
  {
    $db = getDB();
    $stmt = $db->prepare("SELECT * FROM s4_fond_historiques WHERE id = ?");
    $stmt->execute([$id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
  }

  public static function create($data)
  {
    $db = getDB();
    $stmt = $db->prepare("INSERT INTO s4_fond_historiques (fond_id, description, montant, est_sortie) VALUES (?, ?, ?, ?)");
    $stmt->execute([$data->fond_id, $data->description, $data->montant, $data->est_sortie]);
    return $db->lastInsertId();
  }

  public static function update($id, $data)
  {
    $db = getDB();
    
    // Build dynamic update query based on non-null values
    $updateFields = [];
    $updateValues = [];
    
    if (isset($data->fond_id) && $data->fond_id !== null) {
      $updateFields[] = "fond_id = ?";
      $updateValues[] = $data->fond_id;
    }
    
    if (isset($data->description) && $data->description !== null) {
      $updateFields[] = "description = ?";
      $updateValues[] = $data->description;
    }
    
    if (isset($data->montant) && $data->montant !== null) {
      $updateFields[] = "montant = ?";
      $updateValues[] = $data->montant;
    }
    
    if (isset($data->est_sortie) && $data->est_sortie !== null) {
      $updateFields[] = "est_sortie = ?";
      $updateValues[] = $data->est_sortie;
    }
    
    // Only proceed if there are fields to update
    if (empty($updateFields)) {
      return; // No fields to update
    }
    
    // Add the ID to the end of values array
    $updateValues[] = $id;
    
    $sql = "UPDATE s4_fond_historiques SET " . implode(", ", $updateFields) . " WHERE id = ?";
    $stmt = $db->prepare($sql);
    $stmt->execute($updateValues);
  }

  public static function delete($id)
  {
    $db = getDB();
    $stmt = $db->prepare("DELETE FROM s4_fond_historiques WHERE id = ?");
    $stmt->execute([$id]);
  }
}
