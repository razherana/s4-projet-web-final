<?php

namespace models;

use PDO;

class Prets
{
  public static function getAll()
  {
    $db = getDB();
    $stmt = $db->query("SELECT * FROM s4_prets");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }

  public static function getById($id)
  {
    $db = getDB();
    $stmt = $db->prepare("SELECT * FROM s4_prets WHERE id = ?");
    $stmt->execute([$id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
  }

  public static function create($data)
  {
    $db = getDB();
    $stmt = $db->prepare("INSERT INTO s4_prets (client_id, type_pret_id, montant, duree, date_acceptation, date_refus, date_creation) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([$data->client_id, $data->type_pret_id, $data->montant, $data->duree, $data->date_acceptation, $data->date_refus, $data->date_creation]);
    return $db->lastInsertId();
  }

  public static function update($id, $data)
  {
    $db = getDB();
    $stmt = $db->prepare("UPDATE s4_prets SET client_id = ?, type_pret_id = ?, montant = ?, duree = ?, date_acceptation = ?, date_refus = ?, date_creation = ? WHERE id = ?");
    $stmt->execute([$data->client_id, $data->type_pret_id, $data->montant, $data->duree, $data->date_acceptation, $data->date_refus, $data->date_creation, $id]);
  }

  public static function delete($id)
  {
    $db = getDB();
    $stmt = $db->prepare("DELETE FROM s4_prets WHERE id = ?");
    $stmt->execute([$id]);
  }
}
