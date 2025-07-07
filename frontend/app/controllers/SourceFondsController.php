<?php

declare(strict_types=1);

namespace app\controllers;

use flight\Engine;

class SourceFondsController
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
    $sourceFonds = $this->apiCall('/source-fonds');

    $content = $this->app->view()->fetch('source_fonds/list', [
      'sourceFonds' => $sourceFonds
    ]);

    $this->app->render('layout', [
      'title' => 'Gestion des Sources de Fonds',
      'content' => $content
    ]);
  }

  public function create()
  {
    $content = $this->app->view()->fetch('source_fonds/create');

    $this->app->render('layout', [
      'title' => 'Créer une Source de Fonds',
      'content' => $content
    ]);
  }

  public function store()
  {
    $input = file_get_contents('php://input');
    $data = json_decode($input, true);

    $result = $this->apiCall('/source-fonds', 'POST', $data);
    $this->app->json($result);
  }

  public function edit($id)
  {
    $sourceFond = $this->apiCall('/source-fonds/' . $id);

    $content = $this->app->view()->fetch('source_fonds/edit', [
      'sourceFond' => $sourceFond
    ]);

    $this->app->render('layout', [
      'title' => 'Modifier la Source de Fonds',
      'content' => $content
    ]);
  }

  public function update($id)
  {
    $input = file_get_contents('php://input');
    $data = json_decode($input, true);

    $result = $this->apiCall('/source-fonds/' . $id, 'PUT', $data);
    $this->app->json($result);
  }

  public function delete($id)
  {
    $result = $this->apiCall('/source-fonds/' . $id, 'DELETE');
    $this->app->json($result);
  }
}
