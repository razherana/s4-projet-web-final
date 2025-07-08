<div class="simulate-dashboard">
  <!-- Header Section -->
  <section class="simulate-header">
    <div class="header-card">
      <div class="header-content">
        <h2 class="header-title">Simulateur de Prêt</h2>
        <p class="header-subtitle">Calculez votre capacité d'emprunt et visualisez vos paiements</p>
      </div>
      <div class="header-icon">
        <i class="fas fa-calculator"></i>
      </div>
    </div>
  </section>

  <!-- Success/Error Messages -->
  <?php if (isset($_GET['success'])): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
      <i class="fas fa-check-circle"></i>
      Demande de prêt créée avec succès !
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
  <?php elseif (isset($_GET['error'])): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
      <i class="fas fa-exclamation-triangle"></i>
      <?php
        switch ($_GET['error']) {
          case 'missing_data':
            echo 'Veuillez remplir tous les champs requis.';
            break;
          case 'loan_creation_failed':
            echo 'Échec de la création du prêt. Veuillez réessayer.';
            break;
          case 'loan_creation_error':
            echo 'Erreur lors de la création du prêt.';
            break;
          default:
            echo 'Une erreur est survenue.';
        }
      ?>
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
  <?php endif; ?>

  <!-- Loan Form Section -->
  <section class="loan-form-section">
    <div class="form-card">
      <div class="form-header">
        <h3 class="form-title">
          <i class="fas fa-form"></i>
          Détails de votre Prêt
        </h3>
        <p class="form-subtitle">Remplissez les informations pour simuler votre prêt</p>
      </div>
      
      <form id="loanDataForm" class="loan-form">
        <div class="form-row">
          <div class="form-group">
            <label for="typePretId" class="form-label">
              <i class="fas fa-tags"></i>
              Type de Prêt
            </label>
            <select id="typePretId" name="type_pret_id" class="form-control" required onchange="updateDurationLimits(); calculatePayment()">
              <option value="">Sélectionner un type de prêt...</option>
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
            <small class="form-help">Choisissez le type de prêt qui correspond à vos besoins</small>
          </div>
          
          <div class="form-group">
            <label for="montant" class="form-label">
              <i class="fas fa-coins"></i>
              Montant (Ar)
            </label>
            <input type="number" id="montant" name="montant" class="form-control" required min="1" step="1000" oninput="calculatePayment()" placeholder="Ex: 1000000">
            <small class="form-help">Montant que vous souhaitez emprunter</small>
          </div>
        </div>
        
        <div class="form-row">
          <div class="form-group">
            <label for="duree" class="form-label">
              <i class="fas fa-calendar-alt"></i>
              Durée (années)
            </label>
            <input type="number" id="duree" name="duree" class="form-control" required min="1" oninput="calculatePayment()" placeholder="Ex: 5">
            <small class="form-help" id="dureeHelp">Durée de remboursement en années</small>
          </div>
          
          <div class="form-group">
            <div class="simulation-actions">
              <button type="button" class="btn-simulate" onclick="calculatePayment()">
                <i class="fas fa-calculator"></i>
                <span>Simuler</span>
              </button>
              <button type="button" class="btn-reset" onclick="resetForm()">
                <i class="fas fa-undo"></i>
                <span>Réinitialiser</span>
              </button>
            </div>
          </div>
        </div>
      </form>
    </div>
  </section>

  <!-- Payment Preview Section -->
  <section class="preview-section" id="paymentPreview" style="display: none;">
    <div class="preview-card">
      <div class="preview-header">
        <h3 class="preview-title">
          <i class="fas fa-chart-pie"></i>
          Aperçu des Paiements
        </h3>
        <p class="preview-subtitle">Voici le détail de votre prêt simulé</p>
      </div>

      <!-- Summary Cards -->
      <div class="summary-section">
        <div class="summary-grid">
          <div class="summary-item primary">
            <div class="summary-icon">
              <i class="fas fa-money-bill-wave"></i>
            </div>
            <div class="summary-content">
              <div class="summary-value" id="summaryMontant">0 Ar</div>
              <div class="summary-label">Montant Emprunté</div>
            </div>
          </div>
          
          <div class="summary-item success">
            <div class="summary-icon">
              <i class="fas fa-credit-card"></i>
            </div>
            <div class="summary-content">
              <div class="summary-value" id="summaryPaiementAnnuel">0 Ar</div>
              <div class="summary-label">Paiement Annuel</div>
            </div>
          </div>
          
          <div class="summary-item warning">
            <div class="summary-icon">
              <i class="fas fa-chart-line"></i>
            </div>
            <div class="summary-content">
              <div class="summary-value" id="summaryTotalARembourser">0 Ar</div>
              <div class="summary-label">Total à Rembourser</div>
            </div>
          </div>
          
          <div class="summary-item info">
            <div class="summary-icon">
              <i class="fas fa-percentage"></i>
            </div>
            <div class="summary-content">
              <div class="summary-value" id="summaryInterets">0 Ar</div>
              <div class="summary-label">Intérêts + Assurance</div>
            </div>
          </div>
        </div>
      </div>

      <!-- Payment Schedule -->
      <div class="schedule-section">
        <div class="schedule-header">
          <h4 class="schedule-title">
            <i class="fas fa-calendar-check"></i>
            Calendrier des Paiements
          </h4>
          <p class="schedule-subtitle">Détail de vos paiements annuels</p>
        </div>
        
        <div class="payment-schedule" id="paymentSchedule">
          <!-- Populated by JavaScript -->
        </div>
      </div>

      <!-- Confirmation Actions -->
      <div class="form-actions">
        <button type="button" class="btn-secondary" onclick="hidePreview()">
          <i class="fas fa-edit"></i>
          Modifier
        </button>
        <button type="button" class="btn-primary" onclick="confirmLoanCreation()" id="confirmBtn">
          <i class="fas fa-check"></i>
          <span>Confirmer le Prêt</span>
        </button>
      </div>
    </div>
  </section>
