let currentSimulation = null;
let allSimulations = [];
let simulationCounter = 0;
let typePrets = [];
let preUrl = '';

// Initialize the simulation system
function initializeSimulator(typePretsData, preUrlParam) {
  preUrl = preUrlParam;
  typePrets = typePretsData;
  loadSavedSimulations();
  
  // Auto-hide alerts after 5 seconds
  const alerts = document.querySelectorAll('.alert-dismissible');
  alerts.forEach(alert => {
    setTimeout(() => {
      const closeBtn = alert.querySelector('.btn-close');
      if (closeBtn) {
        closeBtn.click();
      }
    }, 5000);
  });
}

// Load saved simulations from server
async function loadSavedSimulations() {
  try {
    const response = await fetch(preUrl + '/api/simulations/my-simulations');
    const simulations = await response.json();
    
    if (simulations && simulations.length > 0) {
      allSimulations = simulations.map(sim => ({
        id: sim.id,
        type_pret_id: sim.type_pret_id,
        type_pret_nom: getTypePretName(sim.type_pret_id),
        montant: sim.montant,
        duree: sim.duree,
        delai: sim.delai,
        datePret: sim.date_creation.split(' ')[0],
        paiementMensuel: calculateMonthlyPayment(sim),
        totalARembourser: calculateTotalAmount(sim),
        interetTotal: calculateInterest(sim),
        assuranceTotal: calculateInsurance(sim),
        tauxInteret: getTypePretData(sim.type_pret_id).taux_interet,
        tauxAssurance: getTypePretData(sim.type_pret_id).taux_assurance,
        saved: true
      }));
      
      simulationCounter = Math.max(...allSimulations.map(s => s.id)) || 0;
      updateSimulationsDisplay();
    }
  } catch (error) {
    console.error('Error loading saved simulations:', error);
  }
}

function getTypePretName(typePretId) {
  const type = typePrets.find(t => t.id == typePretId);
  return type ? type.nom : 'Type inconnu';
}

function getTypePretData(typePretId) {
  return typePrets.find(t => t.id == typePretId) || {};
}

function calculateMonthlyPayment(simulation) {
  const typeData = getTypePretData(simulation.type_pret_id);
  const montantMensuel = simulation.montant / simulation.duree;
  const interetTotal = simulation.montant * (typeData.taux_interet / 100) * (simulation.duree + simulation.delai);
  const assuranceTotal = simulation.montant * ((typeData.taux_assurance || 0) / 100) * (simulation.duree + simulation.delai);
  return montantMensuel + (interetTotal / simulation.duree) + (assuranceTotal / simulation.duree);
}

function calculateTotalAmount(simulation) {
  const typeData = getTypePretData(simulation.type_pret_id);
  const interetTotal = simulation.montant * (typeData.taux_interet / 100) * (simulation.duree + simulation.delai);
  const assuranceTotal = simulation.montant * ((typeData.taux_assurance || 0) / 100) * (simulation.duree + simulation.delai);
  return simulation.montant + interetTotal + assuranceTotal;
}

function calculateInterest(simulation) {
  const typeData = getTypePretData(simulation.type_pret_id);
  return simulation.montant * (typeData.taux_interet / 100) * (simulation.duree + simulation.delai);
}

function calculateInsurance(simulation) {
  const typeData = getTypePretData(simulation.type_pret_id);
  return simulation.montant * ((typeData.taux_assurance || 0) / 100) * (simulation.duree + simulation.delai);
}

