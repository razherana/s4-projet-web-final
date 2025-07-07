<div class="clients-dashboard">
  
  <?php if ($view === 'clients'): ?>
    <!-- Success/Error Messages -->
    <?php if (isset($_GET['success'])): ?>
      <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fas fa-check-circle"></i>
        <?php
          switch ($_GET['success']) {
            case 'client_created':
              echo 'Client créé avec succès !';
              break;
            default:
              echo 'Opération réussie !';
          }
        ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
      </div>
    <?php elseif (isset($_GET['error'])): ?>
      <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="fas fa-exclamation-triangle"></i>
        <?php
          switch ($_GET['error']) {
            case 'missing_data':
              echo 'Données manquantes pour créer le client.';
              break;
            case 'invalid_email':
              echo 'Format d\'email invalide.';
              break;
            case 'weak_password':
              echo 'Le mot de passe doit contenir au moins 6 caractères.';
              break;
            case 'client_creation_failed':
              echo 'Échec de la création du client. L\'email existe peut-être déjà.';
              break;
            case 'client_creation_error':
              echo 'Erreur lors de la création du client.';
              break;
            default:
              echo 'Une erreur est survenue.';
          }
        ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
      </div>
    <?php endif; ?>

    <!-- Clients List Section -->
    <div class="clients-section">
      <div class="section-header">
        <div class="header-content">
          <h2 class="section-title">
            <i class="fas fa-users"></i>
            Gestion des Clients
          </h2>
          <p class="section-subtitle">Sélectionnez un client pour voir ses prêts</p>
        </div>
        <div class="header-actions">
          <button class="action-btn primary" onclick="showAddClientModal()">
            <i class="fas fa-plus"></i>
            Nouveau Client
          </button>
        </div>
      </div>

      <div class="clients-grid">
        <?php if (!empty($clients)): ?>
          <?php foreach ($clients as $client): ?>
            <a href="?client_id=<?= $client['id'] ?>" class="client-card">
              <div class="client-avatar">
                <i class="fas fa-user"></i>
              </div>
              <div class="client-info">
                <h3 class="client-name"><?= htmlspecialchars($client['nom'] . ' ' . $client['prenom']) ?></h3>
                <p class="client-email"><?= htmlspecialchars($client['email']) ?></p>
                <div class="client-stats">
                  <span class="stat-item">
                    <i class="fas fa-handshake"></i>
                    <?= $client['loan_count'] ?> prêt(s)
                  </span>
                  <span class="stat-item">
                    <i class="fas fa-calendar"></i>
                    Membre depuis <?= date('Y', strtotime($client['created_at'] ?? 'now')) ?>
                  </span>
                </div>
              </div>
              <div class="client-arrow">
                <i class="fas fa-chevron-right"></i>
              </div>
            </a>
          <?php endforeach; ?>
        <?php else: ?>
          <div class="empty-state">
            <i class="fas fa-users"></i>
            <h3>Aucun client trouvé</h3>
            <p>Commencez par ajouter votre premier client</p>
          </div>
        <?php endif; ?>
      </div>
    </div>

  <?php elseif ($view === 'loans'): ?>
    <!-- Client Loans Section -->
    <div class="loans-section">
      <div class="section-header">
        <div class="header-content">
          <a href="?" class="back-btn">
            <i class="fas fa-arrow-left"></i>
          </a>
          <div>
            <h2 class="section-title">
              <i class="fas fa-handshake"></i>
              Prêts de <?= htmlspecialchars($currentClient['nom'] . ' ' . $currentClient['prenom']) ?>
            </h2>
            <p class="section-subtitle">Cliquez sur un prêt approuvé pour voir les paiements</p>
          </div>
        </div>
      </div>

      <div class="loans-list">
        <?php if (!empty($clientLoans)): ?>
          <?php foreach ($clientLoans as $loan): ?>
            <?php 
              $isApproved = !empty($loan['date_acceptation']);
              $isRefused = !empty($loan['date_refus']);
              $canAccess = $isApproved && !$isRefused;
            ?>
            <?php if ($canAccess): ?>
              <a href="?client_id=<?= $currentClient['id'] ?>&loan_id=<?= $loan['id'] ?>" class="loan-card">
            <?php else: ?>
              <div class="loan-card <?= $isRefused ? 'loan-refused' : 'loan-pending' ?>">
            <?php endif; ?>
              <div class="loan-header">
                <h3 class="loan-title">Prêt #<?= $loan['id'] ?></h3>
                <span class="loan-status <?= $loan['date_acceptation'] ? 'approved' : ($loan['date_refus'] ? 'rejected' : 'pending') ?>">
                  <?= $loan['date_acceptation'] ? 'Approuvé' : ($loan['date_refus'] ? 'Refusé' : 'En attente') ?>
                </span>
              </div>
              <div class="loan-details">
                <div class="loan-detail">
                  <div class="detail-label">Montant</div>
                  <div class="detail-value"><?= number_format($loan['montant'], 0, ',', ' ') ?> Ar</div>
                </div>
                <div class="loan-detail">
                  <div class="detail-label">Durée</div>
                  <div class="detail-value"><?= $loan['duree'] ?> an(s)</div>
                </div>
                <div class="loan-detail">
                  <div class="detail-label">Paiement annuel</div>
                  <div class="detail-value"><?= number_format($loan['montant'] / $loan['duree'], 0, ',', ' ') ?> Ar</div>
                </div>
                <div class="loan-detail">
                  <div class="detail-label">Date création</div>
                  <div class="detail-value"><?= date('d/m/Y', strtotime($loan['date_creation'])) ?></div>
                </div>
              </div>
              <?php if ($isRefused): ?>
                <div class="loan-notice">
                  <i class="fas fa-info-circle"></i>
                  Ce prêt a été refusé. Aucun paiement n'est requis.
                </div>
              <?php elseif (!$isApproved): ?>
                <div class="loan-notice">
                  <i class="fas fa-clock"></i>
                  Ce prêt est en attente d'approbation.
                </div>
              <?php endif; ?>
            <?php if ($canAccess): ?>
              </a>
            <?php else: ?>
              </div>
            <?php endif; ?>
          <?php endforeach; ?>
        <?php else: ?>
          <div class="empty-state">
            <i class="fas fa-handshake"></i>
            <h3>Aucun prêt trouvé</h3>
            <p>Ce client n'a pas encore de prêts</p>
          </div>
        <?php endif; ?>
      </div>
    </div>

  <?php elseif ($view === 'payments'): ?>
    <?php if (empty($currentLoan['date_acceptation']) || !empty($currentLoan['date_refus'])): ?>
      <!-- Access Denied for Non-Approved Loans -->
      <div class="access-denied-section">
        <div class="section-header">
          <div class="header-content">
            <a href="?client_id=<?= $currentClient['id'] ?>" class="back-btn">
              <i class="fas fa-arrow-left"></i>
            </a>
            <div>
              <h2 class="section-title">
                <i class="fas fa-exclamation-triangle"></i>
                Accès Restreint - Prêt #<?= $currentLoan['id'] ?>
              </h2>
              <p class="section-subtitle">Ce prêt n'est pas accessible pour les paiements</p>
            </div>
          </div>
        </div>

        <div class="access-denied-card">
          <div class="denied-icon">
            <i class="fas fa-ban"></i>
          </div>
          <div class="denied-content">
            <h3>Paiements Non Autorisés</h3>
            <?php if (!empty($currentLoan['date_refus'])): ?>
              <p>Ce prêt a été <strong>refusé</strong> le <?= date('d/m/Y', strtotime($currentLoan['date_refus'])) ?>.</p>
              <p>Aucun paiement n'est requis pour les prêts refusés.</p>
            <?php else: ?>
              <p>Ce prêt est encore <strong>en attente d'approbation</strong>.</p>
              <p>Les paiements ne peuvent être effectués que pour les prêts approuvés.</p>
            <?php endif; ?>
          </div>
          <div class="denied-actions">
            <a href="?client_id=<?= $currentClient['id'] ?>" class="btn-back">
              <i class="fas fa-arrow-left"></i>
              Retour aux Prêts
            </a>
          </div>
        </div>
      </div>
    <?php else: ?>
      <!-- Normal Loan Payments Section for Approved Loans -->
      <div class="payments-section">
        <div class="section-header">
          <div class="header-content">
            <a href="?client_id=<?= $currentClient['id'] ?>" class="back-btn">
              <i class="fas fa-arrow-left"></i>
            </a>
            <div>
              <h2 class="section-title">
                <i class="fas fa-money-bill-wave"></i>
                Paiements - Prêt #<?= $currentLoan['id'] ?>
              </h2>
              <p class="section-subtitle">Historique des paiements</p>
            </div>
          </div>
        </div>

        <!-- Success/Error Messages -->
        <?php if (isset($_GET['success']) && $_GET['success'] === 'payment_successful'): ?>
          <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle"></i>
            Paiement enregistré avec succès !
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
          </div>
        <?php elseif (isset($_GET['error'])): ?>
          <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-triangle"></i>
            <?php
              switch ($_GET['error']) {
                case 'payment_failed':
                  echo 'Échec du paiement. Veuillez réessayer.';
                  break;
                case 'payment_error':
                  echo 'Erreur lors du traitement du paiement.';
                  break;
                case 'missing_data':
                  echo 'Données de paiement manquantes.';
                  break;
                default:
                  echo 'Une erreur est survenue.';
              }
            ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
          </div>
        <?php endif; ?>

        <!-- Loan Summary -->
        <div class="loan-summary">
          <div class="summary-grid">
            <div class="summary-item">
              <div class="summary-value"><?= number_format($currentLoan['montant'], 0, ',', ' ') ?> Ar</div>
              <div class="summary-label">Montant total</div>
            </div>
            <div class="summary-item">
              <div class="summary-value"><?= $currentLoan['duree'] ?> an(s)</div>
              <div class="summary-label">Durée</div>
            </div>
            <div class="summary-item">
              <div class="summary-value">
                <?php 
                  $annualPayment = !empty($paymentSchedule) ? $paymentSchedule[0]['montant'] : ($currentLoan['montant'] / $currentLoan['duree']);
                  echo number_format($annualPayment, 0, ',', ' '); 
                ?> Ar
              </div>
              <div class="summary-label">Paiement annuel</div>
            </div>
            <div class="summary-item">
              <div class="summary-value"><?= date('d/m/Y', strtotime($currentLoan['date_creation'])) ?></div>
              <div class="summary-label">Date de création</div>
            </div>
          </div>
        </div>

        <!-- Payments Table -->
        <div class="payments-list">
          <table class="payments-table">
            <thead>
              <tr>
                <th>Année #</th>
                <th>Date d'échéance</th>
                <th>Montant</th>
                <th>Statut</th>
                <th>Date de paiement</th>
                <th>Action</th>
              </tr>
            </thead>
            <tbody>
              <?php if (!empty($paymentSchedule)): ?>
                <?php 
                  $lastPaidYear = 0;
                  // Find last paid year
                  foreach ($paymentSchedule as $index => $payment) {
                    if ($payment['isPayer']) {
                      $lastPaidYear = $index + 1;
                    }
                  }
                ?>
                <?php foreach ($paymentSchedule as $index => $payment): ?>
                  <?php 
                    $yearNumber = $index + 1;
                    $dueDate = new DateTime($payment['date_echeance']);
                    
                    // Find corresponding actual payment
                    $actualPayment = null;
                    foreach ($loanPayments as $p) {
                      $paymentDate = new DateTime($p['date_retour']);
                      if ($paymentDate->format('Y') == $payment['annee']) {
                        $actualPayment = $p;
                        break;
                      }
                    }
                    
                    $isPaid = $payment['isPayer'] || $actualPayment;
                    $statusClass = $isPaid ? 'paid' : 'pending';
                    $statusText = $isPaid ? 'Payé' : 'En attente';
                    $paymentDateText = $actualPayment ? (new DateTime($actualPayment['date_retour']))->format('d/m/Y') : '-';
                    
                    if ($isPaid) {
                      $lastPaidYear = max($lastPaidYear, $yearNumber);
                    }
                  ?>
                  <tr>
                    <td>Année <?= $yearNumber ?></td>
                    <td><?= $dueDate->format('d/m/Y') ?></td>
                    <td><?= number_format($payment['montant'], 0, ',', ' ') ?> Ar</td>
                    <td><span class="payment-status <?= $statusClass ?>"><?= $statusText ?></span></td>
                    <td><?= $paymentDateText ?></td>
                    <td>
                      <?php if (!$isPaid && $yearNumber <= $lastPaidYear + 1): ?>
                        <form method="POST" action="<?= route('/admin/clients/payment') ?>" style="display: inline;">
                          <input type="hidden" name="pret_id" value="<?= $currentLoan['id'] ?>">
                          <input type="hidden" name="montant" value="<?= $payment['montant'] ?>">
                          <input type="hidden" name="date_retour" value="<?= $dueDate->format('Y-m-d') ?>">
                          <input type="hidden" name="client_id" value="<?= $currentClient['id'] ?>">
                          <input type="hidden" name="loan_id" value="<?= $currentLoan['id'] ?>">
                          <button type="submit" class="pay-btn" 
                            onclick="return confirm('Confirmer le paiement pour l\'année <?= $yearNumber ?> ?\nMontant: <?= number_format($payment['montant'], 0, ',', ' ') ?> Ar')">
                            Payer
                          </button>
                        </form>
                      <?php elseif (!$isPaid): ?>
                        <button class="pay-btn" disabled title="Payez d'abord les années précédentes">
                          Payer
                        </button>
                      <?php endif; ?>
                    </td>
                  </tr>
                <?php endforeach; ?>
              <?php else: ?>
                <tr>
                  <td colspan="6" class="text-center">Aucun paiement programmé</td>
                </tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>
    <?php endif; ?>
  <?php endif; ?>
