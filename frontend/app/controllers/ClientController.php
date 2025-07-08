<?php

declare(strict_types=1);

namespace app\controllers;

use DateTime;
use flight\Engine;

class ClientController
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

  public function dashboard()
  {
    $currentUser = $_SESSION['user'];
    $clientId = $currentUser['id'];
    
    // Get client's loans
    $allPrets = $this->apiCall('/prets');
    $clientPrets = array_filter($allPrets ?? [], function($pret) use ($clientId) {
      return $pret['client_id'] == $clientId;
    });
    
    // Calculate metrics
    $totalPrets = count($clientPrets);
    $activePrets = count(array_filter($clientPrets, function($pret) {
      return !empty($pret['date_acceptation']) && empty($pret['date_refus']);
    }));
    $pendingPrets = count(array_filter($clientPrets, function($pret) {
      return empty($pret['date_acceptation']) && empty($pret['date_refus']);
    }));
    $rejectedPrets = count(array_filter($clientPrets, function($pret) {
      return !empty($pret['date_refus']);
    }));
    
    // Calculate total amount
    $totalMontant = array_sum(array_column($clientPrets, 'montant'));
    
    $config = $this->app->get('config');

    $content = $this->app->view()->fetch('client/dashboard', [
      'user' => $currentUser,
      'totalPrets' => $totalPrets,
      'activePrets' => $activePrets,
      'pendingPrets' => $pendingPrets,
      'rejectedPrets' => $rejectedPrets,
      'totalMontant' => $totalMontant,
      'recentPrets' => array_slice($clientPrets, -3),
      'config' => $config
    ]);

    $this->app->view()->render('client/layout', [
      'title' => 'Mon Espace',
      'subtitle' => 'Bienvenue dans votre espace personnel',
      'content' => $content
    ]);
  }

  public function loans()
  {
    $currentUser = $_SESSION['user'];
    $clientId = $currentUser['id'];
    
    $allPrets = $this->apiCall('/prets');
    $clientPrets = array_filter($allPrets ?? [], function($pret) use ($clientId) {
      return $pret['client_id'] == $clientId;
    });
    
    // Sort loans by date (most recent first)
    usort($clientPrets, function($a, $b) {
      return strtotime($b['date_creation']) - strtotime($a['date_creation']);
    });
    
    $typePrets = $this->apiCall('/type-prets');
    $config = $this->app->get('config');

    $content = $this->app->view()->fetch('client/loans', [
      'user' => $currentUser,
      'prets' => array_values($clientPrets),
      'typePrets' => $typePrets,
      'config' => $config
    ]);

    $this->app->view()->render('client/layout', [
      'title' => 'Mes Prêts',
      'subtitle' => 'Gestion de vos demandes de prêts',
      'content' => $content
    ]);
  }

  public function simulate()
  {
    $typePrets = $this->apiCall('/type-prets');
    $config = $this->app->get('config');

    $content = $this->app->view()->fetch('client/simulate', [
      'typePrets' => $typePrets,
      'config' => $config
    ]);

    $this->app->view()->render('client/layout', [
      'title' => 'Simuler un Prêt',
      'subtitle' => 'Calculez votre capacité d\'emprunt',
      'content' => $content
    ]);
  }

  public function createLoan()
  {
    $currentUser = $_SESSION['user'];
    
    $typePretId = $_POST['type_pret_id'] ?? null;
    $montant = $_POST['montant'] ?? null;
    $duree = $_POST['duree'] ?? null;
    $dateRetour = $_POST['date_creation'] ?? date('Y-m-d H:i:s');
    $delai = $_POST['delai'] ?? 0;
    $dateRetour = (new DateTime($dateRetour))->format('Y-m-d H:i:s');

    if (!$typePretId || !$montant || !$duree) {
      $this->app->redirect('/client/simulate?error=missing_data');
      return;
    }

    try {
      $loanData = [
        'client_id' => (int)$currentUser['id'],
        'type_pret_id' => (int)$typePretId,
        'montant' => (float)$montant,
        'duree' => (int)$duree,
        'date_acceptation' => null,
        'date_refus' => null,
        'date_creation' => $dateRetour,
        'delai' => (int)$delai
      ];

      $result = $this->apiCall('/prets', 'POST', $loanData);
      
      if ($result && !isset($result['error'])) {
        $this->app->redirect('/client/simulate?success=loan_created');
      } else {
        $this->app->redirect('/client/simulate?error=loan_creation_failed');
      }
    } catch (\Exception $e) {
      $this->app->redirect('/client/simulate?error=loan_creation_error');
    }
  }

  public function processPayment()
  {
    $currentUser = $_SESSION['user'];
    
    $pretId = $_POST['pret_id'] ?? null;
    $montant = $_POST['montant'] ?? null;
    $dateRetour = $_POST['date_retour'] ?? null;

    if (!$pretId || !$montant || !$dateRetour) {
      $this->app->redirect('/client/loans?error=missing_data');
      return;
    }

    $paymentData = [
      'pret_id' => (int)$pretId,
      'montant' => (float)$montant,
      'date_retour' => $dateRetour
    ];

    try {
      $result = $this->apiCall('/pret-retour-historiques', 'POST', $paymentData);
      
      if ($result && !isset($result['error'])) {
        $this->app->redirect('/client/loans?success=payment_successful');
      } else {
        $this->app->redirect('/client/loans?error=payment_failed');
      }
    } catch (\Exception $e) {
      $this->app->redirect('/client/loans?error=payment_error');
    }
  }
}
