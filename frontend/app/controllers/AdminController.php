<?php

declare(strict_types=1);

namespace app\controllers;

use flight\Engine;

class AdminController
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

  public function interets()
  {
    $config = $this->app->get('config');
    
    $content = $this->app->view()->fetch('admin/interets', [
      'config' => $config
    ]);

    $this->app->view()->render('admin/layout', [
      'title' => 'Analyse des Intérêts',
      'subtitle' => 'Suivez l\'évolution de vos revenus d\'intérêts',
      'content' => $content
    ]);
  }

  public function dashboard()
  {
    // Get comprehensive data for dashboard
    $prets = $this->apiCall('/prets');
    $clients = $this->apiCall('/clients');
    $fonds = $this->apiCall('/fonds');
    $typePrets = $this->apiCall('/type-prets');
    $sourceFonds = $this->apiCall('/source-fonds');
    $retourHistoriques = $this->apiCall('/pret-retour-historiques');
    $fondHistoriques = $this->apiCall('/fond-historiques');
    
    // Calculate dashboard metrics
    $totalMontantPrets = array_sum(array_column($prets ?? [], 'montant'));
    $pretsApprouves = array_filter($prets ?? [], fn($p) => !empty($p['date_acceptation']));
    $pretsEnAttente = array_filter($prets ?? [], fn($p) => empty($p['date_acceptation']) && empty($p['date_refus']));
    $pretsRefuses = array_filter($prets ?? [], fn($p) => !empty($p['date_refus']));
    
    $totalMontantFonds = array_sum(array_column($fonds ?? [], 'montant_initial'));
    $totalRetours = array_sum(array_column($retourHistoriques ?? [], 'montant'));
    
    // Calculate monthly trends for charts
    $monthlyPrets = [];
    $monthlyRetours = [];
    
    for ($i = 5; $i >= 0; $i--) {
      $date = date('Y-m', strtotime("-$i months"));
      $monthlyPrets[$date] = 0;
      $monthlyRetours[$date] = 0;
    }
    
    foreach ($prets ?? [] as $pret) {
      if (!empty($pret['date_creation'])) {
        $month = date('Y-m', strtotime($pret['date_creation']));
        if (isset($monthlyPrets[$month])) {
          $monthlyPrets[$month] += $pret['montant'];
        }
      }
    }
    
    foreach ($retourHistoriques ?? [] as $retour) {
      if (!empty($retour['date_retour'])) {
        $month = date('Y-m', strtotime($retour['date_retour']));
        if (isset($monthlyRetours[$month])) {
          $monthlyRetours[$month] += $retour['montant'];
        }
      }
    }
    
    $config = $this->app->get('config');

    $content = $this->app->view()->fetch('admin/dashboard', [
      'totalPrets' => count($prets ?? []),
      'totalClients' => count($clients ?? []),
      'totalFonds' => count($fonds ?? []),
      'totalMontantPrets' => $totalMontantPrets,
      'totalMontantFonds' => $totalMontantFonds,
      'totalRetours' => $totalRetours,
      'pretsApprouves' => count($pretsApprouves),
      'pretsEnAttente' => count($pretsEnAttente),
      'pretsRefuses' => count($pretsRefuses),
      'monthlyPrets' => $monthlyPrets,
      'monthlyRetours' => $monthlyRetours,
      'typePrets' => $typePrets ?? [],
      'sourceFonds' => $sourceFonds ?? [],
      'config' => $config
    ]);

    $this->app->view()->render('admin/layout', [
      'title' => 'Dashboard',
      'subtitle' => 'Vue d\'ensemble de votre activité',
      'content' => $content
    ]);
  }

  public function prets()
  {
    $prets = $this->apiCall('/prets');
    $clients = $this->apiCall('/clients');
    $typePrets = $this->apiCall('/type-prets');
    $config = $this->app->get('config');

    // Calculate loan counts for each client
    foreach ($clients as &$client) {
      $clientLoanCount = count(array_filter($prets, function($pret) use ($client) {
        return $pret['client_id'] == $client['id'];
      }));
      $client['loan_count'] = $clientLoanCount;
    }

    $content = $this->app->view()->fetch('admin/prets', [
      'prets' => $prets,
      'clients' => $clients,
      'typePrets' => $typePrets,
      'config' => $config
    ]);

    $this->app->view()->render('admin/layout', [
      'title' => 'Gestion des Prêts',
      'subtitle' => 'Administrez vos prêts en cours',
      'content' => $content
    ]);
  }

  public function createLoan()
  {
    $clientId = $_POST['client_id'] ?? null;
    $typePretId = $_POST['type_pret_id'] ?? null;
    $montant = $_POST['montant'] ?? null;
    $duree = $_POST['duree'] ?? null;

    if (!$clientId || !$typePretId || !$montant || !$duree) {
      $this->app->redirect('/admin/prets?error=missing_data');
      return;
    }

    try {
      $loanData = [
        'client_id' => (int)$clientId,
        'type_pret_id' => (int)$typePretId,
        'montant' => (float)$montant,
        'duree' => (int)$duree,
        'date_acceptation' => null,
        'date_refus' => null,
        'date_creation' => date('Y-m-d H:i:s')
      ];

      $result = $this->apiCall('/prets', 'POST', $loanData);
      
      if ($result && !isset($result['error'])) {
        $this->app->redirect('/admin/prets?success=loan_created');
      } else {
        $this->app->redirect('/admin/prets?error=loan_creation_failed');
      }
    } catch (\Exception $e) {
      $this->app->redirect('/admin/prets?error=loan_creation_error');
    }
  }

  public function createClient()
  {
    $nom = $_POST['nom'] ?? null;
    $prenom = $_POST['prenom'] ?? null;
    $email = $_POST['email'] ?? null;
    $password = $_POST['password'] ?? null;
    $userId = $_POST['user_id'] ?? null;

    if (!$nom || !$prenom || !$email || !$password || !$userId) {
      $this->app->redirect('/admin/clients?error=missing_data');
      return;
    }

    // Validate email format
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
      $this->app->redirect('/admin/clients?error=invalid_email');
      return;
    }

    // Validate password length
    if (strlen($password) < 6) {
      $this->app->redirect('/admin/clients?error=weak_password');
      return;
    }

    try {
      // Hash the password for security
      $hashedPassword = $password;
      // $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

      $clientData = [
        'nom' => trim($nom),
        'prenom' => trim($prenom),
        'email' => trim(strtolower($email)),
        'password' => $hashedPassword,
        'user_id' => (int)$userId
      ];

      $result = $this->apiCall('/clients', 'POST', $clientData);
      
      if ($result && !isset($result['error'])) {
        $this->app->redirect('/admin/clients?success=client_created');
      } else {
        $this->app->redirect('/admin/clients?error=client_creation_failed');
      }
    } catch (\Exception $e) {
      $this->app->redirect('/admin/clients?error=client_creation_error');
    }
  }

  public function clients()
  {
    $clients = $this->apiCall('/clients');
    $prets = $this->apiCall('/prets');
    $config = $this->app->get('config');

    // Get client ID from URL if viewing specific client
    $clientId = $_GET['client_id'] ?? null;
    $loanId = $_GET['loan_id'] ?? null;

    $currentClient = null;
    $currentLoan = null;
    $clientLoans = [];
    $paymentSchedule = [];

    if ($clientId) {
      $currentClient = array_filter($clients, function($client) use ($clientId) {
        return $client['id'] == $clientId;
      });
      $currentClient = $currentClient ? array_values($currentClient)[0] : null;

      if ($currentClient) {
        $clientLoans = array_filter($prets, function($pret) use ($clientId) {
          return $pret['client_id'] == $clientId;
        });
      }

      if ($loanId) {
        $currentLoan = array_filter($clientLoans, function($loan) use ($loanId) {
          return $loan['id'] == $loanId;
        });
        $currentLoan = $currentLoan ? array_values($currentLoan)[0] : null;

        if ($currentLoan) {
          $paymentSchedule = $this->apiCall('/prets/' . $loanId . '/payments');
          $existingPayments = $this->apiCall('/pret-retour-historiques');
          $loanPayments = array_filter($existingPayments, function($payment) use ($loanId) {
            return $payment['pret_id'] == $loanId;
          });
        }
      }
    }

    // Calculate loan counts for each client
    foreach ($clients as &$client) {
      $clientLoanCount = count(array_filter($prets, function($pret) use ($client) {
        return $pret['client_id'] == $client['id'];
      }));
      $client['loan_count'] = $clientLoanCount;
    }

    $content = $this->app->view()->fetch('admin/clients', [
      'clients' => $clients,
      'prets' => $prets,
      'config' => $config,
      'currentClient' => $currentClient,
      'currentLoan' => $currentLoan,
      'clientLoans' => $clientLoans,
      'paymentSchedule' => $paymentSchedule ?? [],
      'loanPayments' => $loanPayments ?? [],
      'view' => $loanId ? 'payments' : ($clientId ? 'loans' : 'clients')
    ]);

    $this->app->view()->render('admin/layout', [
      'title' => 'Gestion des Clients',
      'subtitle' => 'Base de données des clients et leurs prêts',
      'content' => $content
    ]);
  }

  public function fonds()
  {
    $fonds = $this->apiCall('/fonds');
    $sourceFonds = $this->apiCall('/source-fonds');

    $content = $this->app->view()->fetch('admin/fonds', [
      'fonds' => $fonds,
      'sourceFonds' => $sourceFonds
    ]);

    $this->app->view()->render('admin/layout', [
      'title' => 'Gestion des Fonds',
      'subtitle' => 'Suivi de vos ressources financières',
      'content' => $content
    ]);
  }

  public function settings()
  {
    $content = $this->app->view()->fetch('admin/settings', []);

    $this->app->view()->render('admin/layout', [
      'title' => 'Paramètres',
      'subtitle' => 'Configuration du système',
      'content' => $content
    ]);
  }

  public function processPayment()
  {
    $pretId = $_POST['pret_id'] ?? null;
    $montant = $_POST['montant'] ?? null;
    $dateRetour = $_POST['date_retour'] ?? null;
    $clientId = $_POST['client_id'] ?? null;
    $loanId = $_POST['loan_id'] ?? null;

    if (!$pretId || !$montant || !$dateRetour) {
      $this->app->redirect('/admin/clients?error=missing_data');
      return;
    }

    // Convert date to timestamp for API

    $paymentData = [
      'pret_id' => (int)$pretId,
      'montant' => (float)$montant,
      'date_retour' => $dateRetour
    ];

    try {
      $result = $this->apiCall('/pret-retour-historiques', 'POST', $paymentData);
      
      if ($result && !isset($result['error'])) {
        // Redirect back to the payment view with success message
        $redirectUrl = '/admin/clients?client_id=' . $clientId . '&loan_id=' . $loanId . '&success=payment_successful';
        $this->app->redirect($redirectUrl);
      } else {
        // Redirect back with error message
        $redirectUrl = '/admin/clients?client_id=' . $clientId . '&loan_id=' . $loanId . '&error=payment_failed';
        $this->app->redirect($redirectUrl);
      }
    } catch (\Exception $e) {
      // Redirect back with error message
      $redirectUrl = '/admin/clients?client_id=' . $clientId . '&loan_id=' . $loanId . '&error=payment_error';
      $this->app->redirect($redirectUrl);
    }
  }
}