</div>

<!-- Add Client Modal -->
<div class="modal fade" id="addClientModal" tabindex="-1" aria-labelledby="addClientModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="addClientModalLabel">
          <i class="fas fa-user-plus"></i>
          Ajouter un Nouveau Client
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form id="addClientForm" method="POST" action="<?= route('/admin/clients/create') ?>">
        <div class="modal-body">
          <div class="row">
            <div class="col-md-6">
              <div class="form-group mb-3">
                <label for="clientNom" class="form-label">
                  <i class="fas fa-user"></i>
                  Nom <span class="text-danger">*</span>
                </label>
                <input type="text" class="form-control" id="clientNom" name="nom" required>
                <div class="invalid-feedback">
                  Veuillez saisir le nom du client.
                </div>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group mb-3">
                <label for="clientPrenom" class="form-label">
                  <i class="fas fa-user"></i>
                  Prénom <span class="text-danger">*</span>
                </label>
                <input type="text" class="form-control" id="clientPrenom" name="prenom" required>
                <div class="invalid-feedback">
                  Veuillez saisir le prénom du client.
                </div>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-md-6">
              <div class="form-group mb-3">
                <label for="clientEmail" class="form-label">
                  <i class="fas fa-envelope"></i>
                  Email <span class="text-danger">*</span>
                </label>
                <input type="email" class="form-control" id="clientEmail" name="email" required>
                <div class="invalid-feedback">
                  Veuillez saisir un email valide.
                </div>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group mb-3">
                <label for="clientPassword" class="form-label">
                  <i class="fas fa-lock"></i>
                  Mot de passe <span class="text-danger">*</span>
                </label>
                <input type="password" class="form-control" id="clientPassword" name="password" required minlength="6">
                <div class="invalid-feedback">
                  Le mot de passe doit contenir au moins 6 caractères.
                </div>
                <small class="form-text text-muted">Minimum 6 caractères</small>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-12">
              <div class="form-group mb-3">
                <label for="clientUserId" class="form-label">
                  <i class="fas fa-id-card"></i>
                  Utilisateur <span class="text-danger">*</span>
                </label>
                <select class="form-control" id="clientUserId" name="user_id" required>
                  <option value="">Sélectionner un utilisateur...</option>
                </select>
                <div class="invalid-feedback">
                  Veuillez sélectionner un utilisateur.
                </div>
                <small class="form-text text-muted">Utilisateur associé dans le système</small>
              </div>
            </div>
          </div>
          <div class="alert alert-info">
            <i class="fas fa-info-circle"></i>
            <strong>Information:</strong> Un nouveau client sera créé avec les informations fournies. Assurez-vous que l'email n'existe pas déjà dans le système.
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
            <i class="fas fa-times"></i>
            Annuler
          </button>
          <button type="submit" class="btn btn-primary" id="submitClientBtn">
            <i class="fas fa-save"></i>
            <span class="btn-text">Créer le Client</span>
            <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<style>

