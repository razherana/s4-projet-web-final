<?php

namespace controllers;

use Flight;
use models\Simulations;
use Utils;

class SimulationsController
{
  public static function getAll()
  {
    try {
      $simulations = Simulations::getAll();
      Flight::json($simulations);
    } catch (\Throwable $th) {
      echo $th->getMessage();
    }
  }

  public static function getById($id)
  {
    $simulation = Simulations::getById($id);
    Flight::json($simulation);
  }

  public static function getByClient($clientId)
  {
    try {
      $simulations = Simulations::getAllByClient($clientId);
      Flight::json($simulations);
    } catch (\Throwable $th) {
      echo $th->getMessage();
    }
  }

  public static function create()
  {
    $data = Flight::request()->data;
    $id = Simulations::create($data);
    Flight::json(['message' => 'Simulation ajoutée', 'id' => $id]);
  }

  public static function update($id)
  {
    $data = Flight::request()->data;
    Simulations::update($id, $data);
    Flight::json(['message' => 'Simulation modifiée']);
  }

  public static function delete($id)
  {
    Simulations::delete($id);
    Flight::json(['message' => 'Simulation supprimée']);
  }

  public static function deleteByClient($clientId)
  {
    Simulations::deleteByClient($clientId);
    Flight::json(['message' => 'Simulations du client supprimées']);
  }
}
