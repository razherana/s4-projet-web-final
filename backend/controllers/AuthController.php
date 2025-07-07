<?php

namespace controllers;

use Flight;
use models\Auth;

class AuthController
{
  public static function test() {
    return "test";
  }

  public static function login()
  {
    $data = Flight::request()->data;
    
    if (!isset($data->email) || !isset($data->password)) {
      Flight::json(['error' => 'Email et mot de passe requis'], 400);
      return;
    }
    
    $user = Auth::authenticate($data->email, $data->password);

    if (!$user) {
      Flight::json(['error' => 'Identifiants invalides'], 401);
      return;
    }
    
    $token = Auth::generateToken($user);
    
    Flight::json([
      'message' => 'Connexion réussie',
      'token' => $token,
      'user' => $user
    ]);
  }
  
  public static function verify()
  {
    $headers = getallheaders();
    $authHeader = $headers['Authorization'] ?? '';
    
    if (!$authHeader || !str_starts_with($authHeader, 'Bearer ')) {
      Flight::json(['error' => 'Token manquant'], 401);
      return;
    }
    
    $token = substr($authHeader, 7);
    $payload = Auth::validateToken($token);
    
    if (!$payload) {
      Flight::json(['error' => 'Token invalide'], 401);
      return;
    }
    
    Flight::json(['valid' => true, 'user' => $payload]);
  }
  
  public static function logout()
  {
    Flight::json(['message' => 'Déconnexion réussie']);
  }
}
