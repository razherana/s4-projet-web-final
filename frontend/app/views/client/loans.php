<div class="loans-dashboard">
  <!-- Header Section -->
  <section class="loans-header">
    <div class="header-card">
      <div class="header-content">
        <h2 class="header-title">Mes Prêts</h2>
        <p class="header-subtitle">Gérez vos demandes de prêts et suivez vos paiements</p>
      </div>
      <div class="header-actions">
        <a href="<?= route('/client/simulate') ?>" class="action-btn primary">
          <i class="fas fa-plus"></i>
          <span>Nouveau Prêt</span>
        </a>
      </div>
    </div>
  </section>

  <!-- Filters Section -->
  <section class="filters-section">
    <div class="filters-card">
      <div class="filters-grid">
        <div class="filter-group">
          <label for="searchInput" class="filter-label">
            <i class="fas fa-search"></i>
            Rechercher
          </label>
          <input type="text" id="searchInput" class="filter-input" placeholder="Rechercher un prêt...">
        </div>
        
        <div class="filter-group">
          <label for="statusFilter" class="filter-label">
            <i class="fas fa-filter"></i>
            Statut
          </label>
          <select id="statusFilter" class="filter-select">
            <option value="">Tous les statuts</option>
            <option value="approved">Approuvé</option>
            <option value="pending">En attente</option>
            <option value="rejected">Refusé</option>
          </select>
        </div>
        
        <div class="filter-group">
          <label for="amountFilter" class="filter-label">
            <i class="fas fa-money-bill"></i>
            Montant
          </label>
          <select id="amountFilter" class="filter-select">
            <option value="">Tous les montants</option>
            <option value="0-1000000">0 - 1M Ar</option>
            <option value="1000000-5000000">1M - 5M Ar</option>
            <option value="5000000-10000000">5M - 10M Ar</option>
            <option value="10000000+">10M+ Ar</option>
          </select>
        </div>
        
        <div class="filter-group">
          <label for="sortFilter" class="filter-label">
            <i class="fas fa-sort"></i>
            Trier par
          </label>
          <select id="sortFilter" class="filter-select">
            <option value="date_desc">Date (récent)</option>
            <option value="date_asc">Date (ancien)</option>
            <option value="amount_desc">Montant (élevé)</option>
            <option value="amount_asc">Montant (faible)</option>
          </select>
        </div>
      </div>
      
      <div class="filter-actions">
        <button type="button" class="filter-btn reset" onclick="resetFilters()">
          <i class="fas fa-undo"></i>
          Réinitialiser
        </button>
        <button type="button" class="filter-btn apply" onclick="applyFilters()">
          <i class="fas fa-check"></i>
          Appliquer
        </button>
      </div>
    </div>
  </section>

  <!-- Success/Error Messages -->
  <?php if (isset($_GET['success'])): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
      <i class="fas fa-check-circle"></i>
      <?php
        switch ($_GET['success']) {
          case 'loan_created':
            echo 'Demande de prêt créée avec succès !';
            break;
          case 'payment_successful':
            echo 'Paiement enregistré avec succès !';
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
          case 'payment_failed':
            echo 'Échec du paiement. Veuillez réessayer.';
            break;
          case 'payment_error':
            echo 'Erreur lors du traitement du paiement.';
            break;
          case 'missing_data':
            echo 'Données manquantes.';
            break;
          default:
            echo 'Une erreur est survenue.';
        }
      ?>
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
  <?php endif; ?>

  <!-- Loans List Section -->
  <section class="loans-list-section" id="loansSection">
    <div class="loans-container">
      <div class="loans-grid" id="loansGrid">
        <?php if (!empty($prets)): ?>
          <?php foreach ($prets as $pret): ?>
            <?php 
              $isApproved = !empty($pret['date_acceptation']);
              $isRefused = !empty($pret['date_refus']);
              $isPending = !$isApproved && !$isRefused;
              $canAccess = $isApproved && !$isRefused;
              
              $statusClass = $isApproved ? 'approved' : ($isRefused ? 'rejected' : 'pending');
              $statusText = $isApproved ? 'Approuvé' : ($isRefused ? 'Refusé' : 'En attente');
              $cardClass = $isRefused ? 'loan-refused' : ($isPending ? 'loan-pending' : 'loan-approved');
            ?>
            <div class="loan-card <?= $cardClass ?>" 
                 data-loan-id="<?= $pret['id'] ?>"
                 data-status="<?= $statusClass ?>"
                 data-amount="<?= $pret['montant'] ?>"
                 data-date="<?= $pret['date_creation'] ?>"
                 <?= $canAccess ? 'onclick="showLoanPayments(' . htmlspecialchars(json_encode($pret)) . ')"' : '' ?>>
              
              <div class="loan-header">
                <div class="loan-title-section">
                  <h3 class="loan-title">Prêt #<?= $pret['id'] ?></h3>
                  <span class="loan-status <?= $statusClass ?>">
                    <i class="fas <?= $isApproved ? 'fa-check-circle' : ($isRefused ? 'fa-times-circle' : 'fa-clock') ?>"></i>
                    <?= $statusText ?>
                  </span>
                </div>
                <?php if ($canAccess): ?>
                  <div class="loan-action">
                    <i class="fas fa-chevron-right"></i>
                  </div>
                <?php else: ?>
                  <div class="loan-action disabled">
                    <i class="fas fa-lock"></i>
                  </div>
                <?php endif; ?>
              </div>

              <div class="loan-details-grid">
                <div class="loan-detail">
                  <div class="detail-icon">
                    <i class="fas fa-coins"></i>
                  </div>
                  <div class="detail-content">
                    <div class="detail-label">Montant</div>
                    <div class="detail-value"><?= number_format($pret['montant'], 0, ',', ' ') ?> Ar</div>
                  </div>
                </div>
                
                <div class="loan-detail">
                  <div class="detail-icon">
                    <i class="fas fa-calendar-alt"></i>
                  </div>
                  <div class="detail-content">
                    <div class="detail-label">Durée</div>
                    <div class="detail-value"><?= $pret['duree'] ?> an(s)</div>
                  </div>
                </div>
                
                <div class="loan-detail">
                  <div class="detail-icon">
                    <i class="fas fa-credit-card"></i>
                  </div>
                  <div class="detail-content">
                    <div class="detail-label">Paiement annuel</div>
                    <div class="detail-value" id="annual-payment-<?= $pret['id'] ?>">
                      <?= number_format(($pret['montant'] * ($pret['taux_interet'] / 100 + $pret['taux_assurance'] / 100 + 1 / $pret['duree'])), 0, ',', ' ') ?> Ar
                    </div>
                  </div>
                </div>
                
                <div class="loan-detail">
                  <div class="detail-icon">
                    <i class="fas fa-calendar-plus"></i>
                  </div>
                  <div class="detail-content">
                    <div class="detail-label">Date création</div>
                    <div class="detail-value"><?= date('d/m/Y', strtotime($pret['date_creation'])) ?></div>
                  </div>
                </div>
              </div>

              <?php if ($isRefused): ?>
                <div class="loan-notice rejected">
                  <i class="fas fa-info-circle"></i>
                  <span>Ce prêt a été refusé. Aucun paiement n'est requis.</span>
                </div>
              <?php elseif ($isPending): ?>
                <div class="loan-notice pending">
                  <i class="fas fa-clock"></i>
                  <span>Ce prêt est en attente d'approbation.</span>
                </div>
              <?php else: ?>
                <div class="loan-notice approved">
                  <i class="fas fa-hand-point-right"></i>
                  <span>Cliquez pour voir les paiements et effectuer des versements.</span>
                </div>
              <?php endif; ?>
            </div>
          <?php endforeach; ?>
        <?php else: ?>
          <div class="empty-state">
            <div class="empty-icon">
              <i class="fas fa-handshake"></i>
            </div>
            <div class="empty-content">
              <h3>Aucun prêt trouvé</h3>
              <p>Vous n'avez pas encore de demandes de prêts</p>
              <a href="<?= route('/client/simulate') ?>" class="empty-action">
                <i class="fas fa-plus"></i>
                Faire une demande
              </a>
            </div>
          </div>
        <?php endif; ?>
      </div>

      <!-- No Results Message (hidden by default) -->
      <div class="no-results" id="noResults" style="display: none;">
        <div class="no-results-icon">
          <i class="fas fa-search"></i>
        </div>
        <div class="no-results-content">
          <h3>Aucun prêt trouvé</h3>
          <p>Aucun prêt ne correspond à vos critères de recherche</p>
          <button type="button" class="no-results-action" onclick="resetFilters()">
            <i class="fas fa-undo"></i>
            Réinitialiser les filtres
          </button>
        </div>
      </div>
    </div>
  </section>

  <!-- Payments Modal -->
  <div class="payments-modal" id="paymentsModal" style="display: none;">
    <div class="modal-backdrop" onclick="closePaymentsModal()"></div>
    <div class="modal-container">
      <div class="modal-header">
        <div class="modal-title">
          <i class="fas fa-money-bill-wave"></i>
          <span id="modalLoanTitle">Paiements - Prêt #</span>
        </div>
        <div class="modal-actions">
          <button class="modal-action-btn" onclick="exportCurrentLoanPayments()" id="exportBtn">
            <i class="fas fa-file-pdf"></i>
          </button>
          <button class="modal-close" onclick="closePaymentsModal()">
            <i class="fas fa-times"></i>
          </button>
        </div>
      </div>
      
      <div class="modal-body">
        <!-- Loan Summary -->
        <div class="loan-summary" id="loanSummary">
          <!-- Populated by JavaScript -->
        </div>
        
        <!-- Payments Table -->
        <div class="payments-table-container">
          <div class="payments-table-header">
            <h4>Calendrier des Paiements</h4>
            <p>Effectuez vos paiements annuels selon l'échéancier ci-dessous</p>
          </div>
          <div class="payments-table-content" id="paymentsTableContent">
            <!-- Populated by JavaScript -->
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<style>
.loans-dashboard {
  display: flex;
  flex-direction: column;
  gap: 2rem;
  padding: 0;
}

