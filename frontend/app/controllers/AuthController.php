<?php

declare(strict_types=1);

namespace app\controllers;

use flight\Engine;

class AuthController
{
  /** @var Engine */
  protected Engine $app;

  public function __construct(Engine $app)
  {
    $this->app = $app;
  }

  private function apiCall($endpoint, $method = 'GET', $data = null)
  {
    $config = $this->app->get('config');
    $url = $config['api']['base_url'] . $endpoint;

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);

    if ($data) {
      curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
      curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    }

    $response = curl_exec($ch);
    curl_close($ch);

    return json_decode($response, true);
  }

  public function showLogin()
  {
    // If already logged in, redirect to dashboard
    if (isset($_SESSION['user'])) {
      $this->app->redirect('/admin/dashboard');
      return;
    }

    $content = $this->app->view()->fetch('auth/login', []);

    $this->app->view()->render('auth/layout', [
      'title' => 'Connexion',
      'content' => $content
    ]);
  }

  public function login()
  {
    $email = $_POST['email'] ?? null;
    $password = $_POST['password'] ?? null;

    if (!$email || !$password) {
      $this->app->redirect('/login?error=missing_credentials');
      return;
    }

    try {
      $result = $this->apiCall('/auth/login', 'POST', [
        'email' => $email,
        'password' => $password
      ]);

      if (isset($result['error'])) {
        $this->app->redirect('/login?error=invalid_credentials');
        return;
      }

      // Store user session
      
      $_SESSION['user'] = $result['user'];
      $_SESSION['token'] = $result['token'];

      // Redirect based on role
      if ($result['user']['user_id'] === 1) {
        $this->app->redirect('/admin/dashboard');
      } else {
        $this->app->redirect('/client/dashboard');
      }

    } catch (\Exception $e) {
      $this->app->redirect('/login?error=server_error');
    }
  }

  public function logout()
  {
    
    session_destroy();
    $this->app->redirect('/login?success=logged_out');
  }

  public static function requireAuth()
  {
    
    if (!isset($_SESSION['user'])) {
      header('Location: /login');
      exit;
    }
  }

  public static function requireAdmin()
  {
    
    if (!isset($_SESSION['user']) || $_SESSION['user']['user_id'] !== 1) {
      header('Location: /login');
      exit;
    }
  }
}