.modal-backdrop {
  display: none !important;
}

#addClientModal {
  z-index: 1056 !important;
}

.clients-dashboard {
  display: flex;
  flex-direction: column;
  gap: 0;
  padding: 0;
}

/* Section Headers */
.section-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  margin-bottom: 2rem;
  padding: 1.5rem;
  background: white;
  border-radius: 16px;
  box-shadow: var(--card-shadow);
}

.header-content {
  display: flex;
  align-items: center;
  gap: 1rem;
}

.back-btn {
  width: 40px;
  height: 40px;
  border-radius: 12px;
  border: 2px solid var(--border-color);
  background: white;
  color: var(--text-secondary);
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  transition: all 0.2s ease;
}

.back-btn:hover {
  border-color: var(--primary-color);
  background: var(--primary-color);
  color: white;
}

.section-title {
  font-size: 1.5rem;
  font-weight: 700;
  color: var(--text-primary);
  margin: 0;
  display: flex;
  align-items: center;
  gap: 0.5rem;
}

.section-subtitle {
  font-size: 0.875rem;
  color: var(--text-secondary);
  margin: 0;
}

.header-actions {
  display: flex;
  gap: 0.75rem;
}

.action-btn {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  padding: 0.75rem 1.5rem;
  border: 2px solid var(--border-color);
  background: white;
  border-radius: 12px;
  font-size: 0.875rem;
  font-weight: 600;
  color: var(--text-secondary);
  cursor: pointer;
  transition: all 0.2s ease;
}

