<?php

namespace models;

use helpers\AuthTokenManager;
use PDO;

class Auth
{
  public static function authenticate($email, $password)
  {
    $db = getDB();
    
    // Try to find user in clients table first
    $stmt = $db->prepare("SELECT s4_clients.*, s4_users.nom AS role FROM s4_clients JOIN s4_users ON s4_users.id = user_id WHERE email = ?");
    $stmt->execute([$email]);
    $client = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($client && $client['password'] === $password) {
      return [
        'id' => $client['id'],
        'email' => $client['email'],
        'nom' => $client['nom'],
        'prenom' => $client['prenom'],
        'user_id' => $client['user_id'],
        'role' => $client['role']
      ];
    }
    
    return false;
  }
  
  public static function generateToken($user)
  {
    return AuthTokenManager::generateToken($user);
  }
  
  public static function validateToken($token)
  {
    try {
      $payload = AuthTokenManager::validateToken($token);
      return $payload;
    } catch (\Exception $e) {
      return false;
    }
  }
}
