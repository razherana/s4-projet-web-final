<?php

namespace controllers;

use Flight;
use models\Fonds;
use Utils;

class FondsController
{
  public static function getAll()
  {
    try {
      $fonds = Fonds::getAll();
      Flight::json($fonds);
    } catch (\Throwable $th) {
      echo $th->getMessage();
    }
  }

  public static function getById($id)
  {
    $fonds = Fonds::getById($id);
    Flight::json($fonds);
  }

  public static function create()
  {
    $data = Flight::request()->data;
    $id = Fonds::create($data);
    Flight::json(['message' => 'Fonds ajouté', 'id' => $id]);
  }

  public static function update($id)
  {
    $data = Flight::request()->data;
    Fonds::update($id, $data);
    Flight::json(['message' => 'Fonds modifié']);
  }

  public static function delete($id)
  {
    Fonds::delete($id);
    Flight::json(['message' => 'Fonds supprimé']);
  }
}
