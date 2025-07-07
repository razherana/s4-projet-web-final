<?php

declare(strict_types=1);

namespace app\controllers;

use flight\Engine;

class PretsController
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
    $prets = $this->apiCall('/prets');
    $clients = $this->apiCall('/clients');
    $typePrets = $this->apiCall('/type-prets');

    $content = $this->app->view()->fetch('prets/list', [
      'prets' => $prets,
      'clients' => $clients,
      'typePrets' => $typePrets
    ]);

    $this->app->render('layout', [
      'title' => 'Gestion des Prêts',
      'content' => $content
    ]);
  }

  public function create()
  {
    $clients = $this->apiCall('/clients');
    $typePrets = $this->apiCall('/type-prets');

    $content = $this->app->view()->fetch('prets/create', [
      'clients' => $clients,
      'typePrets' => $typePrets
    ]);

    $this->app->render('layout', [
      'title' => 'Créer un Prêt',
      'content' => $content
    ]);
  }

  public function store()
  {
    $data = $this->app->request()->data->getData();
    $result = $this->apiCall('/prets', 'POST', $data);

    $this->app->redirect('/prets');
  }

  public function edit($id)
  {
    $pret = $this->apiCall('/prets/' . $id);
    $clients = $this->apiCall('/clients');
    $typePrets = $this->apiCall('/type-prets');

    $content = $this->app->view()->fetch('prets/edit', [
      'pret' => $pret,
      'clients' => $clients,
      'typePrets' => $typePrets
    ]);

    $this->app->render('layout', [
      'title' => 'Modifier le Prêt',
      'content' => $content
    ]);
  }

  public function update($id)
  {
    $data = $this->app->request()->data->getData();
    $result = $this->apiCall('/prets/' . $id, 'PUT', $data);

    $this->app->redirect('/prets');
  }

  public function delete($id)
  {
    $result = $this->apiCall('/prets/' . $id, 'DELETE');
    $this->app->json($result);
  }
}