.action-btn.primary {
  border-color: var(--primary-color);
  background: var(--primary-color);
  color: white;
}

.action-btn:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}

/* Clients Grid */
.clients-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
  gap: 1.5rem;
  margin-top: 1rem;
}

.client-card {
  background: white;
  border-radius: 16px;
  padding: 1.5rem;
  box-shadow: var(--card-shadow);
  display: flex;
  align-items: center;
  gap: 1rem;
  cursor: pointer;
  transition: all 0.3s ease;
  border: 2px solid transparent;
  text-decoration: none;
  color: inherit;
}

.client-card:hover {
  transform: translateY(-4px);
  box-shadow: var(--card-shadow-hover);
  border-color: var(--primary-color);
  text-decoration: none;
  color: inherit;
}

.client-avatar {
  width: 60px;
  height: 60px;
  border-radius: 16px;
  background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
  display: flex;
  align-items: center;
  justify-content: center;
  color: white;
  font-size: 1.5rem;
}

.client-info {
  flex: 1;
}

.client-name {
  font-size: 1.125rem;
  font-weight: 600;
  color: var(--text-primary);
  margin: 0 0 0.25rem 0;
}

.client-email {
  font-size: 0.875rem;
  color: var(--text-secondary);
  margin: 0 0 0.5rem 0;
}

.client-stats {
  display: flex;
  gap: 1rem;
}

.stat-item {
  font-size: 0.75rem;
  color: var(--text-secondary);
  display: flex;
  align-items: center;
  gap: 0.25rem;
}

.client-arrow {
  color: var(--text-secondary);
  font-size: 1rem;
}

/* Loans List */
.loans-list {
  display: flex;
  flex-direction: column;
  gap: 1rem;
}

.loan-card {
  background: white;
  border-radius: 16px;
  padding: 1.5rem;
  box-shadow: var(--card-shadow);
  cursor: pointer;
  transition: all 0.3s ease;
  border: 2px solid transparent;
  text-decoration: none;
  color: inherit;
}

.loan-card:hover {
  transform: translateY(-2px);
  box-shadow: var(--card-shadow-hover);
  border-color: var(--primary-color);
  text-decoration: none;
  color: inherit;
}

.loan-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  margin-bottom: 1rem;
}

.loan-title {
  font-size: 1.125rem;
  font-weight: 600;
  color: var(--text-primary);
  margin: 0;
}

.loan-status {
  padding: 0.25rem 0.75rem;
  border-radius: 20px;
  font-size: 0.75rem;
  font-weight: 600;
}

.loan-status.approved {
  background: rgba(16, 185, 129, 0.1);
  color: var(--accent-color);
}

.loan-status.pending {
  background: rgba(245, 158, 11, 0.1);
  color: #F59E0B;
}

.loan-status.rejected {
  background: rgba(239, 68, 68, 0.1);
  color: #EF4444;
}

.loan-details {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
  gap: 1rem;
}

.loan-detail {
  text-align: center;
}

.detail-label {
  font-size: 0.75rem;
  color: var(--text-secondary);
  margin-bottom: 0.25rem;
}

.detail-value {
  font-size: 1rem;
  font-weight: 600;
  color: var(--text-primary);
}

