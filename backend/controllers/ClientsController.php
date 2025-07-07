<?php

namespace controllers;

use Flight;
use models\Clients;
use Utils;

class ClientsController
{
  public static function getAll()
  {
    try {
      $clients = Clients::getAll();
      Flight::json($clients);
    } catch (\Throwable $th) {
      echo $th->getMessage();
    }
  }

  public static function getById($id)
  {
    $clients = Clients::getById($id);
    Flight::json($clients);
  }

  public static function create()
  {
    $data = Flight::request()->data;
    $id = Clients::create($data);
    Flight::json(['message' => 'Client ajouté', 'id' => $id]);
  }

  public static function update($id)
  {
    $data = Flight::request()->data;
    Clients::update($id, $data);
    Flight::json(['message' => 'Client modifié']);
  }

  public static function delete($id)
  {
    Clients::delete($id);
    Flight::json(['message' => 'Client supprimé']);
  }
}
