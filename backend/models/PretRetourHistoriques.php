<?php

namespace models;

use DateTime;
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

  public static function getByPretAndMoisAnnee($pretId, $mois1, $annee1, $mois2 = null, $annee2 = null)
  {
    if ($mois2 === null) {
      $mois2 = $mois1;
    }

    if ($annee2 === null) {
      $annee2 = $annee1;
    }

    $startDate = $annee1 . '-' . str_pad($mois1, 2, '0', STR_PAD_LEFT) . '-01';

    $endDateBase = $annee2 . '-' . str_pad($mois2, 2, '0', STR_PAD_LEFT) . '-01';


    $sql = "SELECT *
            FROM s4_pret_retour_historiques
            WHERE pret_id = :id
            AND date_retour 
            BETWEEN :start_date AND LAST_DAY(:end_date_base)
            ORDER BY date_retour ASC
            ";

    $db = getDB();

    $stmt = $db->prepare($sql);

    // Bind the parameters
    $stmt->bindParam(':id', $pretId);
    $stmt->bindParam(':start_date', $startDate);
    $stmt->bindParam(':end_date_base', $endDateBase);

    // Execute the query
    $stmt->execute();

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }

  public static function getByMoisAnnee($mois1, $annee1, $mois2 = null, $annee2 = null)
  {
    if ($mois2 === null) {
      $mois2 = $mois1;
    }

    if ($annee2 === null) {
      $annee2 = $annee1;
    }

    $startDate = $annee1 . '-' . str_pad($mois1, 2, '0', STR_PAD_LEFT) . '-01';

    $endDateBase = $annee2 . '-' . str_pad($mois2 + 1, 2, '0', STR_PAD_LEFT) . '-01';


    $sql = "SELECT s4_pret_retour_historiques.*, s4_prets.montant AS montant_initial, s4_prets.id AS pret_id, s4_prets.duree AS pret_duree
            FROM s4_pret_retour_historiques
            JOIN s4_prets ON s4_pret_retour_historiques.pret_id = s4_prets.id
            WHERE date_retour 
            BETWEEN :start_date AND LAST_DAY(:end_date_base)
            ORDER BY date_retour ASC
            ";

    $db = getDB();

    $stmt = $db->prepare($sql);

    // Bind the parameters
    $stmt->bindParam(':start_date', $startDate);
    $stmt->bindParam(':end_date_base', $endDateBase);

    // Execute the query
    $stmt->execute();

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }

  public static function getInteretByPret($pret, $mois1, $annee1, $mois2 = null, $annee2 = null)
  {
    $montant = $pret['montant_initial'] / $pret['duree'];
    $interet = 0;
    $retours = self::getByPretAndMoisAnnee($pret['id'], $mois1, $annee1, $mois2, $annee2);

    foreach ($retours as $retour)
      $interet += $retour['montant'] - $montant;

    return $interet;
  }

  public static function getInterets($mois1, $annee1, $mois2 = null, $annee2 = null)
  {
    $interet = [];
    $retours = self::getByMoisAnnee($mois1, $annee1, $mois2, $annee2);

    foreach ($retours as $retour) {
      $montant = $retour['montant_initial'] / $retour['pret_duree'];
      $dateKey = (new DateTime($retour['date_retour']))->format("Y-m");
      
      if (!isset($interet[$dateKey])) {
        $interet[$dateKey] = 0;
      }
      
      $interet[$dateKey] += $retour['montant'] - $montant;
    }

    $startDate = new DateTime("$annee1-$mois1-01");
    $endDate = ($mois2 && $annee2) ? new DateTime("$annee2-$mois2-01") : clone $startDate;

    $completeInteret = [];
    $currentDate = clone $startDate;

    while ($currentDate <= $endDate) {
      $key = $currentDate->format("Y-m");
      $completeInteret[$key] = $interet[$key] ?? 0;
      $currentDate->modify('+1 month');
    }

    return $completeInteret;
  }

  public static function isPayer($PretId, $mois, $annee){
    $db = getDB();
    $stmt = $db->prepare("SELECT * FROM s4_pret_retour_historiques WHERE pret_id= ? AND MONTH(FROM_UNIXTIME(date_retour)) = ? AND YEAR(FROM_UNIXTIME(date_retour)) = ?");
    $stmt->execute([$PretId, $mois, $annee]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
  }

  public static function isPayerAnnuel($pretId, $annee) {
    $db = getDB();
    $stmt = $db->prepare("SELECT * FROM s4_pret_retour_historiques WHERE pret_id = ? AND YEAR(FROM_UNIXTIME(date_retour)) = ?");
    $stmt->execute([$pretId, $annee]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
  }

  public static function getLastPaymentYear($pretId) {
    $db = getDB();
    $stmt = $db->prepare("
      SELECT 
        COUNT(*) as payments_made,
        MAX(YEAR(date_retour)) as last_year 
      FROM s4_pret_retour_historiques 
      WHERE pret_id = ?
    ");
    $stmt->execute([$pretId]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
  }

  public static function getPaymentsByLoan($pretId) {
    $db = getDB();
    $stmt = $db->prepare("
      SELECT 
        *,
        YEAR(date_retour) as payment_year
      FROM s4_pret_retour_historiques 
      WHERE pret_id = ?
      ORDER BY date_retour ASC
    ");
    $stmt->execute([$pretId]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }
}
