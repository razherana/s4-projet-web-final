<div class="prets-dashboard">
  <!-- Header Section -->
  <div class="section-header">
    <div class="header-content">
      <h2 class="section-title">
        <i class="fas fa-handshake"></i>
        Gestion des Prêts
      </h2>
      <p class="section-subtitle">Gérez les demandes de prêts et suivez leur statut</p>
    </div>
    <div class="header-actions">
      <button class="action-btn primary" onclick="showClientModal()">
        <i class="fas fa-plus"></i>
        Nouveau Prêt
      </button>
    </div>
  </div>

  <!-- Success/Error Messages -->
  <?php if (isset($_GET['success'])): ?>
    <div class="alert alert-success alert-dismissible">
      <i class="fas fa-check-circle"></i>
      <?php
        switch ($_GET['success']) {
          case 'loan_created':
            echo 'Prêt créé avec succès !';
            break;
          default:
            echo 'Opération réussie !';
        }
      ?>
      <button type="button" class="btn-close" onclick="this.parentElement.style.display='none'"></button>
    </div>
  <?php elseif (isset($_GET['error'])): ?>
    <div class="alert alert-danger alert-dismissible">
      <i class="fas fa-exclamation-triangle"></i>
      <?php
        switch ($_GET['error']) {
          case 'missing_data':
            echo 'Données manquantes pour créer le prêt.';
            break;
          case 'loan_creation_failed':
            echo 'Échec de la création du prêt.';
            break;
          case 'loan_creation_error':
            echo 'Erreur lors de la création du prêt.';
            break;
          default:
            echo 'Une erreur est survenue.';
        }
      ?>
      <button type="button" class="btn-close" onclick="this.parentElement.style.display='none'"></button>
    </div>
  <?php endif; ?>

  <!-- Loans List -->
  <div class="loans-grid">
    <?php if (!empty($prets)): ?>
      <?php foreach ($prets as $pret): ?>
        <?php 
          $client = array_filter($clients, function($c) use ($pret) {
            return $c['id'] == $pret['client_id'];
          });
          $client = $client ? array_values($client)[0] : null;
          
          $typePret = array_filter($typePrets, function($tp) use ($pret) {
            return $tp['id'] == $pret['type_pret_id'];
          });
          $typePret = $typePret ? array_values($typePret)[0] : null;
        ?>
        <div class="loan-card">
          <div class="loan-header">
            <div class="loan-info">
              <h3 class="loan-title">Prêt #<?= $pret['id'] ?></h3>
              <p class="loan-client">
                <?= $client ? htmlspecialchars($client['nom'] . ' ' . $client['prenom']) : 'Client inconnu' ?>
              </p>
            </div>
            <span class="loan-status <?= $pret['date_acceptation'] ? 'approved' : ($pret['date_refus'] ? 'rejected' : 'pending') ?>">
              <?= $pret['date_acceptation'] ? 'Approuvé' : ($pret['date_refus'] ? 'Refusé' : 'En attente') ?>
            </span>
          </div>
          
          <div class="loan-details">
            <div class="loan-detail">
              <div class="detail-label">Type de Prêt</div>
              <div class="detail-value"><?= $typePret ? htmlspecialchars($typePret['nom']) : 'Type inconnu' ?></div>
            </div>
            <div class="loan-detail">
              <div class="detail-label">Montant</div>
              <div class="detail-value"><?= number_format($pret['montant'], 0, ',', ' ') ?> Ar</div>
            </div>
            <div class="loan-detail">
              <div class="detail-label">Durée</div>
              <div class="detail-value"><?= $pret['duree'] ?> an(s)</div>
            </div>
            <div class="loan-detail">
              <div class="detail-label">Date création</div>
              <div class="detail-value"><?= date('d/m/Y', strtotime($pret['date_creation'])) ?></div>
            </div>
          </div>

          <div class="loan-actions">
            <?php if (!$pret['date_acceptation'] && !$pret['date_refus']): ?>
              <button class="loan-btn approve" onclick="updateLoanStatus(<?= $pret['id'] ?>, 'approve')">
                <i class="fas fa-check"></i>
                Approuver
              </button>
              <button class="loan-btn reject" onclick="updateLoanStatus(<?= $pret['id'] ?>, 'reject')">
                <i class="fas fa-times"></i>
                Refuser
              </button>
            <?php elseif ($pret['date_acceptation']): ?>
              <button class="loan-btn info" onclick="showLoanDetails(<?= json_encode($pret) ?>)">
                <i class="fas fa-eye"></i>
                Voir Paiements
              </button>
              <button class="loan-btn export" onclick="exportLoanPayments(<?= $pret['id'] ?>)">
                <i class="fas fa-file-pdf"></i>
                Export PDF
              </button>
            <?php else: ?>
              <span class="status-text">
                Refusé le <?= date('d/m/Y', strtotime($pret['date_refus'])) ?>
              </span>
            <?php endif; ?>
          </div>
        </div>
      <?php endforeach; ?>
    <?php else: ?>
      <div class="empty-state">
        <i class="fas fa-handshake"></i>
        <h3>Aucun prêt trouvé</h3>
        <p>Commencez par créer votre premier prêt</p>
      </div>
    <?php endif; ?>
  </div>
