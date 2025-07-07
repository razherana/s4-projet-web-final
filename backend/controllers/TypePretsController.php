<?php

namespace controllers;

use Flight;
use models\TypePrets;
use Utils;

class TypePretsController
{
  public static function getAll()
  {
    try {
      $typePrets = TypePrets::getAll();
      Flight::json($typePrets);
    } catch (\Throwable $th) {
      echo $th->getMessage();
    }
  }

  public static function getById($id)
  {
    $typePrets = TypePrets::getById($id);
    Flight::json($typePrets);
  }

  public static function create()
  {
    $data = Flight::request()->data;
    $id = TypePrets::create($data);
    Flight::json(['message' => 'Type de prêt ajouté', 'id' => $id]);
  }

  public static function update($id)
  {
    $data = Flight::request()->data;
    TypePrets::update($id, $data);
    Flight::json(['message' => 'Type de prêt modifié']);
  }

  public static function delete($id)
  {
    TypePrets::delete($id);
    Flight::json(['message' => 'Type de prêt supprimé']);
  }
}
