<?php

declare(strict_types=1);

namespace app\controllers;

use flight\Engine;

class PretRetourHistoriquesController
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
    $pretRetourHistoriques = $this->apiCall('/pret-retour-historiques');
    $prets = $this->apiCall('/prets');

    $content = $this->app->view()->fetch('pret_retour_historiques/list', [
      'pretRetourHistoriques' => $pretRetourHistoriques,
      'prets' => $prets
    ]);

    $this->app->render('layout', [
      'title' => 'Gestion des Historiques de Retour',
      'content' => $content
    ]);
  }

  public function interets()
  {
    $config = $this->app->get('config');
    
    $content = $this->app->view()->fetch('pret_retour_historiques/interets', [
      'config' => $config
    ]);

    $this->app->render('layout', [
      'title' => 'Calcul des Intérêts',
      'content' => $content
    ]);
  }

  public function store()
  {
    $input = file_get_contents('php://input');
    $data = json_decode($input, true);
    
    $result = $this->apiCall('/pret-retour-historiques', 'POST', $data);
    $this->app->json($result);
  }

  public function update($id)
  {
    $input = file_get_contents('php://input');
    $data = json_decode($input, true);
    
    $result = $this->apiCall('/pret-retour-historiques/' . $id, 'PUT', $data);
    $this->app->json($result);
  }

  public function delete($id)
  {
    $result = $this->apiCall('/pret-retour-historiques/' . $id, 'DELETE');
    $this->app->json($result);
  }
}
