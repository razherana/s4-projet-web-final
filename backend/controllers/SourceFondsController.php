<?php

namespace controllers;

use Flight;
use models\SourceFonds;
use Utils;

class SourceFondsController
{
  public static function getAll()
  {
    try {
      $sourceFonds = SourceFonds::getAll();
      Flight::json($sourceFonds);
    } catch (\Throwable $th) {
      echo $th->getMessage();
    }
  }

  public static function getById($id)
  {
    $sourceFonds = SourceFonds::getById($id);
    Flight::json($sourceFonds);
  }

  public static function create()
  {
    $data = Flight::request()->data;
    $id = SourceFonds::create($data);
    Flight::json(['message' => 'Source de fonds ajoutée', 'id' => $id]);
  }

  public static function update($id)
  {
    $data = Flight::request()->data;
    SourceFonds::update($id, $data);
    Flight::json(['message' => 'Source de fonds modifiée']);
  }

  public static function delete($id)
  {
    SourceFonds::delete($id);
    Flight::json(['message' => 'Source de fonds supprimée']);
  }
}
