<?php

declare(strict_types=1);

namespace app\controllers;

use flight\Engine;

class ClientsController
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
    $clients = $this->apiCall('/clients');
    $users = $this->apiCall('/users');

    $content = $this->app->view()->fetch('clients/list', [
      'clients' => $clients,
      'users' => $users
    ]);

    $this->app->render('layout', [
      'title' => 'Gestion des Clients',
      'content' => $content
    ]);
  }

  public function create()
  {
    $users = $this->apiCall('/users');

    $content = $this->app->view()->fetch('clients/create', [
      'users' => $users
    ]);

    $this->app->render('layout', [
      'title' => 'Créer un Client',
      'content' => $content
    ]);
  }

  public function store()
  {
    $data = $this->app->request()->data->getData();
    $result = $this->apiCall('/clients', 'POST', $data);

    $this->app->redirect('/clients');
  }

  public function edit($id)
  {
    $client = $this->apiCall('/clients/' . $id);
    $users = $this->apiCall('/users');

    $content = $this->app->view()->fetch('clients/edit', [
      'client' => $client,
      'users' => $users
    ]);

    $this->app->render('layout', [
      'title' => 'Modifier le Client',
      'content' => $content
    ]);
  }

  public function update($id)
  {
    $data = $this->app->request()->data->getData();
    $result = $this->apiCall('/clients/' . $id, 'PUT', $data);

    $this->app->redirect('/clients');
  }

  public function delete($id)
  {
    $result = $this->apiCall('/clients/' . $id, 'DELETE');
    $this->app->json($result);
  }
}
