<?php

declare(strict_types=1);

namespace app\controllers;

use flight\Engine;

class UsersController
{
  /** @var Engine */
  protected Engine $app;

  /**
   * Constructor
   */
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

  public function list()
  {
    $users = $this->apiCall('/users');

    $content = $this->app->view()->fetch('users/list', [
      'users' => $users
    ]);

    $this->app->render('layout', [
      'title' => 'Gestion des Utilisateurs',
      'content' => $content
    ]);
  }

  public function create()
  {
    $content = $this->app->view()->fetch('users/create');

    $this->app->render('layout', [
      'title' => 'Créer un Utilisateur',
      'content' => $content
    ]);
  }

  public function store()
  {
    $data = $this->app->request()->data->getData();
    $result = $this->apiCall('/users', 'POST', $data);

    $this->app->redirect('/users');
  }

  public function edit($id)
  {
    $user = $this->apiCall('/users/' . $id);

    $content = $this->app->view()->fetch('users/edit', [
      'user' => $user
    ]);

    $this->app->render('layout', [
      'title' => 'Modifier l\'Utilisateur',
      'content' => $content
    ]);
  }

  public function update($id)
  {
    $data = $this->app->request()->data->getData();
    $result = $this->apiCall('/users/' . $id, 'PUT', $data);

    $this->app->redirect('/users');
  }

  public function delete($id)
  {
    $result = $this->apiCall('/users/' . $id, 'DELETE');
    $this->app->json($result);
  }
}
