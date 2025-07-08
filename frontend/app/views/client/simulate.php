<link rel="stylesheet" href="<?= route('/css/client_simulate.css') ?>">

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

  <!-- Active Simulations Section -->
  <section class="simulations-section" id="simulationsSection" style="display: none;">
    <div class="simulations-card">
      <div class="simulations-header">
        <h3 class="simulations-title">
          <i class="fas fa-list"></i>
          Simulations Actives
        </h3>
        <p class="simulations-subtitle">Gérez vos simulations de prêt</p>
        <button type="button" class="btn-clear-all" onclick="clearAllSimulations()">
          <i class="fas fa-trash"></i>
          Tout effacer
        </button>
      </div>
      
      <div class="simulations-grid" id="simulationsGrid">
        <!-- Populated by JavaScript -->
      </div>
    </div>
  </section>

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
              Date de prets
            </label>
            <input type="date" id="datePret" name="datePret" class="form-control" required oninput="calculatePayment()">
            <small class="form-help">Date de l'emprunt</small>
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
              Durée (mois)
            </label>
            <input type="number" id="duree" name="duree" class="form-control" required min="1" oninput="calculatePayment()" placeholder="Ex: 5">
            <small class="form-help" id="dureeHelp">Durée de remboursement en années</small>
          </div>
          <div class="form-group">
            <label for="duree" class="form-label">
              <i class="fas fa-calendar-alt"></i>
              Delai avant remboursement (mois)
            </label>
            <input type="number" id="delai" name="delai" class="form-control" required min="1" oninput="calculatePayment()" placeholder="Ex: 5">
            <small class="form-help" id="dureeHelp"></small>
          </div>
          
          <div class="form-group">
            <div class="simulation-actions">
              <button type="button" class="btn-simulate" onclick="addSimulation()">
                <i class="fas fa-plus"></i>
                <span>Ajouter Simulation</span>
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
              <div class="summary-label">Paiement Mensuel</div>
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

<script>
let currentSimulation = null;
let allSimulations = [];
let simulationCounter = 0;
let typePrets = <?= json_encode($typePrets) ?>;

