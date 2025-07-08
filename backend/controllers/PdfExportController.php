<?php

namespace controllers;

use Flight;
use models\Prets;
use models\PretRetourHistoriques;

class PdfExportController
{
  public static function exportLoanPayments($pretId)
  {
    ob_start();
    
    try {
      // Get loan details with client and type information
      $db = getDB();
      
      $loanQuery = "
        SELECT 
          p.*,
          c.nom as client_nom,
          c.prenom as client_prenom,
          c.email as client_email,
          tp.nom as type_pret_nom,
          tp.taux_interet,
          tp.taux_assurance
        FROM s4_prets p
        JOIN s4_clients c ON p.client_id = c.id
        JOIN s4_type_prets tp ON p.type_pret_id = tp.id
        WHERE p.id = ?
      ";
      
      $stmt = $db->prepare($loanQuery);
      $stmt->execute([$pretId]);
      $loan = $stmt->fetch(\PDO::FETCH_ASSOC);
      
      if (!$loan) {
        Flight::json(['error' => 'Prêt non trouvé'], 404);
        return;
      }
      
      // Get payment schedule
      $paymentSchedule = Prets::getMontantAPayer($pretId);
      
      // Get actual payments
      $paymentsQuery = "
        SELECT 
          *,
          YEAR(date_retour) as payment_year,
          MONTH(date_retour) as payment_month
        FROM s4_pret_retour_historiques 
        WHERE pret_id = ?
        ORDER BY date_retour ASC
      ";
      
      $stmt = $db->prepare($paymentsQuery);
      $stmt->execute([$pretId]);
      $payments = $stmt->fetchAll(\PDO::FETCH_ASSOC);
      
      // Calculate totals
      $totalAmount = $loan['montant'];
      $annualPayment = $paymentSchedule[0]['montant'] ?? ($totalAmount / $loan['duree']);
      $totalWithInterest = $annualPayment * $loan['duree'];
      $totalInterest = $totalWithInterest - $totalAmount;
      
      // Generate HTML for PDF
      $html = self::generatePaymentsPdfHtml($loan, $paymentSchedule, $payments, $totalAmount, $annualPayment, $totalWithInterest, $totalInterest);
      
      // Try different PDF generation methods
      $pdf = self::generatePdfFromHtml($html, $pretId);

      ob_end_clean();

      if ($pdf) {
        // Set headers for PDF download
        Flight::response()
          ->header('Content-Type', 'application/pdf')
          ->header('Content-Disposition', 'attachment; filename="paiements_pret_' . $pretId . '.pdf"')
          ->header('Cache-Control', 'no-cache, must-revalidate')
          ->header('Pragma', 'no-cache')
          ->header('Content-Length', strlen($pdf))
          ->write($pdf)
          ->send();
      } else {
        // Fallback to HTML if PDF generation fails
        Flight::response()
          ->header('Content-Type', 'text/html; charset=utf-8')
          ->header('Content-Disposition', 'inline; filename="paiements_pret_' . $pretId . '.html"')
          ->header('Cache-Control', 'no-cache, must-revalidate')
          ->header('Pragma', 'no-cache')
          ->write($html)
          ->send();
      }
      
    } catch (\Exception $e) {
      error_log('PDF Export Error: ' . $e->getMessage());
      Flight::json(['error' => 'Erreur lors de la génération du PDF: ' . $e->getMessage()], 500);
    }
  }
  
  private static function generatePdfFromHtml($html, $pretId)
  {
    if (self::isTcpdfAvailable()) {
      return self::generatePdfWithTcpdf($html);
    }
    
    return null; // No PDF library available
  }
  
  private static function isTcpdfAvailable()
  {
    return class_exists('TCPDF');
  }
  
  private static function generatePdfWithTcpdf($html)
  {
    try {
      $pdf = new \TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
      
      $pdf->SetCreator('FinanceAdmin');
      $pdf->SetAuthor('System');
      $pdf->SetTitle('Relevé de Paiements');
      $pdf->SetMargins(15, 15, 15);
      $pdf->SetAutoPageBreak(TRUE, 15);
      
      $pdf->AddPage();
      $pdf->writeHTML($html, true, false, true, false, '');
      
      return $pdf->Output('', 'S');
    } catch (\Exception $e) {
      error_log('TCPDF error: ' . $e->getMessage());
      return null;
    }
  }
  
