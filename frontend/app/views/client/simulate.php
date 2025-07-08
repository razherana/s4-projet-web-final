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
        <div>
          <h3 class="simulations-title">
            <i class="fas fa-list"></i>
            Simulations Actives
          </h3>
          <p class="simulations-subtitle">Gérez vos simulations de prêt</p>
        </div>
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

<script src="<?= route('/js/client-simulate.js') ?>"></script>
<script>
  // Initialize the simulator when page loads
  document.addEventListener('DOMContentLoaded', function() {
    const typePrets = <?= json_encode($typePrets) ?>;
    initializeSimulator(typePrets, <?= route('') ?>);
  });
</script>
