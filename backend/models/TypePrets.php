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
    $stmt = $db->prepare("INSERT INTO s4_type_prets (nom, taux_interet, duree_min, duree_max) VALUES (?, ?, ?, ?)");
    $stmt->execute([$data->nom, $data->taux_interet, $data->duree_min, $data->duree_max]);
    return $db->lastInsertId();
  }

  public static function update($id, $data)
  {
    $db = getDB();
    $stmt = $db->prepare("UPDATE s4_type_prets SET nom = ?, taux_interet = ?, duree_min = ?, duree_max = ? WHERE id = ?");
    $stmt->execute([$data->nom, $data->taux_interet, $data->duree_min, $data->duree_max, $id]);
  }

  public static function delete($id)
  {
    $db = getDB();
    $stmt = $db->prepare("DELETE FROM s4_type_prets WHERE id = ?");
    $stmt->execute([$id]);
  }
}
