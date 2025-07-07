<?php

declare(strict_types=1);

namespace app\controllers;

use flight\Engine;

class FondsController
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
    $fonds = $this->apiCall('/fonds');
    $sourceFonds = $this->apiCall('/source-fonds');

    $content = $this->app->view()->fetch('fonds/list', [
      'fonds' => $fonds,
      'sourceFonds' => $sourceFonds
    ]);

    $this->app->render('layout', [
      'title' => 'Gestion des Fonds',
      'content' => $content
    ]);
  }

  public function create()
  {
    $sourceFonds = $this->apiCall('/source-fonds');

    $content = $this->app->view()->fetch('fonds/create', [
      'sourceFonds' => $sourceFonds
    ]);

    $this->app->render('layout', [
      'title' => 'Créer un Fond',
      'content' => $content
    ]);
  }

  public function store()
  {
    $request = $this->app->request();
    $input = file_get_contents('php://input');
    $data = json_decode($input, true);

    $result = $this->apiCall('/fonds', 'POST', $data);
    $this->app->json($result);
  }

  public function edit($id)
  {
    $fond = $this->apiCall('/fonds/' . $id);
    $sourceFonds = $this->apiCall('/source-fonds');

    $content = $this->app->view()->fetch('fonds/edit', [
      'fond' => $fond,
      'sourceFonds' => $sourceFonds
    ]);

    $this->app->render('layout', [
      'title' => 'Modifier le Fond',
      'content' => $content
    ]);
  }

  public function update($id)
  {
    $request = $this->app->request();
    $input = file_get_contents('php://input');
    $data = json_decode($input, true);

    $result = $this->apiCall('/fonds/' . $id, 'PUT', $data);
    $this->app->json($result);
  }

  public function delete($id)
  {
    $result = $this->apiCall('/fonds/' . $id, 'DELETE');
    $this->app->json($result);
  }
}
