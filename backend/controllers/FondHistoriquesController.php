<?php

namespace controllers;

use Flight;
use models\FondHistoriques;
use Utils;

class FondHistoriquesController
{
  public static function getAll()
  {
    try {
      $fondHistoriques = FondHistoriques::getAll();
      Flight::json($fondHistoriques);
    } catch (\Throwable $th) {
      echo $th->getMessage();
    }
  }

  public static function getById($id)
  {
    $fondHistoriques = FondHistoriques::getById($id);
    Flight::json($fondHistoriques);
  }

  public static function create()
  {
    $data = Flight::request()->data;
    $id = FondHistoriques::create($data);
    Flight::json(['message' => 'Historique de fond ajouté', 'id' => $id]);
  }

  public static function update($id)
  {
    $data = Flight::request()->data;
    FondHistoriques::update($id, $data);
    Flight::json(['message' => 'Historique de fond modifié']);
  }

  public static function delete($id)
  {
    FondHistoriques::delete($id);
    Flight::json(['message' => 'Historique de fond supprimé']);
  }
}