/* Header Section */
.loans-header {
  margin-bottom: 1rem;
}

.header-card {
  background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
  color: white;
  border-radius: 16px;
  padding: 2rem;
  display: flex;
  align-items: center;
  justify-content: space-between;
  position: relative;
  overflow: hidden;
  box-shadow: var(--card-shadow-hover);
}

.header-card::before {
  content: '';
  position: absolute;
  top: 0;
  right: 0;
  width: 150px;
  height: 150px;
  background: rgba(255, 255, 255, 0.1);
  border-radius: 50%;
  transform: translate(50px, -50px);
}

.header-content {
  position: relative;
  z-index: 2;
}

.header-title {
  font-size: 1.75rem;
  font-weight: 700;
  margin: 0 0 0.5rem 0;
}

.header-subtitle {
  font-size: 1rem;
  opacity: 0.9;
  margin: 0;
}

.header-actions {
  position: relative;
  z-index: 2;
}

.action-btn {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  padding: 0.875rem 1.5rem;
  background: rgba(255, 255, 255, 0.2);
  color: white;
  text-decoration: none;
  border-radius: 12px;
  font-weight: 600;
  transition: all 0.2s ease;
  border: 2px solid rgba(255, 255, 255, 0.3);
}

.action-btn:hover {
  background: white;
  color: var(--primary-color);
  text-decoration: none;
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(255, 255, 255, 0.3);
}

