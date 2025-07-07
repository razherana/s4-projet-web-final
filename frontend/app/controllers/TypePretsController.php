<?php

declare(strict_types=1);

namespace app\controllers;

use flight\Engine;

class TypePretsController
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
    $typePrets = $this->apiCall('/type-prets');

    $content = $this->app->view()->fetch('type_prets/list', [
      'typePrets' => $typePrets
    ]);

    $this->app->render('layout', [
      'title' => 'Gestion des Types de Prêts',
      'content' => $content
    ]);
  }

  public function create()
  {
    $content = $this->app->view()->fetch('type_prets/create');

    $this->app->render('layout', [
      'title' => 'Créer un Type de Prêt',
      'content' => $content
    ]);
  }

  public function store()
  {
    $data = $this->app->request()->data->getData();
    $result = $this->apiCall('/type-prets', 'POST', $data);

    $this->app->redirect('/type-prets');
  }

  public function edit($id)
  {
    $typePret = $this->apiCall('/type-prets/' . $id);

    $content = $this->app->view()->fetch('type_prets/edit', [
      'typePret' => $typePret
    ]);

    $this->app->render('layout', [
      'title' => 'Modifier le Type de Prêt',
      'content' => $content
    ]);
  }

  public function update($id)
  {
    $data = $this->app->request()->data->getData();
    $result = $this->apiCall('/type-prets/' . $id, 'PUT', $data);

    $this->app->redirect('/type-prets');
  }

  public function delete($id)
  {
    $result = $this->apiCall('/type-prets/' . $id, 'DELETE');
    $this->app->json($result);
  }
}
