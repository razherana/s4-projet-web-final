<?php

namespace controllers;

use Flight;
use models\PretRetourHistoriques;
use Utils;

class PretRetourHistoriquesController
{
  public static function getAll()
  {
    try {
      $pretRetourHistoriques = PretRetourHistoriques::getAll();
      Flight::json($pretRetourHistoriques);
    } catch (\Throwable $th) {
      echo $th->getMessage();
    }
  }

  public static function getById($id)
  {
    $pretRetourHistoriques = PretRetourHistoriques::getById($id);
    Flight::json($pretRetourHistoriques);
  }

  public static function create()
  {
    $data = Flight::request()->data;
    $id = PretRetourHistoriques::create($data);
    Flight::json(['message' => 'Historique de retour ajouté', 'id' => $id]);
  }

  public static function update($id)
  {
    $data = Flight::request()->data;
    PretRetourHistoriques::update($id, $data);
    Flight::json(['message' => 'Historique de retour modifié']);
  }

  public static function delete($id)
  {
    PretRetourHistoriques::delete($id);
    Flight::json(['message' => 'Historique de retour supprimé']);
  }

  public static function getInterets()
  {
    $data = Flight::request()->query;
    $mois1 = $data->mois1 ?? null;
    $annee1 = $data->annee1 ?? null;

    $mois2 = $data->mois2 ?? null;
    $annee2 = $data->annee2 ?? null;

    if (!$mois1 || !$annee1) {
      Flight::json(['error' => 'Mois et année de début requis'], 400);
      return;
    }

    try {
      $interets = PretRetourHistoriques::getInterets($mois1, $annee1, $mois2, $annee2);
      Flight::json($interets);
    } catch (\Throwable $th) {
      Flight::json(['error' => $th->getMessage()], 500);
    }
  }
}