</div>

<style>
.simulate-dashboard {
  display: flex;
  flex-direction: column;
  gap: 2rem;
  padding: 0;
}

/* Header Section */
.simulate-header {
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

.header-icon {
  font-size: 3rem;
  opacity: 0.3;
  position: relative;
  z-index: 1;
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

/* Form Section */
.loan-form-section {
  margin-bottom: 1rem;
}

.form-card {
  background: white;
  border-radius: 16px;
  padding: 2rem;
  box-shadow: var(--card-shadow);
  border: 1px solid var(--border-color);
}

.form-header {
  margin-bottom: 2rem;
  text-align: center;
}

.form-title {
  font-size: 1.25rem;
  font-weight: 600;
  color: var(--text-primary);
  margin: 0 0 0.5rem 0;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 0.5rem;
}

.form-subtitle {
  font-size: 0.875rem;
  color: var(--text-secondary);
  margin: 0;
}

.loan-form {
  display: flex;
  flex-direction: column;
  gap: 1.5rem;
}

.form-row {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
  gap: 1.5rem;
}

.form-group {
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
}

.form-label {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  font-size: 0.875rem;
  font-weight: 600;
  color: var(--text-primary);
  margin-bottom: 0.5rem;
}

.form-control {
  padding: 0.875rem 1rem;
  border: 2px solid var(--border-color);
  border-radius: 12px;
  font-size: 0.875rem;
  transition: all 0.2s ease;
  background: white;
}

.form-control:focus {
  outline: none;
  border-color: var(--primary-color);
  box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

.form-help {
  font-size: 0.75rem;
  color: var(--text-secondary);
  margin-top: 0.25rem;
}

.simulation-actions {
  display: flex;
  gap: 1rem;
  margin-top: 1.5rem;
}

.btn-simulate,
.btn-reset {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  padding: 0.875rem 1.5rem;
  border: none;
  border-radius: 12px;
  font-size: 0.875rem;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.2s ease;
}

.btn-simulate {
  background: linear-gradient(135deg, var(--primary-color), var(--primary-dark));
  color: white;
  flex: 1;
}

.btn-simulate:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
}

.btn-reset {
  background: #E5E7EB;
  color: var(--text-secondary);
}

.btn-reset:hover {
  background: #D1D5DB;
  transform: translateY(-1px);
}

/* Preview Section */
.preview-section {
  flex: 1;
}

.preview-card {
  background: white;
  border-radius: 16px;
  padding: 2rem;
  box-shadow: var(--card-shadow);
  border: 1px solid var(--border-color);
}

.preview-header {
  margin-bottom: 2rem;
  text-align: center;
}

.preview-title {
  font-size: 1.25rem;
  font-weight: 600;
  color: var(--text-primary);
  margin: 0 0 0.5rem 0;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 0.5rem;
}

.preview-subtitle {
  font-size: 0.875rem;
  color: var(--text-secondary);
  margin: 0;
}

/* Summary Cards */
.summary-section {
  margin-bottom: 2rem;
}

.summary-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
  gap: 1.5rem;
}

.summary-item {
  display: flex;
  align-items: center;
  gap: 1rem;
  padding: 1.5rem;
  border-radius: 12px;
  box-shadow: var(--card-shadow);
  transition: all 0.3s ease;
  position: relative;
  overflow: hidden;
}

.summary-item::before {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  height: 4px;
  background: var(--primary-color);
}

.summary-item.success::before { background: var(--accent-color); }
.summary-item.warning::before { background: #F59E0B; }
.summary-item.info::before { background: var(--secondary-color); }

.summary-item:hover {
  transform: translateY(-2px);
  box-shadow: var(--card-shadow-hover);
}

.summary-icon {
  width: 48px;
  height: 48px;
  border-radius: 12px;
  background: linear-gradient(135deg, var(--primary-color), var(--primary-dark));
  color: white;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 1.25rem;
  flex-shrink: 0;
}

.summary-item.success .summary-icon {
  background: linear-gradient(135deg, var(--accent-color), #059669);
}

.summary-item.warning .summary-icon {
  background: linear-gradient(135deg, #F59E0B, #D97706);
}

.summary-item.info .summary-icon {
  background: linear-gradient(135deg, var(--secondary-color), #4F46E5);
}

.summary-content {
  flex: 1;
}

.summary-value {
  font-size: 1.5rem;
  font-weight: 700;
  color: var(--text-primary);
  margin-bottom: 0.25rem;
  line-height: 1;
}

.summary-label {
  font-size: 0.875rem;
  color: var(--text-secondary);
  font-weight: 500;
}

/* Schedule Section */
.schedule-section {
  margin-bottom: 2rem;
}

.schedule-header {
  margin-bottom: 1.5rem;
}

.schedule-title {
  font-size: 1.125rem;
  font-weight: 600;
  color: var(--text-primary);
  margin: 0 0 0.5rem 0;
  display: flex;
  align-items: center;
  gap: 0.5rem;
}

.schedule-subtitle {
  font-size: 0.875rem;
  color: var(--text-secondary);
  margin: 0;
}

/* Payment Schedule Table */
.schedule-table-container {
  border: 1px solid var(--border-color);
  border-radius: 12px;
  overflow: hidden;
  background: white;
  box-shadow: var(--card-shadow);
}

.schedule-table {
  width: 100%;
  border-collapse: collapse;
  margin: 0;
  background: white;
}

.schedule-table thead {
  background: linear-gradient(135deg, #F8FAFC 0%, #E2E8F0 100%);
}

.schedule-table th {
  padding: 1rem 1.5rem;
  text-align: left;
  font-weight: 600;
  color: var(--text-primary);
  font-size: 0.875rem;
  border-bottom: 2px solid var(--border-color);
  position: relative;
}

.schedule-table th:first-child {
  border-radius: 0;
}

.schedule-table th:last-child {
  border-radius: 0;
}

.schedule-table tbody tr {
  border-bottom: 1px solid var(--border-color);
  transition: all 0.2s ease;
}

.schedule-table tbody tr:hover {
  background: #F8FAFC;
  transform: translateX(2px);
}

.schedule-table tbody tr:last-child {
  border-bottom: none;
}

.schedule-table td {
  padding: 1.25rem 1.5rem;
  color: var(--text-secondary);
  font-size: 0.875rem;
  vertical-align: middle;
}

.schedule-table td:first-child {
  font-weight: 600;
  color: var(--text-primary);
}

.schedule-table td:nth-child(3),
.schedule-table td:nth-child(4),
.schedule-table td:nth-child(5),
.schedule-table td:nth-child(6) {
  font-weight: 600;
  color: var(--text-primary);
}

/* Add zebra striping */
.schedule-table tbody tr:nth-child(even) {
  background: rgba(248, 250, 252, 0.5);
}

.schedule-table tbody tr:nth-child(even):hover {
  background: #F1F5F9;
}

/* Enhanced cell styling */
.schedule-table td[style*="font-weight: 600"] {
  color: var(--primary-color);
}

/* Confirmation Section */
.form-actions {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 1rem;
  padding: 2rem 0 1rem 0;
  border-top: 1px solid var(--border-color);
  margin-top: 2rem;
}

.btn-secondary,
.btn-primary {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  padding: 0.875rem 2rem;
  border: none;
  border-radius: 12px;
  font-size: 0.875rem;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.2s ease;
  text-decoration: none;
  min-width: 140px;
  justify-content: center;
}

.btn-secondary {
  background: #F3F4F6;
  color: var(--text-secondary);
  border: 2px solid var(--border-color);
}

.btn-secondary:hover {
  background: #E5E7EB;
  border-color: var(--text-secondary);
  color: var(--text-primary);
  transform: translateY(-1px);
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

.btn-primary {
  background: linear-gradient(135deg, var(--primary-color), var(--primary-dark));
  color: white;
  border: 2px solid transparent;
}

.btn-primary:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
}

.btn-primary:disabled {
  opacity: 0.6;
  cursor: not-allowed;
  transform: none;
  box-shadow: none;
}

.btn-primary:disabled:hover {
  transform: none;
  box-shadow: none;
}

/* Responsive Design */
@media (max-width: 768px) {
  .simulate-dashboard {
    gap: 1.5rem;
  }
  
  .header-card {
    flex-direction: column;
    text-align: center;
    gap: 1rem;
  }
  
  .form-row {
    grid-template-columns: 1fr;
  }
  
  .summary-grid {
    grid-template-columns: repeat(2, 1fr);
  }
  
  .confirmation-actions {
    flex-direction: column;
  }
}

@media (max-width: 480px) {
  .header-card {
    padding: 1.5rem;
  }
  
  .form-card,
  .results-card {
    padding: 1.5rem;
  }
  
  .summary-grid {
    grid-template-columns: 1fr;
  }
  
  .simulation-actions {
    flex-direction: column;
  }
}
</style>

<script>
let currentSimulation = null;
let typePrets = <?= json_encode($typePrets) ?>;

function updateDurationLimits() {
  const typePretSelect = document.getElementById('typePretId');
  const dureeInput = document.getElementById('duree');
  const dureeHelp = document.getElementById('dureeHelp');
  
  if (!typePretSelect.value) {
    dureeHelp.textContent = 'Durée de remboursement en années';
    dureeInput.removeAttribute('min');
    dureeInput.removeAttribute('max');
    return;
  }
  
  const selectedOption = typePretSelect.options[typePretSelect.selectedIndex];
  const dureeMin = parseInt(selectedOption.dataset.dureeMin) || 1;
  const dureeMax = parseInt(selectedOption.dataset.dureeMax) || 30;
  
  dureeInput.setAttribute('min', dureeMin);
  dureeInput.setAttribute('max', dureeMax);
  dureeHelp.textContent = `Durée autorisée: ${dureeMin} - ${dureeMax} années`;
}

function calculatePayment() {
  const typePretSelect = document.getElementById('typePretId');
  const montant = parseFloat(document.getElementById('montant').value) || 0;
  const duree = parseInt(document.getElementById('duree').value) || 0;
  
  if (!typePretSelect.value || !montant || !duree) {
    hidePreview();
    return;
  }
  
  const selectedOption = typePretSelect.options[typePretSelect.selectedIndex];
  const tauxInteret = parseFloat(selectedOption.dataset.tauxInteret) || 0;
  const tauxAssurance = parseFloat(selectedOption.dataset.tauxAssurance) || 0;
  const dureeMin = parseInt(selectedOption.dataset.dureeMin) || 1;
  const dureeMax = parseInt(selectedOption.dataset.dureeMax) || 30;
  
  if (duree < dureeMin || duree > dureeMax) {
    hidePreview();
    return;
  }
  
  // Calculate payments like in admin prets.php
  const montantAnnuel = montant / duree;
  const interetTotal = montant * (tauxInteret / 100);
  const assuranceTotal = montant * (tauxAssurance / 100);
  const paiementAnnuel = montantAnnuel + (interetTotal / duree) + (assuranceTotal / duree);
  const totalARembourser = montant + interetTotal + assuranceTotal;
  
  currentSimulation = {
    type_pret_id: typePretSelect.value,
    montant: montant,
    duree: duree,
    paiementAnnuel: paiementAnnuel,
    totalARembourser: totalARembourser,
    interetTotal: interetTotal,
    assuranceTotal: assuranceTotal
  };
  
  showPreview(currentSimulation);
}

function showPreview(simulation) {
  // Update summary cards
  document.getElementById('summaryMontant').textContent = simulation.montant.toLocaleString('fr-FR') + ' Ar';
  document.getElementById('summaryPaiementAnnuel').textContent = Math.round(simulation.paiementAnnuel).toLocaleString('fr-FR') + ' Ar';
  document.getElementById('summaryTotalARembourser').textContent = Math.round(simulation.totalARembourser).toLocaleString('fr-FR') + ' Ar';
  document.getElementById('summaryInterets').textContent = Math.round(simulation.interetTotal + simulation.assuranceTotal).toLocaleString('fr-FR') + ' Ar';
  
  // Generate payment schedule
  generatePaymentSchedule(simulation);
  
  // Show preview section
  document.getElementById('paymentPreview').style.display = 'block';
  
  // Smooth scroll to preview
  document.getElementById('paymentPreview').scrollIntoView({ 
    behavior: 'smooth', 
    block: 'start' 
  });
}

function generatePaymentSchedule(simulation) {
  const scheduleContainer = document.getElementById('paymentSchedule');
  const currentDate = new Date();
  
  let scheduleHTML = `
    <table class="schedule-table">
      <thead>
        <tr>
          <th>Année</th>
          <th>Date d'échéance</th>
          <th>Capital</th>
          <th>Intérêts</th>
          <th>Assurance</th>
          <th>Total à payer</th>
        </tr>
      </thead>
      <tbody>
  `;
  
  for (let i = 1; i <= simulation.duree; i++) {
    const dueDate = new Date(currentDate);
    dueDate.setFullYear(dueDate.getFullYear() + i);
    
    const capital = simulation.montant / simulation.duree;
    const interets = simulation.interetTotal / simulation.duree;
    const assurance = simulation.assuranceTotal / simulation.duree;
    const totalAnnuel = capital + interets + assurance;
    
    scheduleHTML += `
      <tr>
        <td><strong>Année ${i}</strong></td>
        <td>${dueDate.toLocaleDateString('fr-FR')}</td>
        <td>${Math.round(capital).toLocaleString('fr-FR')} Ar</td>
        <td>${Math.round(interets).toLocaleString('fr-FR')} Ar</td>
        <td>${Math.round(assurance).toLocaleString('fr-FR')} Ar</td>
        <td><strong>${Math.round(totalAnnuel).toLocaleString('fr-FR')} Ar</strong></td>
      </tr>
    `;
  }
  
  scheduleHTML += `
      </tbody>
    </table>
  `;
  
  scheduleContainer.innerHTML = scheduleHTML;
}

function hidePreview() {
  document.getElementById('paymentPreview').style.display = 'none';
  currentSimulation = null;
}

function resetForm() {
  document.getElementById('loanDataForm').reset();
  hidePreview();
  updateDurationLimits();
}

function confirmLoanCreation() {
  if (!currentSimulation) {
    alert('Veuillez d\'abord effectuer une simulation');
    return;
  }
  
  if (!confirm('Confirmer la création de cette demande de prêt ?')) {
    return;
  }
  
  const confirmBtn = document.getElementById('confirmBtn');
  const btnText = confirmBtn.querySelector('span');
  
  // Show loading state
  confirmBtn.disabled = true;
  btnText.textContent = 'Création en cours...';
  
  // Create form and submit to existing route
  const form = document.createElement('form');
  form.method = 'POST';
  form.action = '<?= route('/client/loans/create') ?>';
  
  // Add form data
  const formData = [
    { name: 'type_pret_id', value: currentSimulation.type_pret_id },
    { name: 'montant', value: currentSimulation.montant },
    { name: 'duree', value: currentSimulation.duree }
  ];
  
  formData.forEach(data => {
    const input = document.createElement('input');
    input.type = 'hidden';
    input.name = data.name;
    input.value = data.value;
    form.appendChild(input);
  });
  
  document.body.appendChild(form);
  form.submit();
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
</script>
