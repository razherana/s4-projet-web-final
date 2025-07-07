<?php

namespace models;

use PDO;

class PretRetourHistoriques
{
  public static function getAll()
  {
    $db = getDB();
    $stmt = $db->query("SELECT * FROM s4_pret_retour_historiques");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }

  public static function getById($id)
  {
    $db = getDB();
    $stmt = $db->prepare("SELECT * FROM s4_pret_retour_historiques WHERE id = ?");
    $stmt->execute([$id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
  }

  public static function create($data)
  {
    $db = getDB();
    $stmt = $db->prepare("INSERT INTO s4_pret_retour_historiques (pret_id, montant, date_retour) VALUES (?, ?, ?)");
    $stmt->execute([$data->pret_id, $data->montant, $data->date_retour]);
    return $db->lastInsertId();
  }

  public static function update($id, $data)
  {
    $db = getDB();
    $stmt = $db->prepare("UPDATE s4_pret_retour_historiques SET pret_id = ?, montant = ?, date_retour = ? WHERE id = ?");
    $stmt->execute([$data->pret_id, $data->montant, $data->date_retour, $id]);
  }

  public static function delete($id)
  {
    $db = getDB();
    $stmt = $db->prepare("DELETE FROM s4_pret_retour_historiques WHERE id = ?");
    $stmt->execute([$id]);
  }
}