/* Loan Summary */
.loan-summary {
  background: white;
  border-radius: 16px;
  padding: 1.5rem;
  box-shadow: var(--card-shadow);
  margin-bottom: 1.5rem;
}

.summary-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
  gap: 1.5rem;
}

.summary-item {
  text-align: center;
  padding: 1rem;
  border-radius: 12px;
  background: #F8FAFC;
}

.summary-value {
  font-size: 1.5rem;
  font-weight: 700;
  color: var(--text-primary);
  margin-bottom: 0.25rem;
}

.summary-label {
  font-size: 0.875rem;
  color: var(--text-secondary);
}

/* Payments List */
.payments-list {
  background: white;
  border-radius: 16px;
  box-shadow: var(--card-shadow);
  overflow: hidden;
}

.payments-table {
  width: 100%;
  border-collapse: collapse;
}

.payments-table th {
  background: #F8FAFC;
  padding: 1rem;
  text-align: left;
  font-weight: 600;
  color: var(--text-primary);
  border-bottom: 2px solid var(--border-color);
}

.payments-table td {
  padding: 1rem;
  border-bottom: 1px solid var(--border-color);
  color: var(--text-secondary);
}

.payments-table tr:hover {
  background: #F8FAFC;
}

.payment-status {
  padding: 0.25rem 0.75rem;
  border-radius: 20px;
  font-size: 0.75rem;
  font-weight: 600;
}

.payment-status.paid {
  background: rgba(16, 185, 129, 0.1);
  color: var(--accent-color);
}

.payment-status.pending {
  background: rgba(245, 158, 11, 0.1);
  color: #F59E0B;
}

.payment-status.overdue {
  background: rgba(239, 68, 68, 0.1);
  color: #EF4444;
}

.pay-btn {
  padding: 0.5rem 1rem;
  background: linear-gradient(135deg, var(--primary-color), var(--primary-dark));
  color: white;
  border: none;
  border-radius: 8px;
  font-size: 0.75rem;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.2s ease;
}

.pay-btn:hover {
  transform: translateY(-1px);
  box-shadow: 0 2px 8px rgba(59, 130, 246, 0.3);
}

.pay-btn:disabled {
  background: #E5E7EB;
  color: var(--text-secondary);
  cursor: not-allowed;
  transform: none;
  box-shadow: none;
}

/* Empty State */
.empty-state {
  text-align: center;
  padding: 3rem;
  color: var(--text-secondary);
}

.empty-state i {
  font-size: 3rem;
  margin-bottom: 1rem;
  opacity: 0.5;
}

.empty-state h3 {
  font-size: 1.25rem;
  margin-bottom: 0.5rem;
}

/* Alerts */
.alert {
  margin-bottom: 1.5rem;
  padding: 1rem 1.5rem;
  border-radius: 12px;
  border: none;
  display: flex;
  align-items: center;
  gap: 0.75rem;
}

.alert-success {
  background: rgba(16, 185, 129, 0.1);
  color: var(--accent-color);
  border-left: 4px solid var(--accent-color);
}

.alert-danger {
  background: rgba(239, 68, 68, 0.1);
  color: #EF4444;
  border-left: 4px solid #EF4444;
}

.btn-close {
  background: none;
  border: none;
  font-size: 1.2rem;
  cursor: pointer;
  margin-left: auto;
  opacity: 0.7;
}

.btn-close:hover {
  opacity: 1;
}

/* Restricted Loan Cards */
.loan-card.loan-refused,
.loan-card.loan-pending {
  cursor: not-allowed;
  opacity: 0.7;
  border: 2px solid transparent;
}