  private static function generatePaymentsPdfHtml($loan, $schedule, $payments, $totalAmount, $annualPayment, $totalWithInterest, $totalInterest)
  {
    $currentDate = date('d/m/Y H:i');
    $loanDate = date('d/m/Y', strtotime($loan['date_creation']));
    $approvalDate = $loan['date_acceptation'] ? date('d/m/Y', strtotime($loan['date_acceptation'])) : 'En attente';
    
    $html = '
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset="UTF-8">
        <title>Relevé de Paiements - Prêt #' . $loan['id'] . '</title>
        <style>
            @page {
                margin: 2cm;
                size: A4;
            }
            body {
                font-family: "DejaVu Sans", Arial, sans-serif;
                font-size: 11px;
                line-height: 1.4;
                color: #333;
                margin: 0;
                padding: 0;
            }
            .header {
                text-align: center;
                margin-bottom: 25px;
                border-bottom: 2px solid #3B82F6;
                padding-bottom: 15px;
            }
            .header h1 {
                color: #3B82F6;
                margin: 0;
                font-size: 20px;
                font-weight: bold;
            }
            .header p {
                margin: 3px 0;
                color: #666;
                font-size: 10px;
            }
            .loan-info {
                background: #f8f9fa;
                padding: 12px;
                border-radius: 6px;
                margin-bottom: 20px;
                border: 1px solid #e9ecef;
            }
            .loan-info h2 {
                color: #3B82F6;
                margin-top: 0;
                margin-bottom: 10px;
                font-size: 14px;
            }
            .info-grid {
                width: 100%;
                border-collapse: collapse;
            }
            .info-grid td {
                padding: 4px 8px;
                border-bottom: 1px dotted #ddd;
                vertical-align: top;
            }
            .info-label {
                font-weight: bold;
                color: #555;
                width: 25%;
            }
            .info-value {
                color: #333;
                width: 25%;
            }
            .summary-section {
                margin: 15px 0;
                padding: 12px;
                background: #e3f2fd;
                border-radius: 6px;
                border: 1px solid #bbdefb;
            }
            .summary-section h3 {
                color: #1976d2;
                margin-top: 0;
                margin-bottom: 10px;
                font-size: 13px;
            }
            .summary-grid {
                width: 100%;
                border-collapse: collapse;
            }
            .summary-item {
                text-align: center;
                padding: 8px;
                background: white;
                border: 1px solid #ddd;
                vertical-align: top;
            }
            .summary-value {
                font-size: 12px;
                font-weight: bold;
                color: #1976d2;
                display: block;
                margin-bottom: 2px;
            }
            .summary-label {
                font-size: 9px;
                color: #666;
            }
            .payments-table {
                width: 100%;
                border-collapse: collapse;
                margin-top: 15px;
                background: white;
                border: 1px solid #ddd;
            }
            .payments-table th {
                background: #3B82F6;
                color: white;
                padding: 8px 6px;
                text-align: left;
                font-size: 10px;
                font-weight: bold;
                border: 1px solid #2563eb;
            }
            .payments-table td {
                padding: 6px;
                border: 1px solid #e0e0e0;
                font-size: 10px;
                vertical-align: top;
            }
            .payments-table tr:nth-child(even) {
                background: #f9f9f9;
            }
            .status-paid {
                background: #4caf50;
                color: white;
                padding: 2px 6px;
                border-radius: 10px;
                font-size: 8px;
                font-weight: bold;
                display: inline-block;
            }
            .status-pending {
                background: #ff9800;
                color: white;
                padding: 2px 6px;
                border-radius: 10px;
                font-size: 8px;
                font-weight: bold;
                display: inline-block;
            }
            .footer {
                margin-top: 25px;
                text-align: center;
                font-size: 9px;
                color: #666;
                border-top: 1px solid #ddd;
                padding-top: 12px;
            }
            .amount {
                font-weight: bold;
                color: #1976d2;
            }
            .text-center {
                text-align: center;
            }
            .text-right {
                text-align: right;
            }
            .page-break {
                page-break-before: always;
            }
        </style>
    </head>
    <body>
        <div class="header">
            <h1>RELEVÉ DE PAIEMENTS</h1>
            <p>Prêt N° ' . str_pad($loan['id'], 6, '0', STR_PAD_LEFT) . '</p>
            <p>Généré le ' . $currentDate . '</p>
        </div>

        <div class="loan-info">
            <h2>Informations du Prêt</h2>
            <table class="info-grid">
                <tr>
                    <td class="info-label">Client:</td>
                    <td class="info-value">' . htmlspecialchars($loan['client_nom'] . ' ' . $loan['client_prenom']) . '</td>
                    <td class="info-label">Email:</td>
                    <td class="info-value">' . htmlspecialchars($loan['client_email']) . '</td>
                </tr>
                <tr>
                    <td class="info-label">Type de prêt:</td>
                    <td class="info-value">' . htmlspecialchars($loan['type_pret_nom']) . '</td>
                    <td class="info-label">Taux d\'intérêt:</td>
                    <td class="info-value">' . $loan['taux_interet'] . '%</td>
                </tr>
                <tr>
                    <td class="info-label">Montant emprunté:</td>
                    <td class="info-value amount">' . number_format($totalAmount, 0, ',', ' ') . ' Ar</td>
                    <td class="info-label">Durée:</td>
                    <td class="info-value">' . $loan['duree'] . ' année(s)</td>
                </tr>
                <tr>
                    <td class="info-label">Date de création:</td>
                    <td class="info-value">' . $loanDate . '</td>
                    <td class="info-label">Date d\'approbation:</td>
                    <td class="info-value">' . $approvalDate . '</td>
                </tr>
            </table>
        </div>

        <div class="summary-section">
            <h3>Résumé Financier</h3>
            <table class="summary-grid">
                <tr>
                    <td class="summary-item">
                        <span class="summary-value">' . number_format($totalAmount, 0, ',', ' ') . ' Ar</span>
                        <div class="summary-label">Capital</div>
                    </td>
                    <td class="summary-item">
                        <span class="summary-value">' . number_format($annualPayment, 0, ',', ' ') . ' Ar</span>
                        <div class="summary-label">Paiement Mensuel</div>
                    </td>
                    <td class="summary-item">
                        <span class="summary-value">' . number_format($totalWithInterest, 0, ',', ' ') . ' Ar</span>
                        <div class="summary-label">Total à Rembourser</div>
                    </td>
                    <td class="summary-item">
                        <span class="summary-value">' . number_format($totalInterest, 0, ',', ' ') . ' Ar</span>
                        <div class="summary-label">Intérêts Totaux</div>
                    </td>
                </tr>
            </table>
        </div>

        <h3 style="color: #3B82F6; margin-top: 25px; font-size: 14px;">Calendrier des Paiements</h3>
        <table class="payments-table">
            <thead>
                <tr>
                    <th>Mois</th>
                    <th>Date d\'échéance</th>
                    <th class="text-right">Montant à Payer</th>
                    <th class="text-center">Statut</th>
                    <th>Date de Paiement</th>
                    <th class="text-right">Montant Payé</th>
                </tr>
            </thead>
            <tbody>';
    
    // Create payments lookup
    $paymentsLookup = [];
    foreach ($payments as $payment) {
      $paymentsLookup[$payment['payment_year']] = $payment;
    }
    
    foreach ($schedule as $index => $scheduleItem) {
      $yearNumber = $index + 1;
      $dueDate = date('d/m/Y', strtotime($scheduleItem['date_echeance']));
      $year = $scheduleItem['annee'];
      
      $actualPayment = $paymentsLookup[$year] ?? null;
      $isPaid = $scheduleItem['isPayer'] || $actualPayment;
      
      $status = $isPaid ? '<span class="status-paid">Payé</span>' : '<span class="status-pending">En attente</span>';
      $paymentDate = $actualPayment ? date('d/m/Y', strtotime($actualPayment['date_retour'])) : '-';
      $paidAmount = $actualPayment ? number_format($actualPayment['montant'], 0, ',', ' ') . ' Ar' : '-';
      
      $html .= '
                <tr>
                    <td>Mois ' . $yearNumber . '</td>
                    <td>' . $dueDate . '</td>
                    <td class="text-right amount">' . number_format($scheduleItem['montant'], 0, ',', ' ') . ' Ar</td>
                    <td class="text-center">' . $status . '</td>
                    <td>' . $paymentDate . '</td>
                    <td class="text-right">' . $paidAmount . '</td>
                </tr>';
    }
    
    $html .= '
            </tbody>
        </table>

        <div class="footer">
            <p><strong>FinanceAdmin</strong> - Système de Gestion des Prêts</p>
            <p>Ce document a été généré automatiquement le ' . $currentDate . '</p>
            <p>Pour toute question, veuillez contacter votre conseiller financier</p>
        </div>
    </body>
    </html>';
    
    return $html;
  }
  
