<?php

declare(strict_types=1);

namespace app\controllers;

use flight\Engine;

class FondHistoriquesController
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
    $fondHistoriques = $this->apiCall('/fond-historiques');
    $fonds = $this->apiCall('/fonds');

    $content = $this->app->view()->fetch('fond_historiques/list', [
      'fondHistoriques' => $fondHistoriques,
      'fonds' => $fonds
    ]);

    $this->app->render('layout', [
      'title' => 'Gestion des Historiques de Fonds',
      'content' => $content
    ]);
  }

  public function create()
  {
    $fonds = $this->apiCall('/fonds');

    $content = $this->app->view()->fetch('fond_historiques/create', [
      'fonds' => $fonds
    ]);

    $this->app->render('layout', [
      'title' => 'Créer un Historique de Fond',
      'content' => $content
    ]);
  }

  public function store()
  {
    $data = $this->app->request()->data->getData();
    $result = $this->apiCall('/fond-historiques', 'POST', $data);

    $this->app->redirect('/fond-historiques');
  }

  public function edit($id)
  {
    $fondHistorique = $this->apiCall('/fond-historiques/' . $id);
    $fonds = $this->apiCall('/fonds');

    $content = $this->app->view()->fetch('fond_historiques/edit', [
      'fondHistorique' => $fondHistorique,
      'fonds' => $fonds
    ]);

    $this->app->render('layout', [
      'title' => 'Modifier l\'Historique de Fond',
      'content' => $content
    ]);
  }

  public function update($id)
  {
    $data = $this->app->request()->data->getData();
    $result = $this->apiCall('/fond-historiques/' . $id, 'PUT', $data);

    $this->app->redirect('/fond-historiques');
  }

  public function delete($id)
  {
    $result = $this->apiCall('/fond-historiques/' . $id, 'DELETE');
    $this->app->json($result);
  }
}