.loan-card.loan-refused {
  background: linear-gradient(135deg, #FEF2F2 0%, #FECACA 100%);
  border-color: #EF4444;
}

.loan-card.loan-pending {
  background: linear-gradient(135deg, #FFFBEB 0%, #FED7AA 100%);
  border-color: #F59E0B;
}

.loan-card.loan-refused:hover,
.loan-card.loan-pending:hover {
  transform: none;
  box-shadow: var(--card-shadow);
  border-color: #EF4444;
}

.loan-card.loan-pending:hover {
  border-color: #F59E0B;
}

.loan-notice {
  margin-top: 1rem;
  padding: 0.75rem;
  border-radius: 8px;
  background: rgba(255, 255, 255, 0.8);
  border: 1px solid currentColor;
  display: flex;
  align-items: center;
  gap: 0.5rem;
  font-size: 0.875rem;
  font-weight: 500;
}

.loan-card.loan-refused .loan-notice {
  color: #EF4444;
  border-color: #EF4444;
}

.loan-card.loan-pending .loan-notice {
  color: #F59E0B;
  border-color: #F59E0B;
}

/* Access Denied Section */
.access-denied-section {
  display: flex;
  flex-direction: column;
  gap: 1.5rem;
}

.access-denied-card {
  background: white;
  border-radius: 16px;
  box-shadow: var(--card-shadow);
  padding: 3rem;
  text-align: center;
  border: 2px solid #EF4444;
}

.denied-icon {
  width: 80px;
  height: 80px;
  border-radius: 50%;
  background: linear-gradient(135deg, #EF4444, #DC2626);
  display: flex;
  align-items: center;
  justify-content: center;
  margin: 0 auto 1.5rem auto;
  color: white;
  font-size: 2rem;
}

.denied-content h3 {
  font-size: 1.5rem;
  font-weight: 700;
  color: var(--text-primary);
  margin-bottom: 1rem;
}

.denied-content p {
  font-size: 1rem;
  color: var(--text-secondary);
  margin-bottom: 0.5rem;
  line-height: 1.6;
}

.denied-content p:last-child {
  margin-bottom: 0;
}

.denied-actions {
  margin-top: 2rem;
}

.btn-back {
  display: inline-flex;
  align-items: center;
  gap: 0.5rem;
  padding: 0.75rem 1.5rem;
  background: linear-gradient(135deg, var(--primary-color), var(--primary-dark));
  color: white;
  text-decoration: none;
  border-radius: 12px;
  font-weight: 600;
  transition: all 0.2s ease;
}

.btn-back:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
  text-decoration: none;
  color: white;
}

/* Update subtitle for loans section */
.loans-section .section-subtitle {
  color: var(--text-secondary);
}

/* Modal Styles */
.modal-content {
  border-radius: 16px;
  box-shadow: var(--card-shadow-hover);
  border: none;
}

.modal-header {
  background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
  color: white;
  border-radius: 16px 16px 0 0;
  border-bottom: none;
}

.modal-title {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  font-weight: 600;
}

.btn-close {
  filter: invert(1);
}

.modal-body {
  padding: 2rem;
}

.form-group {
  position: relative;
}

.form-label {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  font-weight: 600;
  color: var(--text-primary);
  margin-bottom: 0.5rem;
}

.form-control {
  border-radius: 12px;
  border: 2px solid var(--border-color);
  padding: 0.75rem 1rem;
  font-size: 0.875rem;
  transition: all 0.2s ease;
}

.form-control:focus {
  border-color: var(--primary-color);
  box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

.form-control.is-invalid {
  border-color: #EF4444;
}

.invalid-feedback {
  display: block;
  width: 100%;
  margin-top: 0.25rem;
  font-size: 0.75rem;
  color: #EF4444;
}

.text-danger {
  color: #EF4444 !important;
}

.alert {
  border-radius: 12px;
  border: none;
  padding: 1rem;
  margin-top: 1rem;
}

.alert-info {
  background: rgba(59, 130, 246, 0.1);
  color: var(--primary-color);
  border-left: 4px solid var(--primary-color);
}

.modal-footer {
  padding: 1.5rem 2rem 2rem;
  border-top: none;
  display: flex;
  gap: 1rem;
  justify-content: flex-end;
}

.btn {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  padding: 0.75rem 1.5rem;
  border-radius: 12px;
  font-weight: 600;
  font-size: 0.875rem;
  transition: all 0.2s ease;
  border: none;
}

.btn-secondary {
  background: #6B7280;
  color: white;
}

.btn-secondary:hover {
  background: #4B5563;
  transform: translateY(-1px);
}

.btn-primary {
  background: linear-gradient(135deg, var(--primary-color), var(--primary-dark));
  color: white;
}

.btn-primary:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
}

.btn-primary:disabled {
  opacity: 0.6;
  cursor: not-allowed;
  transform: none;
}

.spinner-border-sm {
  width: 1rem;
  height: 1rem;
}

/* Form validation styles */
.was-validated .form-control:valid {
  border-color: var(--accent-color);
}

.was-validated .form-control:invalid {
  border-color: #EF4444;
}

/* Success/Error Alert Styles */
.alert-dismissible {
  position: relative;
  padding-right: 4rem;
}

.alert-dismissible .btn-close {
  position: absolute;
  top: 0;
  right: 0;
  z-index: 2;
  padding: 1rem;
  filter: none;
  opacity: 0.7;
}

.alert-dismissible .btn-close:hover {
  opacity: 1;
}

.alert-success {
  background: rgba(16, 185, 129, 0.1);
  color: var(--accent-color);
  border-left: 4px solid var(--accent-color);
}

.alert-danger {
  background: rgba(239, 68, 68, 0.1);
  color: #EF4444;
  border-left: 4px solid #EF4444;
}
</style>

<script>
let currentClient = null;
let currentLoan = null;
let allPrets = <?= json_encode($prets ?? []) ?>;
let allClients = <?= json_encode($clients ?? []) ?>;

async function showClientLoans(client) {
  currentClient = client;
  
  // Update UI
  document.getElementById('currentClientName').textContent = client.nom + ' ' + client.prenom;
  document.getElementById('clientsSection').style.display = 'none';
  document.getElementById('loansSection').style.display = 'block';
  document.getElementById('paymentsSection').style.display = 'none';
  
  // Filter loans for this client
  const clientLoans = allPrets.filter(pret => pret.client_id == client.id);
  
  const loansList = document.getElementById('loansList');
  loansList.innerHTML = '';
  
  if (clientLoans.length === 0) {
    loansList.innerHTML = `
      <div class="empty-state">
        <i class="fas fa-handshake"></i>
        <h3>Aucun prêt trouvé</h3>
        <p>Ce client n'a pas encore de prêts</p>
      </div>
    `;
    return;
  }

  for(const loan of clientLoans) {
    const loanCard = await createLoanCard(loan);
    loansList.appendChild(loanCard);
  }
}

let loanMontantDuree = 0;

async function showLoanPayments(loan) {
  currentLoan = loan;
  
  // Update UI
  document.getElementById('currentLoanId').textContent = loan.id;
  document.getElementById('loansSection').style.display = 'none';
  document.getElementById('paymentsSection').style.display = 'block';
  
  // Create loan summary
  const loanSummary = document.getElementById('loanSummary');

  // Load payments using the new API
  await loadPaymentsFromAPI(loan.id);

  const paiementAnnuel = loan.montant / loan.duree;
  
  loanSummary.innerHTML = `
    <div class="summary-grid">
      <div class="summary-item">
        <div class="summary-value">${new Intl.NumberFormat('fr-FR').format(loan.montant)} Ar</div>
        <div class="summary-label">Montant total</div>
      </div>
      <div class="summary-item">
        <div class="summary-value">${loan.duree} an(s)</div>
        <div class="summary-label">Durée</div>
      </div>
      <div class="summary-item">
        <div class="summary-value">${new Intl.NumberFormat('fr-FR').format(loanMontantDuree)} Ar</div>
        <div class="summary-label">Paiement annuel</div>
      </div>
      <div class="summary-item">
        <div class="summary-value">${new Date(loan.date_creation).toLocaleDateString('fr-FR')}</div>
        <div class="summary-label">Date de création</div>
      </div>
    </div>
  `;
}

async function loadPaymentsFromAPI(loanId) {
  try {
    // Fetch payment schedule from API
    const scheduleResponse = await fetch(`<?= $config['api']['base_url'] ?>/prets/${loanId}/payments`);
    const paymentSchedule = await scheduleResponse.json();

    loanMontantDuree = paymentSchedule[0].montant;
    
    // Fetch existing payments
    const paymentsResponse = await fetch(`<?= $config['api']['base_url'] ?>/pret-retour-historiques`);
    const allPayments = await paymentsResponse.json();
    const loanPayments = allPayments.filter(payment => payment.pret_id == loanId);
    
    if (scheduleResponse.ok && paymentSchedule && Array.isArray(paymentSchedule)) {
      displayPaymentsTable(paymentSchedule, loanPayments);
    } else {
      throw new Error('Invalid payment schedule data');
    }
    
  } catch (error) {
    console.error('Error loading payments:', error);
    document.getElementById('paymentsList').innerHTML = `
      <div class="empty-state">
        <i class="fas fa-exclamation-triangle"></i>
        <h3>Erreur de chargement</h3>
        <p>Impossible de charger les paiements: ${error.message}</p>
      </div>
    `;
  }
}

function displayPaymentsTable(schedule, existingPayments) {
  const paymentsList = document.getElementById('paymentsList');
  
  let tableHTML = `
    <table class="payments-table">
      <thead>
        <tr>
          <th>Année #</th>
          <th>Date d'échéance</th>
          <th>Montant</th>
          <th>Statut</th>
          <th>Date de paiement</th>
          <th>Action</th>
        </tr>
      </thead>
      <tbody>
  `;
  
  let lastPaidYear = 0;
  
  // Find the last paid year
  schedule.forEach((payment, index) => {
    if (payment.isPayer) {
      lastPaidYear = index + 1;
    }
  });
  
  schedule.forEach((payment, index) => {
    const yearNumber = index + 1;
    const dueDate = new Date(payment.date_echeance);
    
    // Find corresponding actual payment
    const actualPayment = existingPayments.find(p => {
      const paymentDate = new Date(p.date_retour * 1000);
      return paymentDate.getFullYear() === parseInt(payment.annee);
    });
    
    let statusClass = 'pending';
    let statusText = 'En attente';
    let paymentDate = '-';
    let actionButton = '';
    
    if (payment.isPayer || actualPayment) {
      statusClass = 'paid';
      statusText = 'Payé';
      if (actualPayment) {
        paymentDate = new Date(actualPayment.date_retour * 1000).toLocaleDateString('fr-FR');
      }
      lastPaidYear = Math.max(lastPaidYear, yearNumber);
    } else {
      // Removed overdue logic - only pending status now
      statusClass = 'pending';
      statusText = 'En attente';
      
      // Can only pay if this is the next year after last payment
      if (yearNumber <= lastPaidYear + 1) {
        // Format current date as YYYY-MM-DD for date_retour
        const currentDate = dueDate;
        const formattedDate = currentDate.getFullYear() + '-' + 
          String(currentDate.getMonth()).padStart(2, '0') + '-' + 
          String(currentDate.getDate()).padStart(2, '0');
        
        actionButton = `
          <form method="POST" action="<?= $config['api']['base_url'] ?>/pret-retour-historiques" style="display: inline;">
            <input type="hidden" name="pret_id" value="${currentLoan.id}">
            <input type="hidden" name="montant" value="${payment.montant}">
            <input type="hidden" name="date_retour" value="${formattedDate}">
            <button type="submit" class="pay-btn" onclick="return confirm('Confirmer le paiement pour l\\'année ${yearNumber} ?\\nMontant: ${new Intl.NumberFormat('fr-FR').format(payment.montant)} Ar')">
              Payer
            </button>
          </form>
        `;
      } else if (yearNumber > lastPaidYear + 1) {
        actionButton = `<button class="pay-btn" disabled title="Payez d'abord les années précédentes">Payer</button>`;
      }
    }
    
    tableHTML += `
      <tr>
        <td>Année ${yearNumber}</td>
        <td>${dueDate.toLocaleDateString('fr-FR')}</td>
        <td>${new Intl.NumberFormat('fr-FR').format(payment.montant)} Ar</td>
        <td><span class="payment-status ${statusClass}">${statusText}</span></td>
        <td>${paymentDate}</td>
        <td>${actionButton}</td>
      </tr>
    `;
  });
  
  tableHTML += `
      </tbody>
    </table>
  `;
  
  paymentsList.innerHTML = tableHTML;
}

async function createLoanCard(loan) {
  const card = document.createElement('div');
  card.className = 'loan-card';
  card.onclick = () => showLoanPayments(loan);
  
  let statusClass = 'pending';
  let statusText = 'En attente';
  
  if (loan.date_acceptation) {
    statusClass = 'approved';
    statusText = 'Approuvé';
  } else if (loan.date_refus) {
    statusClass = 'rejected';
    statusText = 'Refusé';
  }

  let a = 0;

  try {
    // Fetch payment schedule from API
    const scheduleResponse = await fetch(`<?= $config['api']['base_url'] ?>/prets/${loan.id}/payments`);
    const paymentSchedule = await scheduleResponse.json();
    a = paymentSchedule[0].montant;
  } catch (e) {
    console.error(e.message);
  }
  
  const paiementAnnuel = a;
  
  card.innerHTML = `
    <div class="loan-header">
      <h3 class="loan-title">Prêt #${loan.id}</h3>
      <span class="loan-status ${statusClass}">${statusText}</span>
    </div>
    <div class="loan-details">
      <div class="loan-detail">
        <div class="detail-label">Montant</div>
        <div class="detail-value">${new Intl.NumberFormat('fr-FR').format(loan.montant)} Ar</div>
      </div>
      <div class="loan-detail">
        <div class="detail-label">Durée</div>
        <div class="detail-value">${loan.duree} an(s)</div>
      </div>
      <div class="loan-detail">
        <div class="detail-label">Paiement annuel</div>
        <div class="detail-value">${new Intl.NumberFormat('fr-FR').format(paiementAnnuel)} Ar</div>
      </div>
      <div class="loan-detail">
        <div class="detail-label">Date création</div>
        <div class="detail-value">${new Date(loan.date_creation).toLocaleDateString('fr-FR')}</div>
      </div>
    </div>
  `;
  
  return card;
}

function showClientsSection() {
  document.getElementById('clientsSection').style.display = 'block';
  document.getElementById('loansSection').style.display = 'none';
  document.getElementById('paymentsSection').style.display = 'none';
}

function showLoansSection() {
  document.getElementById('loansSection').style.display = 'block';
  document.getElementById('paymentsSection').style.display = 'none';
}

function showAddClientModal() {
  const modal = new bootstrap.Modal(document.getElementById('addClientModal'));
  modal.show();
}

// Handle form submission
document.getElementById('addClientForm').addEventListener('submit', function(e) {
  e.preventDefault();
  
  const form = this;
  const submitBtn = document.getElementById('submitClientBtn');
  const btnText = submitBtn.querySelector('.btn-text');
  const spinner = submitBtn.querySelector('.spinner-border');
  
  // Validate form
  if (!form.checkValidity()) {
    form.classList.add('was-validated');
    return;
  }
  
  // Show loading state
  submitBtn.disabled = true;
  btnText.textContent = 'Création...';
  spinner.classList.remove('d-none');
  
  // Submit form
  form.submit();
});

// Load users when modal is shown
document.getElementById('addClientModal').addEventListener('show.bs.modal', function() {
  loadUsers();
});

// Function to load users from API
async function loadUsers() {
  try {
    const response = await fetch('<?= $config['api']['base_url'] ?>/users');
    const users = await response.json();
    
    const userSelect = document.getElementById('clientUserId');
    userSelect.innerHTML = '<option value="">Sélectionner un utilisateur...</option>';
    
    users.forEach(user => {
      const option = document.createElement('option');
      option.value = user.id;
      option.textContent = user.nom;
      userSelect.appendChild(option);
    });
    
    // Set default to user ID 2 if it exists
    const defaultOption = userSelect.querySelector('option[value="2"]');
    if (defaultOption) {
      defaultOption.selected = true;
    }
  } catch (error) {
    console.error('Error loading users:', error);
    // Fallback option if API fails
    const userSelect = document.getElementById('clientUserId');
    userSelect.innerHTML = `
      <option value="">Sélectionner un utilisateur...</option>
      <option value="2" selected>Utilisateur par défaut</option>
    `;
  }
}

// Auto-hide alerts after 5 seconds
document.addEventListener('DOMContentLoaded', function() {
  const alerts = document.querySelectorAll('.alert-dismissible');
  alerts.forEach(alert => {
    setTimeout(() => {
      const closeBtn = alert.querySelector('.btn-close');
      if (closeBtn) {
        closeBtn.click();
      }
    }, 5000);
  });
});

// Reset form when modal is hidden
document.getElementById('addClientModal').addEventListener('hidden.bs.modal', function() {
  const form = document.getElementById('addClientForm');
  form.reset();
  form.classList.remove('was-validated');
  
  const submitBtn = document.getElementById('submitClientBtn');
  const btnText = submitBtn.querySelector('.btn-text');
  const spinner = submitBtn.querySelector('.spinner-border');
  
  submitBtn.disabled = false;
  btnText.textContent = 'Créer le Client';
  spinner.classList.add('d-none');
});

// Email validation
document.getElementById('clientEmail').addEventListener('input', function() {
  const email = this.value;
  const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
  
  if (email && !emailRegex.test(email)) {
    this.setCustomValidity('Veuillez saisir un email valide');
  } else {
    this.setCustomValidity('');
  }
});
</script>

<!-- Include Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
