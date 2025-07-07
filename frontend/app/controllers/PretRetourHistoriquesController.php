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

  public function create()
  {
    $prets = $this->apiCall('/prets');

    $content = $this->app->view()->fetch('pret_retour_historiques/create', [
      'prets' => $prets
    ]);

    $this->app->render('layout', [
      'title' => 'Créer un Historique de Retour',
      'content' => $content
    ]);
  }

  public function store()
  {
    $data = $this->app->request()->data->getData();
    $result = $this->apiCall('/pret-retour-historiques', 'POST', $data);

    $this->app->redirect('/pret-retour-historiques');
  }

  public function edit($id)
  {
    $pretRetourHistorique = $this->apiCall('/pret-retour-historiques/' . $id);
    $prets = $this->apiCall('/prets');

    $content = $this->app->view()->fetch('pret_retour_historiques/edit', [
      'pretRetourHistorique' => $pretRetourHistorique,
      'prets' => $prets
    ]);

    $this->app->render('layout', [
      'title' => 'Modifier l\'Historique de Retour',
      'content' => $content
    ]);
  }

  public function update($id)
  {
    $data = $this->app->request()->data->getData();
    $result = $this->apiCall('/pret-retour-historiques/' . $id, 'PUT', $data);

    $this->app->redirect('/pret-retour-historiques');
  }

  public function delete($id)
  {
    $result = $this->apiCall('/pret-retour-historiques/' . $id, 'DELETE');
    $this->app->json($result);
  }
}
