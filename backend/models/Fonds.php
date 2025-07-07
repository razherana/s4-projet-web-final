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
    $stmt = $db->prepare("UPDATE s4_fonds SET montant_initial = ?, description = ?, source_fond_id = ? WHERE id = ?");
    $stmt->execute([$data->montant_initial, $data->description, $data->source_fond_id, $id]);
  }

  public static function delete($id)
  {
    $db = getDB();
    $stmt = $db->prepare("DELETE FROM s4_fonds WHERE id = ?");
    $stmt->execute([$id]);
  }
}
