<?php

namespace models;

use DateInterval;
use DateTime;
use PDO;

class Prets
{
  public static function getAll()
  {
    $db = getDB();
    $stmt = $db->query("SELECT s4_prets.*, taux_interet, taux_assurance FROM s4_prets JOIN s4_type_prets ON s4_prets.type_pret_id = s4_type_prets.id");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }

  public static function getById($id)
  {
    $db = getDB();
    $stmt = $db->prepare("SELECT * FROM s4_prets WHERE id = ?");
    $stmt->execute([$id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
  }

  public static function getAllByClient(int $id)
  {
    $db = getDB();
    $stmt = $db->prepare("SELECT * FROM s4_prets WHERE client_id = ?");
    $stmt->execute([$id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
  }

  public static function create($data)
  {
    $db = getDB();
    $stmt = $db->prepare("INSERT INTO s4_prets (client_id, type_pret_id, montant, duree, date_acceptation, date_refus, date_creation, delai) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([$data->client_id, $data->type_pret_id, $data->montant, $data->duree, $data->date_acceptation, $data->date_refus, $data->date_creation, $data->delai ?? 0]);
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
    
    if (isset($data->date_acceptation) && $data->date_acceptation !== null) {
      $updateFields[] = "date_acceptation = ?";
      $updateValues[] = $data->date_acceptation;
    }
    
    if (isset($data->date_refus) && $data->date_refus !== null) {
      $updateFields[] = "date_refus = ?";
      $updateValues[] = $data->date_refus;
    }
    
    if (isset($data->date_creation) && $data->date_creation !== null) {
      $updateFields[] = "date_creation = ?";
      $updateValues[] = $data->date_creation;
    }
    
    // Only proceed if there are fields to update
    if (empty($updateFields)) {
      return; // No fields to update
    }
    
    // Add the ID to the end of values array
    $updateValues[] = $id;
    
    $sql = "UPDATE s4_prets SET " . implode(", ", $updateFields) . " WHERE id = ?";
    $stmt = $db->prepare($sql);
    $stmt->execute($updateValues);
  }

  public static function delete($id)
  {
    $db = getDB();
    $stmt = $db->prepare("DELETE FROM s4_prets WHERE id = ?");
    $stmt->execute([$id]);
  }

  public static function getMontantAPayer($pretId) {
    $pret = self::getById($pretId);
    if (!$pret) {
      return [];
    }
    
    // Get type_pret information
    $db = getDB();
    $stmt = $db->prepare("SELECT * FROM s4_type_prets WHERE id = ?");
    $stmt->execute([$pret['type_pret_id']]);
    $type_pret = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$type_pret) {
      return [];
    }

    $delai = $pret['delai'];

    $dateAcceptation = new \DateTime($pret['date_acceptation']);
    $dateAcceptation->setDate(
      $dateAcceptation->format('Y'), $dateAcceptation->format('m') + $delai, 1
    );

    $result = [];
    $totalMois = $pret['duree']; // Total months for the loan
    
    for ($moisIndex = 0; $moisIndex < $totalMois; $moisIndex++) {
      // Calculate current month and year
      $dateEcheance = clone $dateAcceptation;
      $dateEcheance->setDate($dateEcheance->format('Y'), $dateEcheance->format('m') + $moisIndex, 1);
      // $dateEcheance->add(new \DateInterval('P' . $moisIndex . 'M'));
      
      $anneeActuelle = $dateEcheance->format('Y');
      $moisActuelle = $dateEcheance->format('m');
      
      $montant = self::getMontantAPayerByMoisAnnee($pret, $moisActuelle, $anneeActuelle);
      
      // Check if payment exists for this month and year
      $stmt = $db->prepare("SELECT * FROM s4_pret_retour_historiques WHERE pret_id = ? AND MONTH(date_retour) = ? AND YEAR(date_retour) = ?");
      $stmt->execute([$pretId, $moisActuelle, $anneeActuelle]);
      $payer = $stmt->fetch(PDO::FETCH_ASSOC);
      
      $result[] = [
        'mois_numero' => $moisIndex + 1,
        'annee' => $anneeActuelle,
        'mois' => $moisActuelle,
        'date_echeance' => $dateEcheance->format('Y-m-d'),
        'montant' => $montant,
        'isPayer' => !!$payer
      ];
    }
    
    return $result;
  }

  public static function getMontantAPayerByMoisAnnee($pret, $mois, $annee)
  {
    if (!$pret) {
      return 0;
    }
    
    // Get type_pret information
    $db = getDB();
    $stmt = $db->prepare("SELECT * FROM s4_type_prets WHERE id = ?");
    $stmt->execute([$pret['type_pret_id']]);
    $type_pret = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$type_pret) {
      return 0;
    }

    $dateAcceptation = new \DateTime($pret['date_acceptation']);
    
    // Calculate total months from loan start
    $startDate = new \DateTime($dateAcceptation->format('Y-m-01'));
    $startDate->setDate($startDate->format('Y'), $startDate->format('m') + $pret['delai'], 1);

    $currentDate = new \DateTime("$annee-$mois-01");
    
    $diff = $startDate->diff($currentDate);
    $monthsDiff = ($diff->y * 12) + $diff->m + 1;
    
    // Check if this month is within the loan period
    $totalMonths = $pret['duree'];
    if ($monthsDiff < 1 || $monthsDiff > $totalMonths) {
      return 0;
    }

    // Calculate monthly payment with interest and insurance
    $montantMensuel = $pret['montant'] / $totalMonths;
    $interetMensuel = ($pret['montant'] * $type_pret['taux_interet'] / 100) / $totalMonths;
    $assuranceMensuelle = ($pret['montant'] * ($type_pret['taux_assurance'] ?? 0) / 100) / $totalMonths;

    return $montantMensuel + $interetMensuel + $assuranceMensuelle;
  }

  public static function findByMoisAnnee($mois1, $annee1, $mois2 = null, $annee2 = null) {
    if ($mois2 === null) 
      $mois2 = $mois1;

    if ($annee2 === null)
      $annee2 = $annee1;

    $startDate = $annee1 . '-' . str_pad($mois1, 2, '0', STR_PAD_LEFT) . '-01';
    $endDateBase = $annee2 . '-' . str_pad($mois2, 2, '0', STR_PAD_LEFT) . '-01';

    $db = getDB();
    $sql = "SELECT * FROM s4_prets WHERE date_acceptation >= :startDate AND date_acceptation < DATE_ADD(:endDate, INTERVAL 1 MONTH)";
    $stmt = $db->prepare($sql);
    $stmt->execute(['startDate' => $startDate, 'endDate' => $endDateBase]);
    
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }
}
