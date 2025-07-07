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
    $stmt = $db->prepare("INSERT INTO s4_prets (client_id, type_pret_id, montant, duree, date_acceptation, date_refus, date_creation) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([$data->client_id, $data->type_pret_id, $data->montant, $data->duree, $data->date_acceptation, $data->date_refus, $data->date_creation]);
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

  public static function getMontantAPayerByMoisAnnee($pretId, $mois, $annee)
  {
    $pret = self::getById($pretId);
    $type_pret = TypePrets::getById($pret['type_pret_id']);

    if (!$pret || !$type_pret)
      return null;

    $dateAcceptation = new \DateTime($pret['date_acceptation']);
    
    // Calculate the year number from loan start (1-based)
    $anneeDuPret = $annee - $dateAcceptation->format('Y') + 1;
    
    // Check if this year is within the loan period
    if ($anneeDuPret < 1 || $anneeDuPret > $pret['duree']) {
      return 0;
    }

    // Calculate annual payment with interest and insurance
    $montantAnnuel = $pret['montant'] / $pret['duree'];
    $interetAnnuel = ($pret['montant'] * $type_pret['taux_interet'] / 100);
    $assuranceAnnuelle = ($pret['montant'] * ($type_pret['taux_assurance'] ?? 0) / 100);

    return $montantAnnuel + $interetAnnuel + $assuranceAnnuelle;
  }

  public static function getMontantAPayer($pretId) {
    $pret = self::getById($pretId);
    $type_pret = TypePrets::getById($pret['type_pret_id']);

    if (!$pret || !$type_pret)
      return null;

    $dateAcceptation = new \DateTime($pret['date_acceptation']);
    $anneeDebut = $dateAcceptation->format('Y');

    $result = [];
    
    for ($anneeIndex = 0; $anneeIndex < $pret['duree']; $anneeIndex++) {
      $anneeActuelle = $anneeDebut + $anneeIndex;
      
      // Create payment due date (anniversary of loan acceptance)
      $dateEcheance = clone $dateAcceptation;
      $dateEcheance->setDate($anneeActuelle, $dateAcceptation->format('m'), $dateAcceptation->format('d'));
      
      $montant = self::getMontantAPayerByMoisAnnee($pretId, $dateAcceptation->format('m'), $anneeActuelle);
      $payer = PretRetourHistoriques::isPayerAnnuel($pretId, $anneeActuelle);
      
      $result[] = [
        'annee_numero' => $anneeIndex + 1,
        'annee' => $anneeActuelle,
        'date_echeance' => $dateEcheance->format('Y-m-d'),
        'montant' => $montant,
        'isPayer' => !!$payer
      ];
    }
    
    return $result;
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