</div>

<!-- Client Selection Modal -->
<div id="clientModal" class="modal">
  <div class="modal-content">
    <div class="modal-header">
      <h3>Sélectionner un Client</h3>
      <span class="close" onclick="closeClientModal()">&times;</span>
    </div>
    <div class="modal-body">
      <div class="clients-search">
        <input type="text" id="clientSearch" placeholder="Rechercher un client..." onkeyup="filterClients()">
      </div>
      <div class="clients-list" id="clientsList">
        <?php foreach ($clients as $client): ?>
          <div class="client-item" onclick="selectClient(<?= htmlspecialchars(json_encode($client)) ?>)">
            <div class="client-avatar">
              <i class="fas fa-user"></i>
            </div>
            <div class="client-info">
              <div class="client-name"><?= htmlspecialchars($client['nom'] . ' ' . $client['prenom']) ?></div>
              <div class="client-email"><?= htmlspecialchars($client['email']) ?></div>
              <div class="client-stats"><?= $client['loan_count'] ?> prêt(s) actuel(s)</div>
            </div>
            <div class="client-arrow">
              <i class="fas fa-chevron-right"></i>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    </div>
  </div>
</div>

<!-- Loan Creation Modal -->
<div id="loanModal" class="modal">
  <div class="modal-content large">
    <div class="modal-header">
      <h3>Créer un Prêt</h3>
      <span class="close" onclick="closeLoanModal()">&times;</span>
    </div>
    <div class="modal-body">
      <div id="loanForm" class="loan-form">
        <div class="selected-client" id="selectedClient"></div>
        
        <form id="loanDataForm">
          <input type="hidden" id="clientId" name="client_id">
          
          <div class="form-row">
            <div class="form-group">
              <label for="typePretId">Type de Prêt</label>
              <select id="typePretId" name="type_pret_id" required onchange="calculatePayment()">
                <option value="">Sélectionner un type...</option>
                <?php foreach ($typePrets as $type): ?>
                  <option value="<?= $type['id'] ?>" 
                    data-taux-interet="<?= $type['taux_interet'] ?>" 
                    data-taux-assurance="<?= $type['taux_assurance'] ?? 0 ?>"
                    data-duree-min="<?= $type['duree_min'] ?>"
                    data-duree-max="<?= $type['duree_max'] ?>">
                    <?= htmlspecialchars($type['nom']) ?> (<?= $type['taux_interet'] ?>% d'intérêt)
                  </option>
                <?php endforeach; ?>
              </select>
            </div>
            
            <div class="form-group">
              <label for="montant">Montant (Ar)</label>
              <input type="number" id="montant" name="montant" required min="1" oninput="calculatePayment()">
            </div>
          </div>
          
          <div class="form-row">
            <div class="form-group">
              <label for="duree">Durée (années)</label>
              <input type="number" id="duree" name="duree" required min="1" oninput="calculatePayment()">
              <small class="form-help" id="dureeHelp"></small>
            </div>
          </div>
        </form>

        <!-- Payment Preview -->
        <div id="paymentPreview" class="payment-preview" style="display: none;">
          <h4>Aperçu des Paiements</h4>
          <div class="preview-summary">
            <div class="summary-item">
              <span class="label">Paiement annuel:</span>
              <span class="value" id="annualPayment">0 Ar</span>
            </div>
            <div class="summary-item">
              <span class="label">Total à rembourser:</span>
              <span class="value" id="totalAmount">0 Ar</span>
            </div>
          </div>
          
          <div class="payment-schedule" id="paymentSchedule"></div>
          
          <div class="form-actions">
            <button type="button" class="btn-secondary" onclick="closeLoanModal()">Annuler</button>
            <button type="button" class="btn-primary" onclick="confirmLoanCreation()">Confirmer le Prêt</button>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<style>
.prets-dashboard {
  display: flex;
  flex-direction: column;
  gap: 1.5rem;
  padding: 0;
}

/* Header */
.section-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 1.5rem;
  background: white;
  border-radius: 16px;
  box-shadow: var(--card-shadow);
}