function updateDurationLimits() {
  const typePretSelect = document.getElementById('typePretId');
  const dureeInput = document.getElementById('duree');
  const dureeHelp = document.getElementById('dureeHelp');
  
  if (!typePretSelect.value) {
    dureeHelp.textContent = 'Durée de remboursement en mois';
    dureeInput.removeAttribute('min');
    dureeInput.removeAttribute('max');
    return;
  }
  
  const selectedOption = typePretSelect.options[typePretSelect.selectedIndex];
  const dureeMin = parseInt(selectedOption.dataset.dureeMin) || 1;
  const dureeMax = parseInt(selectedOption.dataset.dureeMax) || 30;
  
  dureeInput.setAttribute('min', dureeMin);
  dureeInput.setAttribute('max', dureeMax);
  dureeHelp.textContent = `Durée autorisée: ${dureeMin} - ${dureeMax} mois`;
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
    montant: montant + (montant * (tauxAssurance / 100 + tauxInteret / 100) * delai),
    duree: duree,
    delai: delai,
    datePret: document.getElementById('datePret').value,
    paiementMensuel: paiementMensuel,
    totalARembourser: totalARembourser,
    interetTotal: interetTotal,
    assuranceTotal: assuranceTotal,
    tauxInteret: tauxInteret,
    tauxAssurance: tauxAssurance,
    saved: false
  };
  
  showPreview(currentSimulation);
}

async function addSimulation() {
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
  
  try {
    // Save to server
    const response = await fetch(preUrl + '/client/simulations/save', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/x-www-form-urlencoded',
      },
      body: new URLSearchParams({
        type_pret_id: currentSimulation.type_pret_id,
        montant: currentSimulation.montant,
        duree: currentSimulation.duree,
        delai: currentSimulation.delai,
        date_creation: currentSimulation.datePret + ' ' + new Date().toTimeString().split(' ')[0]
      })
    });
    
    const result = await response.json();
    
    if (result.success) {
      currentSimulation.id = result.id;
      currentSimulation.saved = true;
      allSimulations.push({...currentSimulation});
      
      updateSimulationsDisplay();
      resetForm();
      
      showSuccessMessage('Simulation sauvegardée avec succès !');
    } else {
      alert('Erreur lors de la sauvegarde: ' + (result.error || 'Erreur inconnue'));
    }
  } catch (error) {
    console.error('Error saving simulation:', error);
    alert('Erreur lors de la sauvegarde de la simulation');
  }
}

function showSuccessMessage(message) {
  const successMsg = document.createElement('div');
  successMsg.className = 'alert alert-success alert-dismissible fade show';
  successMsg.innerHTML = `
    <i class="fas fa-check-circle"></i>
    ${message}
    <button type="button" class="btn-close" onclick="this.parentElement.remove()"></button>
  `;
  document.querySelector('.simulate-dashboard').insertBefore(successMsg, document.querySelector('.simulations-section'));
  
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
    const savedBadge = simulation.saved ? '<span class="saved-badge"><i class="fas fa-save"></i> Sauvegardé</span>' : '';
    
    simulationsHTML += `
      <div class="simulation-card ${simulation.saved ? 'saved' : ''}" data-id="${simulation.id}">
        <div class="simulation-header">
          <h4 class="simulation-title">Simulation #${simulation.id} ${savedBadge}</h4>
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

async function removeSimulation(id) {
  if (!confirm('Êtes-vous sûr de vouloir supprimer cette simulation ?')) {
    return;
  }
  
  const simulation = allSimulations.find(sim => sim.id === id);
  
  if (simulation && simulation.saved) {
    try {
      const response = await fetch(preUrl + `/client/simulations/${id}`, {
        method: 'DELETE'
      });
      
      const result = await response.json();
      
      if (!result.success) {
        alert('Erreur lors de la suppression: ' + (result.error || 'Erreur inconnue'));
        return;
      }
    } catch (error) {
      console.error('Error deleting simulation:', error);
      alert('Erreur lors de la suppression de la simulation');
      return;
    }
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

async function clearAllSimulations() {
  if (allSimulations.length === 0) return;
  
  if (!confirm('Êtes-vous sûr de vouloir supprimer toutes les simulations ?')) {
    return;
  }
  
  try {
    const response = await fetch(preUrl + '/client/simulations/clear', {
      method: 'DELETE'
    });
    
    const result = await response.json();
    
    if (result.success) {
      allSimulations = [];
      updateSimulationsDisplay();
      hidePreview();
      showSuccessMessage('Toutes les simulations ont été supprimées');
    } else {
      alert('Erreur lors de la suppression: ' + (result.error || 'Erreur inconnue'));
    }
  } catch (error) {
    console.error('Error clearing simulations:', error);
    alert('Erreur lors de la suppression des simulations');
  }
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
  form.action = '/client/loans/create';
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