/* Filters Section */
.filters-section {
  margin-bottom: 1rem;
}

.filters-card {
  background: white;
  border-radius: 16px;
  padding: 1.5rem;
  box-shadow: var(--card-shadow);
  border: 1px solid var(--border-color);
}

.filters-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
  gap: 1rem;
  margin-bottom: 1rem;
}

.filter-group {
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
}

.filter-label {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  font-size: 0.875rem;
  font-weight: 600;
  color: var(--text-primary);
}

.filter-input,
.filter-select {
  padding: 0.75rem 1rem;
  border: 2px solid var(--border-color);
  border-radius: 12px;
  font-size: 0.875rem;
  transition: all 0.2s ease;
  background: white;
}

.filter-input:focus,
.filter-select:focus {
  outline: none;
  border-color: var(--primary-color);
  box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

.filter-actions {
  display: flex;
  justify-content: flex-end;
  gap: 1rem;
}

.filter-btn {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  padding: 0.75rem 1.5rem;
  border: 2px solid;
  border-radius: 12px;
  font-size: 0.875rem;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.2s ease;
}

.filter-btn.reset {
  border-color: var(--text-secondary);
  background: white;
  color: var(--text-secondary);
}

.filter-btn.reset:hover {
  background: var(--text-secondary);
  color: white;
}

.filter-btn.apply {
  border-color: var(--primary-color);
  background: var(--primary-color);
  color: white;
}

.filter-btn.apply:hover {
  background: var(--primary-dark);
  transform: translateY(-1px);
}

/* Success/Error Messages */
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

/* Loans List Section */
.loans-list-section {
  flex: 1;
}

.loans-container {
  background: white;
  border-radius: 16px;
  padding: 2rem;
  box-shadow: var(--card-shadow);
  min-height: 400px;
}

.loans-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(400px, 1fr));
  gap: 1.5rem;
}