.header-content h2 {
  margin: 0;
  color: var(--text-primary);
  display: flex;
  align-items: center;
  gap: 0.5rem;
}

.header-content p {
  margin: 0.25rem 0 0 0;
  color: var(--text-secondary);
  font-size: 0.875rem;
}

.action-btn {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  padding: 0.75rem 1.5rem;
  border: none;
  border-radius: 12px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.2s ease;
}

.action-btn.primary {
  background: linear-gradient(135deg, var(--primary-color), var(--primary-dark));
  color: white;
}

.action-btn.secondary {
  background: #E5E7EB;
  color: var(--text-secondary);
}

.action-btn.success {
  background: linear-gradient(135deg, var(--accent-color), #059669);
  color: white;
}

.action-btn.warning {
  background: linear-gradient(135deg, #EF4444, #DC2626);
  color: white;
}

.action-btn:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
}

/* Alerts */
.alert {
  padding: 1rem 1.5rem;
  border-radius: 12px;
  margin-bottom: 1.5rem;
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
  margin-left: auto;
  background: none;
  border: none;
  font-size: 1.2rem;
  cursor: pointer;
  opacity: 0.7;
}

/* Loans Grid */
.loans-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(400px, 1fr));
  gap: 1.5rem;
}

.loan-card {
  background: white;
  border-radius: 16px;
  padding: 1.5rem;
  box-shadow: var(--card-shadow);
  transition: all 0.3s ease;
  border: 2px solid transparent;
}

.loan-card:hover {
  transform: translateY(-2px);
  box-shadow: var(--card-shadow-hover);
  border-color: var(--primary-color);
}

.loan-header {
  display: flex;
  align-items: flex-start;
  justify-content: space-between;
  margin-bottom: 1rem;
}

.loan-title {
  font-size: 1.125rem;
  font-weight: 600;
  margin: 0;
  color: var(--text-primary);
}

.loan-client {
  font-size: 0.875rem;
  color: var(--text-secondary);
  margin: 0.25rem 0 0 0;
}

