<?php

namespace models;

use PDO;

class Fonds
{
  public static function getAll()
  {
    $db = getDB();
    $stmt = $db->query("SELECT * FROM s4_fonds");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }

  public static function getById($id)
  {
    $db = getDB();
    $stmt = $db->prepare("SELECT * FROM s4_fonds WHERE id = ?");
    $stmt->execute([$id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
  }

  public static function create($data)
  {
    $db = getDB();
    $stmt = $db->prepare("INSERT INTO s4_fonds (montant_initial, description, source_fond_id) VALUES (?, ?, ?)");
    $stmt->execute([$data->montant_initial, $data->description, $data->source_fond_id]);
    return $db->lastInsertId();
  }

  public static function update($id, $data)
  {
    $db = getDB();
    
    // Build dynamic update query based on non-null values
    $updateFields = [];
    $updateValues = [];
    
    if (isset($data->montant_initial) && $data->montant_initial !== null) {
      $updateFields[] = "montant_initial = ?";
      $updateValues[] = $data->montant_initial;
    }
    
    if (isset($data->description) && $data->description !== null) {
      $updateFields[] = "description = ?";
      $updateValues[] = $data->description;
    }
    
    if (isset($data->source_fond_id) && $data->source_fond_id !== null) {
      $updateFields[] = "source_fond_id = ?";
      $updateValues[] = $data->source_fond_id;
    }
    
    // Only proceed if there are fields to update
    if (empty($updateFields)) {
      return; // No fields to update
    }
    
    // Add the ID to the end of values array
    $updateValues[] = $id;
    
    $sql = "UPDATE s4_fonds SET " . implode(", ", $updateFields) . " WHERE id = ?";
    $stmt = $db->prepare($sql);
    $stmt->execute($updateValues);
  }

  public static function delete($id)
  {
    $db = getDB();
    $stmt = $db->prepare("DELETE FROM s4_fonds WHERE id = ?");
    $stmt->execute([$id]);
  }
}