function updateDurationLimits() {
  const typePretSelect = document.getElementById('typePretId');
  const dureeInput = document.getElementById('duree');
  const dureeHelp = document.getElementById('dureeHelp');
  const delai = document.getElementById('delai')
  
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
  const delai = parseInt(document.getElementById('delai').value) || 0;

  if (!typePretSelect.value || !montant || !duree || !delai) {
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
  
  const montantMensuel = montant / duree;
  const interetTotal = montant * (tauxInteret / 100) * (duree + delai);
  const assuranceTotal = montant * (tauxAssurance / 100) * (duree + delai);
  const paiementMensuel = montantMensuel + (interetTotal / duree) + (assuranceTotal / duree);
  const totalARembourser = montant + interetTotal + assuranceTotal;
  
  currentSimulation = {
    id: null,
    type_pret_id: typePretSelect.value,
    type_pret_nom: selectedOption.textContent,
    montant: montant + (montant * (tauxAssurance / 100 + tauxInteret / 100) * delai), // Include insurance for delay
    duree: duree,
    delai: delai,
    datePret: document.getElementById('datePret').value,
    paiementMensuel: paiementMensuel,
    totalARembourser: totalARembourser,
    interetTotal: interetTotal,
    assuranceTotal: assuranceTotal,
    tauxInteret: tauxInteret,
    tauxAssurance: tauxAssurance
  };
  
  showPreview(currentSimulation);
}

function addSimulation() {
  if (!currentSimulation) {
    alert('Veuillez d\'abord effectuer une simulation');
    return;
  }
  
  // Check if simulation already exists
  const existingIndex = allSimulations.findIndex(sim => 
    sim.type_pret_id === currentSimulation.type_pret_id &&
    sim.montant === currentSimulation.montant &&
    sim.duree === currentSimulation.duree &&
    sim.delai === currentSimulation.delai &&
    sim.datePret === currentSimulation.datePret
  );
  
  if (existingIndex !== -1) {
    alert('Cette simulation existe déjà');
    return;
  }
  
  simulationCounter++;
  currentSimulation.id = simulationCounter;
  allSimulations.push({...currentSimulation});
  
  updateSimulationsDisplay();
  resetForm();
  
  // Show success message
  const successMsg = document.createElement('div');
  successMsg.className = 'alert alert-success alert-dismissible fade show';
  successMsg.innerHTML = `
    <i class="fas fa-check-circle"></i>
    Simulation ajoutée avec succès !
    <button type="button" class="btn-close" onclick="this.parentElement.remove()"></button>
  `;
  document.querySelector('.simulate-dashboard').insertBefore(successMsg, document.querySelector('.loan-form-section'));
  
  setTimeout(() => successMsg.remove(), 3000);
}

function updateSimulationsDisplay() {
  const simulationsSection = document.getElementById('simulationsSection');
  const simulationsGrid = document.getElementById('simulationsGrid');
  
  if (allSimulations.length === 0) {
    simulationsSection.style.display = 'none';
    return;
  }
  
  simulationsSection.style.display = 'block';
  
  let simulationsHTML = '';
  allSimulations.forEach(simulation => {
    simulationsHTML += `
      <div class="simulation-card" data-id="${simulation.id}">
        <div class="simulation-header">
          <h4 class="simulation-title">Simulation #${simulation.id}</h4>
          <div class="simulation-actions">
            <button type="button" class="btn-action edit" onclick="editSimulation(${simulation.id})" title="Modifier">
              <i class="fas fa-edit"></i>
            </button>
            <button type="button" class="btn-action delete" onclick="removeSimulation(${simulation.id})" title="Supprimer">
              <i class="fas fa-trash"></i>
            </button>
          </div>
        </div>
        
        <div class="simulation-details">
          <div class="detail-row">
            <span class="detail-label">Type:</span>
            <span class="detail-value">${simulation.type_pret_nom}</span>
          </div>
          <div class="detail-row">
            <span class="detail-label">Montant:</span>
            <span class="detail-value">${simulation.montant.toLocaleString('fr-FR')} Ar</span>
          </div>
          <div class="detail-row">
            <span class="detail-label">Durée:</span>
            <span class="detail-value">${simulation.duree} mois</span>
          </div>
          <div class="detail-row">
            <span class="detail-label">Délai:</span>
            <span class="detail-value">${simulation.delai} mois</span>
          </div>
          <div class="detail-row">
            <span class="detail-label">Date:</span>
            <span class="detail-value">${new Date(simulation.datePret).toLocaleDateString('fr-FR')}</span>
          </div>
          <div class="detail-row highlight">
            <span class="detail-label">Paiement mensuel:</span>
            <span class="detail-value">${Math.round(simulation.paiementMensuel).toLocaleString('fr-FR')} Ar</span>
          </div>
          <div class="detail-row highlight">
            <span class="detail-label">Total à rembourser:</span>
            <span class="detail-value">${Math.round(simulation.totalARembourser).toLocaleString('fr-FR')} Ar</span>
          </div>
        </div>
        
        <div class="simulation-footer">
          <button type="button" class="btn-confirm" onclick="confirmSimulation(${simulation.id})">
            <i class="fas fa-check"></i>
            Confirmer ce Prêt
          </button>
          <button type="button" class="btn-details" onclick="showSimulationDetails(${simulation.id})">
            <i class="fas fa-eye"></i>
            Voir Détails
          </button>
        </div>
      </div>
    `;
  });
  
  simulationsGrid.innerHTML = simulationsHTML;
}

function removeSimulation(id) {
  if (!confirm('Êtes-vous sûr de vouloir supprimer cette simulation ?')) {
    return;
  }
  
  allSimulations = allSimulations.filter(sim => sim.id !== id);
  updateSimulationsDisplay();
}

function editSimulation(id) {
  const simulation = allSimulations.find(sim => sim.id === id);
  if (!simulation) return;
  
  // Fill form with simulation data
  document.getElementById('typePretId').value = simulation.type_pret_id;
  document.getElementById('montant').value = simulation.montant - (simulation.montant * (simulation.tauxAssurance / 100 + simulation.tauxInteret / 100) * simulation.delai);
  document.getElementById('duree').value = simulation.duree;
  document.getElementById('delai').value = simulation.delai;
  document.getElementById('datePret').value = simulation.datePret;
  
  // Remove simulation from list
  allSimulations = allSimulations.filter(sim => sim.id !== id);
  updateSimulationsDisplay();
  
  // Trigger calculation
  updateDurationLimits();
  calculatePayment();
}

function showSimulationDetails(id) {
  const simulation = allSimulations.find(sim => sim.id === id);
  if (!simulation) return;
  
  currentSimulation = simulation;
  showPreview(simulation);
}

function confirmSimulation(id) {
  const simulation = allSimulations.find(sim => sim.id === id);
  if (!simulation) return;
  
  currentSimulation = simulation;
  confirmLoanCreation();
}

function clearAllSimulations() {
  if (allSimulations.length === 0) return;
  
  if (!confirm('Êtes-vous sûr de vouloir supprimer toutes les simulations ?')) {
    return;
  }
  
  allSimulations = [];
  updateSimulationsDisplay();
  hidePreview();
}

function showPreview(simulation) {
  // Update summary cards
  document.getElementById('summaryMontant').textContent = simulation.montant.toLocaleString('fr-FR') + ' Ar';
  document.getElementById('summaryPaiementAnnuel').textContent = Math.round(simulation.paiementMensuel).toLocaleString('fr-FR') + ' Ar';
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
  const currentDate = new Date(simulation.datePret);
  const delai = simulation.delai;
  currentDate.setMonth(currentDate.getMonth() + delai);

  
  let scheduleHTML = `
    <table class="schedule-table">
      <thead>
        <tr>
          <th>Mois</th>
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
    dueDate.setMonth(dueDate.getMonth() + i);
    
    const capital = simulation.montant / simulation.duree;
    const interets = simulation.interetTotal / simulation.duree;
    const assurance = simulation.assuranceTotal / simulation.duree;
    const totalAnnuel = capital + interets + assurance;
    
    scheduleHTML += `
      <tr>
        <td><strong>Mois ${i}</strong></td>
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
}

function resetForm() {
  document.getElementById('loanDataForm').reset();
  hidePreview();
  currentSimulation = null;
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
  if (confirmBtn) {
    const btnText = confirmBtn.querySelector('span');
    
    // Show loading state
    confirmBtn.disabled = true;
    btnText.textContent = 'Création en cours...';
  }
  
  // Create form and submit to existing route
  const form = document.createElement('form');
  form.method = 'POST';
  form.action = '<?= route('/client/loans/create') ?>';
  const currentDate = new Date(currentSimulation.datePret);
  
  // Add form data
  const formData = [
    { name: 'type_pret_id', value: currentSimulation.type_pret_id },
    { name: 'montant', value: currentSimulation.montant },
    { name: 'duree', value: currentSimulation.duree },
    { name: 'date_creation', value: currentDate.toISOString() },
    { name: 'delai', value: currentSimulation.delai }
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