  public static function getLoanPaymentData($pretId)
  {
    try {
      $data = self::getLoanData($pretId);
      
      if (!$data) {
        Flight::json(['error' => 'Prêt non trouvé'], 404);
        return;
      }
      
      Flight::json($data);
      
    } catch (\Exception $e) {
      Flight::json(['error' => $e->getMessage()], 500);
    }
  }
  
  private static function getLoanData($pretId)
  {
    $db = getDB();
    
    $loanQuery = "
      SELECT 
        p.*,
        c.nom as client_nom,
        c.prenom as client_prenom,
        c.email as client_email,
        tp.nom as type_pret_nom,
        tp.taux_interet,
        tp.taux_assurance
      FROM s4_prets p
      JOIN s4_clients c ON p.client_id = c.id
      JOIN s4_type_prets tp ON p.type_pret_id = tp.id
      WHERE p.id = ?
    ";
    
    $stmt = $db->prepare($loanQuery);
    $stmt->execute([$pretId]);
    $loan = $stmt->fetch(\PDO::FETCH_ASSOC);
    
    if (!$loan) {
      return null;
    }
    
    $paymentSchedule = Prets::getMontantAPayer($pretId);
    
    $paymentsQuery = "
      SELECT 
        *,
        YEAR(date_retour) as payment_year,
        MONTH(date_retour) as payment_month
      FROM s4_pret_retour_historiques 
      WHERE pret_id = ?
      ORDER BY date_retour ASC
    ";
    
    $stmt = $db->prepare($paymentsQuery);
    $stmt->execute([$pretId]);
    $payments = $stmt->fetchAll(\PDO::FETCH_ASSOC);
    
    return [
      'loan' => $loan,
      'schedule' => $paymentSchedule,
      'payments' => $payments
    ];
  }
}

