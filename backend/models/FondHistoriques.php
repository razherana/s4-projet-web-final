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
    $stmt = $db->prepare("UPDATE s4_fond_historiques SET fond_id = ?, description = ?, montant = ?, est_sortie = ? WHERE id = ?");
    $stmt->execute([$data->fond_id, $data->description, $data->montant, $data->est_sortie, $id]);
  }

  public static function delete($id)
  {
    $db = getDB();
    $stmt = $db->prepare("DELETE FROM s4_fond_historiques WHERE id = ?");
    $stmt->execute([$id]);
  }
}
