<?php

declare(strict_types=1);

namespace app\controllers;

use flight\Engine;

class PdfExportController
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
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);

    if ($data) {
      curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
      curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    }

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);
    curl_close($ch);

    if ($error) {
      error_log('CURL Error: ' . $error);
      throw new \Exception('Erreur de connexion: ' . $error);
    }

    if ($httpCode !== 200) {
      error_log('HTTP Error: ' . $httpCode . ' - Response: ' . $response);
      throw new \Exception('Erreur HTTP: ' . $httpCode);
    }

    return $response;
  }

  public function exportLoanPayments($loanId)
  {
    try {
      $htmlContent = $this->apiCall('/prets/' . $loanId . '/export-pdf');
      
      // Set headers for HTML display
      header('Content-Type: text/html; charset=utf-8');
      header('Cache-Control: no-cache, must-revalidate');
      header('Pragma: no-cache');
      
      echo $htmlContent;
      exit;
      
    } catch (\Exception $e) {
      error_log('PDF Export Frontend Error: ' . $e->getMessage());
      
      // Show error page instead of redirect
      echo '<!DOCTYPE html>
      <html>
      <head>
          <meta charset="UTF-8">
          <title>Erreur - Export PDF</title>
          <style>
              body { font-family: Arial, sans-serif; padding: 50px; text-align: center; }
              .error { color: #dc3545; background: #f8d7da; border: 1px solid #f5c6cb; padding: 20px; border-radius: 5px; }
          </style>
      </head>
      <body>
          <div class="error">
              <h2>Erreur lors de l\'export PDF</h2>
              <p>' . htmlspecialchars($e->getMessage()) . '</p>
              <button onclick="window.close()">Fermer</button>
          </div>
      </body>
      </html>';
      exit;
    }
  }
}
