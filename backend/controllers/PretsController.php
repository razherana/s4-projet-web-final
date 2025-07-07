<?php

namespace controllers;

use Flight;
use models\Prets;
use Utils;

class PretsController
{
  public static function getAll()
  {
    try {
      $prets = Prets::getAll();
      Flight::json($prets);
    } catch (\Throwable $th) {
      echo $th->getMessage();
    }
  }

  public static function getAllByClient(int $id) {
    try {
      $prets = Prets::getAllByClient($id);
      Flight::json($prets);
    } catch (\Throwable $th) {
      echo $th->getMessage();
    }
  }

  public static function getById($id)
  {
    $prets = Prets::getById($id);
    Flight::json($prets);
  }

  public static function create()
  {
    $data = Flight::request()->data;
    $id = Prets::create($data);
    Flight::json(['message' => 'Prêt ajouté', 'id' => $id]);
  }

  public static function update($id)
  {
    $data = Flight::request()->data;
    Prets::update($id, $data);
    Flight::json(['message' => 'Prêt modifié']);
  }

  public static function delete($id)
  {
    Prets::delete($id);
    Flight::json(['message' => 'Prêt supprimé']);
  }

  public static function getPaymentSchedule($id)
  {
    try {
      $schedule = Prets::getMontantAPayer($id);
      Flight::json($schedule);
    } catch (\Throwable $th) {
      Flight::json(['error' => $th->getMessage()], 500);
    }
  }
}
