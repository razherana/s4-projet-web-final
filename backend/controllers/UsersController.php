<?php

namespace controllers;

use Flight;
use models\Users;
use Utils;

class UsersController
{
  public static function getAll()
  {
    try {
      $users = Users::getAll();
      Flight::json($users);
    } catch (\Throwable $th) {
      echo $th->getMessage();
    }
  }

  public static function getById($id)
  {
    $users = Users::getById($id);
    Flight::json($users);
  }

  public static function create()
  {
    $data = Flight::request()->data;
    $id = Users::create($data);
    Flight::json(['message' => 'Utilisateur ajouté', 'id' => $id]);
  }

  public static function update($id)
  {
    $data = Flight::request()->data;
    Users::update($id, $data);
    Flight::json(['message' => 'Utilisateur modifié']);
  }

  public static function delete($id)
  {
    Users::delete($id);
    Flight::json(['message' => 'Utilisateur supprimé']);
  }
}