/* Loan Cards */
.loan-card {
  border: 2px solid var(--border-color);
  border-radius: 16px;
  padding: 1.5rem;
  transition: all 0.3s ease;
  position: relative;
  overflow: hidden;
  background: white;
}

.loan-card.loan-approved {
  cursor: pointer;
  border-color: rgba(16, 185, 129, 0.3);
  background: linear-gradient(135deg, #F0FDF4 0%, #ECFDF5 100%);
}

.loan-card.loan-approved:hover {
  transform: translateY(-4px);
  box-shadow: var(--card-shadow-hover);
  border-color: var(--accent-color);
}

.loan-card.loan-pending {
  border-color: rgba(245, 158, 11, 0.5);
  background: linear-gradient(135deg, #FFFBEB 0%, #FEF3C7 100%);
  cursor: not-allowed;
}

.loan-card.loan-refused {
  border-color: rgba(239, 68, 68, 0.5);
  background: linear-gradient(135deg, #FEF2F2 0%, #FECACA 100%);
  cursor: not-allowed;
}

.loan-header {
  display: flex;
  align-items: flex-start;
  justify-content: space-between;
  margin-bottom: 1.5rem;
}

.loan-title-section {
  flex: 1;
}

.loan-title {
  font-size: 1.125rem;
  font-weight: 700;
  color: var(--text-primary);
  margin: 0 0 0.5rem 0;
}

.loan-status {
  display: inline-flex;
  align-items: center;
  gap: 0.5rem;
  padding: 0.375rem 0.875rem;
  border-radius: 20px;
  font-size: 0.75rem;
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: 0.025em;
}

.loan-status.approved {
  background: rgba(16, 185, 129, 0.15);
  color: #065F46;
  border: 1px solid rgba(16, 185, 129, 0.3);
}

.loan-status.pending {
  background: rgba(245, 158, 11, 0.15);
  color: #92400E;
  border: 1px solid rgba(245, 158, 11, 0.3);
}

.loan-status.rejected {
  background: rgba(239, 68, 68, 0.15);
  color: #991B1B;
  border: 1px solid rgba(239, 68, 68, 0.3);
}

.loan-action {
  width: 32px;
  height: 32px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  color: var(--text-secondary);
  transition: all 0.2s ease;
}

.loan-approved .loan-action {
  background: rgba(16, 185, 129, 0.1);
  color: var(--accent-color);
}

.loan-action.disabled {
  opacity: 0.5;
  cursor: not-allowed;
}

/* Loan Details Grid */
.loan-details-grid {
  display: grid;
  grid-template-columns: repeat(2, 1fr);
  gap: 1rem;
  margin-bottom: 1rem;
}

.loan-detail {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  padding: 0.75rem;
  border-radius: 12px;
  background: rgba(255, 255, 255, 0.7);
  border: 1px solid rgba(0, 0, 0, 0.05);
}

.detail-icon {
  width: 32px;
  height: 32px;
  border-radius: 8px;
  background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
  color: white;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 0.875rem;
  flex-shrink: 0;
}

.detail-content {
  flex: 1;
}

.detail-label {
  font-size: 0.75rem;
  color: var(--text-secondary);
  margin-bottom: 0.125rem;
  font-weight: 500;
}

.detail-value {
  font-size: 0.875rem;
  font-weight: 600;
  color: var(--text-primary);
  line-height: 1.2;
}

/* Loan Notice */
.loan-notice {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  padding: 0.75rem;
  border-radius: 8px;
  font-size: 0.875rem;
  font-weight: 500;
  border: 1px solid currentColor;
}

.loan-notice.approved {
  background: rgba(16, 185, 129, 0.1);
  color: #065F46;
  border-color: rgba(16, 185, 129, 0.3);
}

.loan-notice.pending {
  background: rgba(245, 158, 11, 0.1);
  color: #92400E;
  border-color: rgba(245, 158, 11, 0.3);
}

.loan-notice.rejected {
  background: rgba(239, 68, 68, 0.1);
  color: #991B1B;
  border-color: rgba(239, 68, 68, 0.3);
}

/* Empty State */
.empty-state {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  text-align: center;
  padding: 4rem 2rem;
  min-height: 300px;
}

.empty-icon {
  width: 80px;
  height: 80px;
  border-radius: 50%;
  background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
  color: white;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 2rem;
  margin-bottom: 1.5rem;
}

.empty-content h3 {
  font-size: 1.25rem;
  font-weight: 600;
  color: var(--text-primary);
  margin: 0 0 0.5rem 0;
}

.empty-content p {
  font-size: 0.875rem;
  color: var(--text-secondary);
  margin: 0 0 1.5rem 0;
}

.empty-action {
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

.empty-action:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
  text-decoration: none;
  color: white;
}

/* No Results */
.no-results {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  text-align: center;
  padding: 3rem 2rem;
}

.no-results-icon {
  width: 64px;
  height: 64px;
  border-radius: 50%;
  background: var(--light-bg);
  color: var(--text-secondary);
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 1.5rem;
  margin-bottom: 1rem;
}

.no-results-content h3 {
  font-size: 1.125rem;
  font-weight: 600;
  color: var(--text-primary);
  margin: 0 0 0.5rem 0;
}

.no-results-content p {
  font-size: 0.875rem;
  color: var(--text-secondary);
  margin: 0 0 1rem 0;
}

.no-results-action {
  display: inline-flex;
  align-items: center;
  gap: 0.5rem;
  padding: 0.625rem 1.25rem;
  background: white;
  color: var(--primary-color);
  border: 2px solid var(--primary-color);
  border-radius: 12px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.2s ease;
}

.no-results-action:hover {
  background: var(--primary-color);
  color: white;
}

/* Payments Modal */
.payments-modal {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  z-index: 1050;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 1rem;
}

.modal-backdrop {
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(0, 0, 0, 0.5);
  backdrop-filter: blur(4px);
  z-index: 0;
}

.modal-container {
  position: relative;
  width: 100%;
  max-width: 900px;
  max-height: 90vh;
  background: white;
  border-radius: 16px;
  box-shadow: var(--card-shadow-hover);
  overflow: hidden;
  display: flex;
  flex-direction: column;
  animation: modalSlideIn 0.3s ease-out;
  z-index: 1;
}

@keyframes modalSlideIn {
  from {
    opacity: 0;
    transform: translateY(-20px) scale(0.95);
  }
  to {
    opacity: 1;
    transform: translateY(0) scale(1);
  }
}

.modal-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 1.5rem;
  border-bottom: 1px solid var(--border-color);
  background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
  color: white;
}

.modal-title {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  font-size: 1.125rem;
  font-weight: 600;
}

.modal-close {
  width: 32px;
  height: 32px;
  border: none;
  background: rgba(255, 255, 255, 0.2);
  color: white;
  border-radius: 50%;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: all 0.2s ease;
}

.modal-close:hover {
  background: rgba(255, 255, 255, 0.3);
}

.modal-body {
  flex: 1;
  overflow-y: auto;
  padding: 1.5rem;
  display: flex;
  flex-direction: column;
  gap: 1.5rem;
}

/* Modal Actions */
.modal-actions {
  display: flex;
  align-items: center;
  gap: 0.5rem;
}

.modal-action-btn {
  width: 32px;
  height: 32px;
  border: none;
  background: rgba(255, 255, 255, 0.2);
  color: white;
  border-radius: 50%;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: all 0.2s ease;
}

.modal-action-btn:hover {
  background: rgba(255, 255, 255, 0.3);
  transform: scale(1.1);
}

/* Loan Summary in Modal */
.loan-summary {
  background: #F8FAFC;
  border-radius: 12px;
  padding: 1.5rem;
  border: 1px solid var(--border-color);
}

.summary-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
  gap: 1rem;
}

.summary-item {
  text-align: center;
  padding: 1rem;
  background: white;
  border-radius: 8px;
  border: 1px solid var(--border-color);
}

.summary-value {
  font-size: 1.25rem;
  font-weight: 700;
  color: var(--text-primary);
  margin-bottom: 0.25rem;
}

.summary-label {
  font-size: 0.75rem;
  color: var(--text-secondary);
  font-weight: 500;
}

/* Payments Table */
.payments-table-container {
  border: 1px solid var(--border-color);
  border-radius: 12px;
  overflow: hidden;
}

.payments-table-header {
  padding: 1rem;
  background: #F8FAFC;
  border-bottom: 1px solid var(--border-color);
}

.payments-table-header h4 {
  font-size: 1rem;
  font-weight: 600;
  color: var(--text-primary);
  margin: 0 0 0.25rem 0;
}

.payments-table-header p {
  font-size: 0.875rem;
  color: var(--text-secondary);
  margin: 0;
}

.payments-table-content {
  overflow-x: auto;
}

/* Responsive Design */
@media (max-width: 768px) {
  .loans-dashboard {
    gap: 1.5rem;
  }
  
  .header-card {
    flex-direction: column;
    text-align: center;
    gap: 1rem;
  }
  
  .filters-grid {
    grid-template-columns: 1fr;
  }
  
  .loans-grid {
    grid-template-columns: 1fr;
  }
  
  .loan-details-grid {
    grid-template-columns: 1fr;
  }
  
  .modal-container {
    margin: 0.5rem;
    max-height: 95vh;
  }
  
  .summary-grid {
    grid-template-columns: repeat(2, 1fr);
  }
}

@media (max-width: 480px) {
  .header-card {
    padding: 1.5rem;
  }
  
  .filters-card {
    padding: 1rem;
  }
  
  .loans-container {
    padding: 1rem;
  }
  
  .loan-card {
    padding: 1rem;
  }
  
  .summary-grid {
    grid-template-columns: 1fr;
  }
  
  .modal-body {
    padding: 1rem;
  }
}
</style>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>

<script>
let allLoans = <?= json_encode($prets) ?>;
let currentLoanForExport = null;

// Basic filter functionality
function applyFilters() {
  const search = document.getElementById('searchInput').value.toLowerCase();
  const status = document.getElementById('statusFilter').value;
  const amount = document.getElementById('amountFilter').value;
  const sort = document.getElementById('sortFilter').value;

  let filtered = allLoans.filter(loan => {
    // Search filter
    if (search && !loan.id.toString().includes(search) && !loan.montant.toString().includes(search)) {
      return false;
    }
    
    // Status filter
    if (status) {
      const isApproved = !!loan.date_acceptation;
      const isRefused = !!loan.date_refus;
      const isPending = !isApproved && !isRefused;
      
      if ((status === 'approved' && !isApproved) ||
          (status === 'pending' && !isPending) ||
          (status === 'rejected' && !isRefused)) {
        return false;
      }
    }
    
    // Amount filter
    if (amount) {
      const amt = parseFloat(loan.montant);
      if ((amount === '0-1000000' && (amt < 0 || amt > 1000000)) ||
          (amount === '1000000-5000000' && (amt <= 1000000 || amt > 5000000)) ||
          (amount === '5000000-10000000' && (amt <= 5000000 || amt > 10000000)) ||
          (amount === '10000000+' && amt <= 10000000)) {
        return false;
      }
    }
    
    return true;
  });

  // Sort
  filtered.sort((a, b) => {
    switch (sort) {
      case 'date_asc': return new Date(a.date_creation) - new Date(b.date_creation);
      case 'amount_desc': return b.montant - a.montant;
      case 'amount_asc': return a.montant - b.montant;
      default: return new Date(b.date_creation) - new Date(a.date_creation);
    }
  });

  // Show/hide cards
  document.querySelectorAll('.loan-card').forEach(card => {
    const loanId = parseInt(card.dataset.loanId);
    card.style.display = filtered.some(loan => loan.id == loanId) ? 'block' : 'none';
  });
  
  // Show/hide no results
  document.getElementById('noResults').style.display = filtered.length ? 'none' : 'flex';
  document.getElementById('loansGrid').style.display = filtered.length ? 'grid' : 'none';
}

function resetFilters() {
  document.getElementById('searchInput').value = '';
  document.getElementById('statusFilter').value = '';
  document.getElementById('amountFilter').value = '';
  document.getElementById('sortFilter').value = 'date_desc';
  applyFilters();
}

// Modal functions
function showLoanPayments(loan) {
  currentLoanForExport = loan;
  document.getElementById('modalLoanTitle').textContent = `Paiements - Prêt #${loan.id}`;
  document.getElementById('paymentsModal').style.display = 'flex';
  document.body.style.overflow = 'hidden';
  loadPayments(loan);
}

function closePaymentsModal() {
  document.getElementById('paymentsModal').style.display = 'none';
  document.body.style.overflow = 'auto';
}

async function loadPayments(loan) {
  try {
    const [scheduleRes, paymentsRes] = await Promise.all([
      fetch(`<?= $config['api']['base_url'] ?>/prets/${loan.id}/payments`),
      fetch(`<?= $config['api']['base_url'] ?>/pret-retour-historiques`)
    ]);
    
    const schedule = await scheduleRes.json();
    const allPayments = await paymentsRes.json();
    const loanPayments = allPayments.filter(p => p.pret_id == loan.id);
    
    updateSummary(loan, schedule);
    updateTable(schedule, loanPayments, loan);
  } catch (error) {
    document.getElementById('paymentsTableContent').innerHTML = 
      '<div style="padding: 2rem; text-align: center;">Erreur de chargement</div>';
  }
}

function updateSummary(loan, schedule) {
  const payment = schedule.length ? schedule[0].montant : (loan.montant / loan.duree);
  document.getElementById('loanSummary').innerHTML = `
    <div class="summary-grid">
      <div class="summary-item">
        <div class="summary-value">${loan.montant.toLocaleString('fr-FR')} Ar</div>
        <div class="summary-label">Montant Total</div>
      </div>
      <div class="summary-item">
        <div class="summary-value">${loan.duree} an(s)</div>
        <div class="summary-label">Durée</div>
      </div>
      <div class="summary-item">
        <div class="summary-value">${payment.toLocaleString('fr-FR')} Ar</div>
        <div class="summary-label">Paiement Annuel</div>
      </div>
      <div class="summary-item">
        <div class="summary-value">${new Date(loan.date_creation).toLocaleDateString('fr-FR')}</div>
        <div class="summary-label">Date de Création</div>
      </div>
    </div>
  `;
}

function updateTable(schedule, payments, loan) {
  let lastPaid = 0;
  schedule.forEach((p, i) => { if (p.isPayer) lastPaid = i + 1; });
  
  let html = `<table style="width: 100%; border-collapse: collapse;">
    <thead><tr style="background: #F8FAFC;">
      <th style="padding: 1rem; text-align: left;">Année #</th>
      <th style="padding: 1rem; text-align: left;">Date d'échéance</th>
      <th style="padding: 1rem; text-align: left;">Montant</th>
      <th style="padding: 1rem; text-align: left;">Statut</th>
      <th style="padding: 1rem; text-align: left;">Action</th>
    </tr></thead><tbody>`;
  
  schedule.forEach((payment, i) => {
    const year = i + 1;
    const due = new Date(payment.date_echeance);
    const actualPayment = payments.find(p => new Date(p.date_retour).getFullYear() == payment.annee);
    const isPaid = payment.isPayer || actualPayment;
    
    if (isPaid) lastPaid = Math.max(lastPaid, year);
    
    const canPay = !isPaid && year <= lastPaid + 1;
    const action = canPay ? 
      `<form method="POST" action="<?= route('/client/loans') ?>" style="display: inline;" 
         onsubmit="return confirm('Payer ${payment.montant.toLocaleString('fr-FR')} Ar?')">
         <input type="hidden" name="pret_id" value="${loan.id}">
         <input type="hidden" name="montant" value="${payment.montant}">
         <input type="hidden" name="date_retour" value="${due.toISOString().split('T')[0]}">
         <button type="submit" style="padding: 0.5rem 1rem; background: var(--primary-color); 
                color: white; border: none; border-radius: 8px; cursor: pointer;">Payer</button>
       </form>` : 
      `<button disabled style="padding: 0.5rem 1rem; background: #E5E7EB; color: #6B7280; 
              border: none; border-radius: 8px;">Payer</button>`;
    
    html += `<tr>
      <td style="padding: 1rem;">Année ${year}</td>
      <td style="padding: 1rem;">${due.toLocaleDateString('fr-FR')}</td>
      <td style="padding: 1rem;">${payment.montant.toLocaleString('fr-FR')} Ar</td>
      <td style="padding: 1rem;">
        <span style="padding: 0.25rem 0.75rem; border-radius: 20px; font-size: 0.75rem; 
                     background: ${isPaid ? 'rgba(16, 185, 129, 0.1); color: #10B981' : 'rgba(245, 158, 11, 0.1); color: #F59E0B'};">
          ${isPaid ? 'Payé' : 'En attente'}
        </span>
      </td>
      <td style="padding: 1rem;">${action}</td>
    </tr>`;
  });
  
  document.getElementById('paymentsTableContent').innerHTML = html + '</tbody></table>';
}

function exportCurrentLoanPayments() {
  if (!currentLoanForExport) {
    alert('Aucun prêt sélectionné pour l\'export');
    return;
  }
  
  if (!confirm('Exporter les paiements de ce prêt en PDF ?')) {
    return;
  }
  
  const btn = document.getElementById('exportBtn');
  const originalContent = btn.innerHTML;
  btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
  btn.disabled = true;
  
  try {
    const a = document.createElement('a');
    a.download = "export.pdf";
    a.href = '<?= route('/export/loan') ?>/' + currentLoanForExport.id + '/payments';
    a.click();
    
    // Reset button after a delay
    setTimeout(() => {
      btn.innerHTML = originalContent;
      btn.disabled = false;
    }, 2000);
    
  } catch (error) {
    console.error('Export error:', error);
    alert('Erreur lors de l\'export PDF: ' + error.message);
    
    // Reset button
    btn.innerHTML = originalContent;
    btn.disabled = false;
  }
}

// Event listeners
document.addEventListener('DOMContentLoaded', function() {
  // Auto-hide alerts
  document.querySelectorAll('.alert-dismissible').forEach(alert => {
    setTimeout(() => alert.querySelector('.btn-close')?.click(), 5000);
  });
});
</script>