<?php

namespace models;

use PDO;

class PdfExport
{
  public static function getLoanPaymentData($pretId)
  {
    $db = getDB();
    
    // Get loan details with client and type information
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
    $loan = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$loan) {
      return null;
    }
    
    // Get payment schedule using the Prets model
    try {
      $paymentSchedule = \models\Prets::getMontantAPayer($pretId);
    } catch (\Exception $e) {
      $paymentSchedule = [];
    }
    
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
    $payments = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    return [
      'loan' => $loan,
      'schedule' => $paymentSchedule,
      'payments' => $payments
    ];
  }
  
  public static function generatePaymentsPdf($pretId)
  {
    $data = self::getLoanPaymentData($pretId);
    
    if (!$data) {
      throw new \Exception('Prêt non trouvé');
    }
    
    $loan = $data['loan'];
    $schedule = $data['schedule'];
    $payments = $data['payments'];
    
    // Calculate totals
    $totalAmount = $loan['montant'];
    $annualPayment = $schedule[0]['montant'] ?? ($totalAmount / $loan['duree']);
    $totalWithInterest = $annualPayment * $loan['duree'];
    $totalInterest = $totalWithInterest - $totalAmount;
    
    // Generate HTML for PDF
    $html = self::generatePaymentsPdfHtml($loan, $schedule, $payments, $totalAmount, $annualPayment, $totalWithInterest, $totalInterest);
    
    return $html;
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
            body {
                font-family: Arial, sans-serif;
                font-size: 12px;
                line-height: 1.4;
                color: #333;
                margin: 0;
                padding: 20px;
            }
            .header {
                text-align: center;
                margin-bottom: 30px;
                border-bottom: 2px solid #3B82F6;
                padding-bottom: 20px;
            }
            .header h1 {
                color: #3B82F6;
                margin: 0;
                font-size: 24px;
            }
            .header p {
                margin: 5px 0;
                color: #666;
            }
            .loan-info {
                background: #f8f9fa;
                padding: 15px;
                border-radius: 8px;
                margin-bottom: 20px;
            }
            .loan-info h2 {
                color: #3B82F6;
                margin-top: 0;
                font-size: 16px;
            }
            .info-grid {
                display: grid;
                grid-template-columns: 1fr 1fr;
                gap: 15px;
                margin-top: 10px;
            }
            .info-item {
                display: flex;
                justify-content: space-between;
                padding: 5px 0;
                border-bottom: 1px dotted #ddd;
            }
            .info-label {
                font-weight: bold;
                color: #555;
            }
            .info-value {
                color: #333;
            }
            .summary-section {
                margin: 20px 0;
                padding: 15px;
                background: #e3f2fd;
                border-radius: 8px;
            }
            .summary-section h3 {
                color: #1976d2;
                margin-top: 0;
            }
            .summary-grid {
                display: grid;
                grid-template-columns: repeat(4, 1fr);
                gap: 10px;
                margin-top: 10px;
            }
            .summary-item {
                text-align: center;
                padding: 10px;
                background: white;
                border-radius: 5px;
                border: 1px solid #ddd;
            }
            .summary-value {
                font-size: 14px;
                font-weight: bold;
                color: #1976d2;
            }
            .summary-label {
                font-size: 10px;
                color: #666;
                margin-top: 3px;
            }
            .payments-table {
                width: 100%;
                border-collapse: collapse;
                margin-top: 20px;
                background: white;
            }
            .payments-table th {
                background: #3B82F6;
                color: white;
                padding: 10px 8px;
                text-align: left;
                font-size: 11px;
                font-weight: bold;
            }
            .payments-table td {
                padding: 8px;
                border-bottom: 1px solid #e0e0e0;
                font-size: 11px;
            }
            .payments-table tr:nth-child(even) {
                background: #f9f9f9;
            }
            .status-paid {
                background: #4caf50;
                color: white;
                padding: 2px 8px;
                border-radius: 12px;
                font-size: 10px;
                font-weight: bold;
            }
            .status-pending {
                background: #ff9800;
                color: white;
                padding: 2px 8px;
                border-radius: 12px;
                font-size: 10px;
                font-weight: bold;
            }
            .footer {
                margin-top: 30px;
                text-align: center;
                font-size: 10px;
                color: #666;
                border-top: 1px solid #ddd;
                padding-top: 15px;
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
            <div class="info-grid">
                <div class="info-item">
                    <span class="info-label">Client:</span>
                    <span class="info-value">' . htmlspecialchars($loan['client_nom'] . ' ' . $loan['client_prenom']) . '</span>
                </div>
                <div class="info-item">
                    <span class="info-label">Email:</span>
                    <span class="info-value">' . htmlspecialchars($loan['client_email']) . '</span>
                </div>
                <div class="info-item">
                    <span class="info-label">Type de prêt:</span>
                    <span class="info-value">' . htmlspecialchars($loan['type_pret_nom']) . '</span>
                </div>
                <div class="info-item">
                    <span class="info-label">Taux d\'intérêt:</span>
                    <span class="info-value">' . $loan['taux_interet'] . '%</span>
                </div>
                <div class="info-item">
                    <span class="info-label">Montant emprunté:</span>
                    <span class="info-value amount">' . number_format($totalAmount, 0, ',', ' ') . ' Ar</span>
                </div>
                <div class="info-item">
                    <span class="info-label">Durée:</span>
                    <span class="info-value">' . $loan['duree'] . ' année(s)</span>
                </div>
                <div class="info-item">
                    <span class="info-label">Date de création:</span>
                    <span class="info-value">' . $loanDate . '</span>
                </div>
                <div class="info-item">
                    <span class="info-label">Date d\'approbation:</span>
                    <span class="info-value">' . $approvalDate . '</span>
                </div>
            </div>
        </div>

        <div class="summary-section">
            <h3>Résumé Financier</h3>
            <div class="summary-grid">
                <div class="summary-item">
                    <div class="summary-value">' . number_format($totalAmount, 0, ',', ' ') . ' Ar</div>
                    <div class="summary-label">Capital</div>
                </div>
                <div class="summary-item">
                    <div class="summary-value">' . number_format($annualPayment, 0, ',', ' ') . ' Ar</div>
                    <div class="summary-label">Paiement Annuel</div>
                </div>
                <div class="summary-item">
                    <div class="summary-value">' . number_format($totalWithInterest, 0, ',', ' ') . ' Ar</div>
                    <div class="summary-label">Total à Rembourser</div>
                </div>
                <div class="summary-item">
                    <div class="summary-value">' . number_format($totalInterest, 0, ',', ' ') . ' Ar</div>
                    <div class="summary-label">Intérêts Totaux</div>
                </div>
            </div>
        </div>

        <h3 style="color: #3B82F6; margin-top: 30px;">Calendrier des Paiements</h3>
        <table class="payments-table">
            <thead>
                <tr>
                    <th>Année</th>
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
                    <td>Année ' . $yearNumber . '</td>
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
}