.loan-status {
  padding: 0.25rem 0.75rem;
  border-radius: 20px;
  font-size: 0.75rem;
  font-weight: 600;
  white-space: nowrap;
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
  grid-template-columns: repeat(2, 1fr);
  gap: 1rem;
  margin-bottom: 1.5rem;
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

.loan-actions {
  display: flex;
  gap: 0.75rem;
  align-items: center;
}

.loan-btn {
  padding: 0.5rem 1rem;
  border: none;
  border-radius: 8px;
  font-size: 0.875rem;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.2s ease;
  display: flex;
  align-items: center;
  gap: 0.5rem;
}

.loan-btn.approve {
  background: linear-gradient(135deg, var(--accent-color), #059669);
  color: white;
}

.loan-btn.reject {
  background: linear-gradient(135deg, #EF4444, #DC2626);
  color: white;
}

.loan-btn.export {
  background: linear-gradient(135deg, #F59E0B, #D97706);
  color: white;
}

.loan-btn.info {
  background: linear-gradient(135deg, var(--secondary-color), #4F46E5);
  color: white;
}

.loan-btn:hover {
  transform: translateY(-1px);
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
}

.status-text {
  font-size: 0.875rem;
  color: var(--text-secondary);
  font-style: italic;
}

/* Modal Styles */
.modal {
  display: none;
  position: fixed;
  z-index: 1000;
  left: 0;
  top: 0;
  width: 100%;
  height: 100%;
  background-color: rgba(0, 0, 0, 0.5);
  backdrop-filter: blur(4px);
}

.modal-content {
  background-color: white;
  margin: 5% auto;
  border-radius: 16px;
  width: 90%;
  max-width: 600px;
  max-height: 80vh;
  overflow: hidden;
  box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
}

.modal-content.large {
  max-width: 800px;
}

.modal-header {
  padding: 1.5rem;
  border-bottom: 1px solid var(--border-color);
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.modal-header h3 {
  margin: 0;
  color: var(--text-primary);
}

.close {
  font-size: 1.5rem;
  font-weight: bold;
  cursor: pointer;
  color: var(--text-secondary);
}

.close:hover {
  color: var(--text-primary);
}

.modal-body {
  padding: 1.5rem;
  max-height: 60vh;
  overflow-y: auto;
}

/* Client Selection */
.clients-search {
  margin-bottom: 1rem;
}

.clients-search input {
  width: 100%;
  padding: 0.75rem;
  border: 2px solid var(--border-color);
  border-radius: 8px;
  font-size: 1rem;
}

.clients-list {
  max-height: 400px;
  overflow-y: auto;
}

.client-item {
  display: flex;
  align-items: center;
  gap: 1rem;
  padding: 1rem;
  border-radius: 12px;
  cursor: pointer;
  transition: all 0.2s ease;
  margin-bottom: 0.5rem;
}

.client-item:hover {
  background: #F8FAFC;
  transform: translateX(4px);
}

.client-avatar {
  width: 48px;
  height: 48px;
  border-radius: 12px;
  background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
  display: flex;
  align-items: center;
  justify-content: center;
  color: white;
}

.client-info {
  flex: 1;
}

.client-name {
  font-weight: 600;
  color: var(--text-primary);
  margin-bottom: 0.25rem;
}

.client-email {
  font-size: 0.875rem;
  color: var(--text-secondary);
  margin-bottom: 0.25rem;
}

.client-stats {
  font-size: 0.75rem;
  color: var(--text-secondary);
}

.client-arrow {
  color: var(--text-secondary);
}

/* Loan Form */
.selected-client {
  background: #F8FAFC;
  padding: 1rem;
  border-radius: 12px;
  margin-bottom: 1.5rem;
  border-left: 4px solid var(--primary-color);
}

.form-row {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
  gap: 1rem;
  margin-bottom: 1rem;
}

.form-group {
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
}

.form-group label {
  font-weight: 600;
  color: var(--text-primary);
}

.form-group input,
.form-group select {
  padding: 0.75rem;
  border: 2px solid var(--border-color);
  border-radius: 8px;
  font-size: 1rem;
}

.form-group input:focus,
.form-group select:focus {
  outline: none;
  border-color: var(--primary-color);
}

.form-help {
  font-size: 0.75rem;
  color: var(--text-secondary);
}

/* Payment Preview */
.payment-preview {
  margin-top: 1.5rem;
  padding: 1.5rem;
  background: #F8FAFC;
  border-radius: 12px;
  border: 2px solid var(--primary-color);
}

.payment-preview h4 {
  margin: 0 0 1rem 0;
  color: var(--text-primary);
}

.preview-summary {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
  gap: 1rem;
  margin-bottom: 1.5rem;
}

.summary-item {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 0.75rem;
  background: white;
  border-radius: 8px;
}

.summary-item .label {
  font-weight: 600;
  color: var(--text-secondary);
}

.summary-item .value {
  font-weight: 700;
  color: var(--primary-color);
}

.payment-schedule {
  max-height: 300px;
  overflow-y: auto;
}

.schedule-item {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 0.75rem;
  background: white;
  border-radius: 8px;
  margin-bottom: 0.5rem;
}

.form-actions {
  display: flex;
  gap: 1rem;
  justify-content: flex-end;
  margin-top: 1.5rem;
  padding-top: 1.5rem;
  border-top: 1px solid var(--border-color);
}

.btn-secondary,
.btn-primary {
  padding: 0.75rem 1.5rem;
  border: none;
  border-radius: 8px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.2s ease;
}

.btn-secondary {
  background: #E5E7EB;
  color: var(--text-secondary);
}

.btn-primary {
  background: linear-gradient(135deg, var(--primary-color), var(--primary-dark));
  color: white;
}

.btn-secondary:hover,
.btn-primary:hover {
  transform: translateY(-1px);
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
}

/* Empty State */
.empty-state {
  text-align: center;
  padding: 3rem;
  color: var(--text-secondary);
  grid-column: 1 / -1;
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

/* Responsive */
@media (max-width: 768px) {
  .loans-grid {
    grid-template-columns: 1fr;
  }
  
  .loan-details {
    grid-template-columns: 1fr;
  }
  
  .modal-content {
    margin: 2% auto;
    width: 95%;
  }
  
  .form-row {
    grid-template-columns: 1fr;
  }
  
  .preview-summary {
    grid-template-columns: 1fr;
  }
}
</style>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>

<script>
let selectedClient = null;
let loanData = null;
let typePrets = <?= json_encode($typePrets) ?>;

function showClientModal() {
  document.getElementById('clientModal').style.display = 'block';
}

function closeClientModal() {
  document.getElementById('clientModal').style.display = 'none';
}

function closeLoanModal() {
  document.getElementById('loanModal').style.display = 'none';
  selectedClient = null;
  loanData = null;
  document.getElementById('loanDataForm').reset();
  document.getElementById('paymentPreview').style.display = 'none';
}

function filterClients() {
  const search = document.getElementById('clientSearch').value.toLowerCase();
  const clients = document.querySelectorAll('.client-item');
  
  clients.forEach(client => {
    const name = client.querySelector('.client-name').textContent.toLowerCase();
    const email = client.querySelector('.client-email').textContent.toLowerCase();
    
    if (name.includes(search) || email.includes(search)) {
      client.style.display = 'flex';
    } else {
      client.style.display = 'none';
    }
  });
}

function selectClient(client) {
  selectedClient = client;
  closeClientModal();
  
  document.getElementById('clientId').value = client.id;
  document.getElementById('selectedClient').innerHTML = `
    <div class="client-item">
      <div class="client-avatar">
        <i class="fas fa-user"></i>
      </div>
      <div class="client-info">
        <div class="client-name">${client.nom} ${client.prenom}</div>
        <div class="client-email">${client.email}</div>
        <div class="client-stats">${client.loan_count} prêt(s) actuel(s)</div>
      </div>
    </div>
  `;
  
  document.getElementById('loanModal').style.display = 'block';
}

function calculatePayment() {
  const typePretSelect = document.getElementById('typePretId');
  const montant = parseFloat(document.getElementById('montant').value) || 0;
  const duree = parseInt(document.getElementById('duree').value) || 0;
  
  if (!typePretSelect.value || !montant || !duree) {
    document.getElementById('paymentPreview').style.display = 'none';
    return;
  }
  
  const selectedOption = typePretSelect.options[typePretSelect.selectedIndex];
  const tauxInteret = parseFloat(selectedOption.dataset.tauxInteret) || 0;
  const tauxAssurance = parseFloat(selectedOption.dataset.tauxAssurance) || 0;
  const dureeMin = parseInt(selectedOption.dataset.dureeMin) || 1;
  const dureeMax = parseInt(selectedOption.dataset.dureeMax) || 30;
  
  // Update duration help text
  document.getElementById('dureeHelp').textContent = `Durée autorisée: ${dureeMin} - ${dureeMax} années`;
  
  if (duree < dureeMin || duree > dureeMax) {
    document.getElementById('paymentPreview').style.display = 'none';
    return;
  }
  
  // Calculate payments
  const montantAnnuel = montant / duree;
  const interetTotal = montant * (tauxInteret / 100);
  const assuranceTotal = montant * (tauxAssurance / 100);
  const paiementAnnuel = montantAnnuel + (interetTotal / duree) + (assuranceTotal / duree);
  const totalARembourser = montant + interetTotal + assuranceTotal;
  
  // Update display
  document.getElementById('annualPayment').textContent = new Intl.NumberFormat('fr-FR').format(paiementAnnuel) + ' Ar';
  document.getElementById('totalAmount').textContent = new Intl.NumberFormat('fr-FR').format(totalARembourser) + ' Ar';
  
  // Generate schedule
  let scheduleHTML = '';
  const startDate = new Date();
  
  for (let i = 1; i <= duree; i++) {
    const dueDate = new Date(startDate);
    dueDate.setFullYear(dueDate.getFullYear() + i);
    
    scheduleHTML += `
      <div class="schedule-item">
        <span>Année ${i} - ${dueDate.toLocaleDateString('fr-FR')}</span>
        <span class="value">${new Intl.NumberFormat('fr-FR').format(paiementAnnuel)} Ar</span>
      </div>
    `;
  }
  
  document.getElementById('paymentSchedule').innerHTML = scheduleHTML;
  document.getElementById('paymentPreview').style.display = 'block';
}

function confirmLoanCreation() {
  if (!confirm('Confirmer la création de ce prêt ?')) {
    return;
  }
  
  const form = document.getElementById('loanDataForm');
  const formData = new FormData(form);
  
  // Submit to backend
  const submitForm = document.createElement('form');
  submitForm.method = 'POST';
  submitForm.action = '<?= route('/admin/prets/create') ?>';
  
  for (const [key, value] of formData) {
    const input = document.createElement('input');
    input.type = 'hidden';
    input.name = key;
    input.value = value;
    submitForm.appendChild(input);
  }
  
  document.body.appendChild(submitForm);
  submitForm.submit();
}

function updateLoanStatus(loanId, action) {
  const actionText = action === 'approve' ? 'approuver' : 'refuser';
  
  if (!confirm(`Êtes-vous sûr de vouloir ${actionText} ce prêt ?`)) {
    return;
  }
  
  const data = {
    date_acceptation: action === 'approve' ? new Date().toISOString().split('T')[0] + ' ' + new Date().toTimeString().split(' ')[0] : null,
    date_refus: action === 'reject' ? new Date().toISOString().split('T')[0] + ' ' + new Date().toTimeString().split(' ')[0] : null
  };
  
  fetch(`<?= $config['api']['base_url'] ?>/prets/${loanId}`, {
    method: 'PUT',
    headers: {'Content-Type': 'application/json'},
    body: JSON.stringify(data)
  })
  .then(response => response.json())
  .then(result => {
    if (result.message) {
      alert(result.message);
      location.reload();
    } else {
      alert('Erreur lors de la mise à jour');
    }
  })
  .catch(error => {
    console.error('Error:', error);
    alert('Erreur lors de la mise à jour');
  });
}

function exportClientReport(client) {
  if (!client || !client.id) {
    alert('Client non sélectionné');
    return;
  }
  
  // Open PDF in new window
  const exportUrl = '<?= route('/export/client') ?>/' + client.id;
  window.open(exportUrl, '_blank');
}

async function exportLoanPayments(loanId) {
  if (!loanId) {
    alert('ID du prêt manquant');
    return;
  }
  
  if (!confirm('Exporter les paiements de ce prêt en PDF ?')) {
    return;
  }
  
  // Show loading state
  const btn = event.target.closest('.loan-btn');
  const originalContent = btn.innerHTML;
  btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Export...';
  btn.disabled = true;
  
  try {
    // Get loan data from API
    const response = await fetch(`<?= $config['api']['base_url'] ?>/prets/${loanId}/payment-data`);
    const data = await response.json();
    
    if (data.error) {
      throw new Error(data.error);
    }
    
    // Generate PDF using jsPDF
    await generateClientSidePDF(data, loanId);
    
    // Reset button
    btn.innerHTML = originalContent;
    btn.disabled = false;
    
  } catch (error) {
    console.error('Export error:', error);
    alert('Erreur lors de l\'export PDF: ' + error.message);
    
    // Reset button
    btn.innerHTML = originalContent;
    btn.disabled = false;
  }
}

async function generateClientSidePDF(data, loanId) {
  const { jsPDF } = window.jspdf;
  const pdf = new jsPDF('p', 'mm', 'a4');
  
  const loan = data.loan;
  const schedule = data.schedule;
  const payments = data.payments;
  
  // Set font
  pdf.setFont('helvetica');
  
  // Header
  pdf.setFontSize(20);
  pdf.setTextColor(59, 130, 246);
  pdf.text('RELEVÉ DE PAIEMENTS', 105, 20, { align: 'center' });
  
  pdf.setFontSize(12);
  pdf.setTextColor(100, 100, 100);
  pdf.text(`Prêt N° ${loanId.toString().padStart(6, '0')}`, 105, 30, { align: 'center' });
  pdf.text(`Généré le ${new Date().toLocaleDateString('fr-FR')}`, 105, 37, { align: 'center' });
  
  // Loan Information
  let yPos = 50;
  pdf.setFontSize(14);
  pdf.setTextColor(59, 130, 246);
  pdf.text('Informations du Prêt', 20, yPos);
  
  yPos += 10;
  pdf.setFontSize(10);
  pdf.setTextColor(0, 0, 0);
  
  const loanInfo = [
    [`Client: ${loan.client_nom} ${loan.client_prenom}`, `Email: ${loan.client_email}`],
    [`Type: ${loan.type_pret_nom}`, `Taux: ${loan.taux_interet}%`],
    [`Montant: ${loan.montant.toLocaleString('fr-FR')} Ar`, `Durée: ${loan.duree} an(s)`],
    [`Création: ${new Date(loan.date_creation).toLocaleDateString('fr-FR')}`, 
     `Approbation: ${loan.date_acceptation ? new Date(loan.date_acceptation).toLocaleDateString('fr-FR') : 'En attente'}`]
  ];
  
  loanInfo.forEach(row => {
    pdf.text(row[0], 20, yPos);
    pdf.text(row[1], 110, yPos);
    yPos += 7;
  });
  
  // Payment Schedule Table
  yPos += 10;
  pdf.setFontSize(14);
  pdf.setTextColor(59, 130, 246);
  pdf.text('Calendrier des Paiements', 20, yPos);
  
  yPos += 10;
  
  // Table headers
  pdf.setFontSize(9);
  pdf.setTextColor(255, 255, 255);
  pdf.setFillColor(59, 130, 246);
  pdf.rect(20, yPos - 5, 170, 8, 'F');
  
  pdf.text('Année', 22, yPos);
  pdf.text('Date', 42, yPos);
  pdf.text('Montant', 72, yPos);
  pdf.text('Statut', 102, yPos);
  pdf.text('Payé le', 132, yPos);
  pdf.text('Montant', 162, yPos);
  
  yPos += 8;
  
  // Table rows
  pdf.setTextColor(0, 0, 0);
  const paymentsLookup = {};
  payments.forEach(payment => {
    paymentsLookup[payment.payment_year] = payment;
  });
  
  schedule.forEach((item, index) => {
    const yearNumber = index + 1;
    const actualPayment = paymentsLookup[item.annee];
    const isPaid = item.isPayer || actualPayment;
    
    // Alternate row colors
    if (index % 2 === 0) {
      pdf.setFillColor(248, 249, 250);
      pdf.rect(20, yPos - 5, 170, 7, 'F');
    }
    
    pdf.text(`Année ${yearNumber}`, 22, yPos);
    pdf.text(new Date(item.date_echeance).toLocaleDateString('fr-FR'), 42, yPos);
    pdf.text(`${item.montant.toLocaleString('fr-FR')} Ar`, 72, yPos);
    pdf.text(isPaid ? 'Payé' : 'En attente', 102, yPos);
    pdf.text(actualPayment ? new Date(actualPayment.date_retour).toLocaleDateString('fr-FR') : '-', 132, yPos);
    pdf.text(actualPayment ? `${actualPayment.montant.toLocaleString('fr-FR')} Ar` : '-', 162, yPos);
    
    yPos += 7;
    
    // Add new page if needed
    if (yPos > 270) {
      pdf.addPage();
      yPos = 20;
    }
  });
  
  // Footer
  pdf.setFontSize(8);
  pdf.setTextColor(100, 100, 100);
  pdf.text('FinanceAdmin - Système de Gestion des Prêts', 105, 285, { align: 'center' });
  
  // Download PDF
  pdf.save(`paiements_pret_${loanId}.pdf`);
}

function showLoanDetails(loan) {
  // Show loan details modal or redirect to details page
  window.location.href = '<?= route('/admin/clients') ?>?client_id=' + loan.client_id + '&loan_id=' + loan.id;
}

// Close modals when clicking outside
window.onclick = function(event) {
  const clientModal = document.getElementById('clientModal');
  const loanModal = document.getElementById('loanModal');
  
  if (event.target === clientModal) {
    closeClientModal();
  }
  if (event.target === loanModal) {
    closeLoanModal();
  }
}
</script>
