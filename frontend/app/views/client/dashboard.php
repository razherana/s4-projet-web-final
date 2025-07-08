<div class="client-dashboard">
  <!-- Welcome Section -->
  <section class="welcome-section">
    <div class="welcome-card">
      <div class="welcome-content">
        <h2 class="welcome-title">Bonjour, <?= htmlspecialchars($user['prenom']) ?> !</h2>
        <p class="welcome-subtitle">Voici un aperçu de votre activité financière</p>
      </div>
      <div class="welcome-icon">
        <i class="fas fa-hand-wave"></i>
      </div>
    </div>
  </section>

  <!-- Quick Stats Section -->
  <section class="stats-section">
    <div class="stats-grid">
      <div class="stat-card primary">
        <div class="stat-icon">
          <i class="fas fa-handshake"></i>
        </div>
        <div class="stat-content">
          <div class="stat-value" data-target="<?= $totalPrets ?>">0</div>
          <div class="stat-label">Prêts Totaux</div>
        </div>
        <div class="stat-trend positive">
          <i class="fas fa-arrow-up"></i>
          <span>+<?= $totalPrets ?></span>
        </div>
      </div>

      <div class="stat-card success">
        <div class="stat-icon">
          <i class="fas fa-check-circle"></i>
        </div>
        <div class="stat-content">
          <div class="stat-value" data-target="<?= $activePrets ?>">0</div>
          <div class="stat-label">Prêts Actifs</div>
        </div>
        <div class="stat-trend positive">
          <i class="fas fa-arrow-up"></i>
          <span>+<?= $activePrets ?></span>
        </div>
      </div>

      <div class="stat-card warning">
        <div class="stat-icon">
          <i class="fas fa-clock"></i>
        </div>
        <div class="stat-content">
          <div class="stat-value" data-target="<?= $pendingPrets ?>">0</div>
          <div class="stat-label">En Attente</div>
        </div>
        <div class="stat-trend neutral">
          <i class="fas fa-minus"></i>
          <span><?= $pendingPrets ?></span>
        </div>
      </div>

      <div class="stat-card info">
        <div class="stat-icon">
          <i class="fas fa-coins"></i>
        </div>
        <div class="stat-content">
          <div class="stat-value" data-target="<?= $totalMontant ?>">0</div>
          <div class="stat-label">Montant Total (Ar)</div>
        </div>
        <div class="stat-trend positive">
          <i class="fas fa-arrow-up"></i>
          <span>Total</span>
        </div>
      </div>
    </div>
  </section>

  <!-- Quick Actions Section -->
  <section class="actions-section">
    <div class="section-header">
      <h3 class="section-title">Actions Rapides</h3>
      <p class="section-subtitle">Accès rapide aux fonctionnalités principales</p>
    </div>
    
    <div class="actions-grid">
      <a href="<?= route('/client/loans') ?>" class="action-card primary">
        <div class="action-icon">
          <i class="fas fa-list"></i>
        </div>
        <div class="action-content">
          <h4>Voir Mes Prêts</h4>
          <p>Consultez l'état de vos demandes</p>
        </div>
        <div class="action-arrow">
          <i class="fas fa-chevron-right"></i>
        </div>
      </a>
      
      <a href="<?= route('/client/simulate') ?>" class="action-card secondary">
        <div class="action-icon">
          <i class="fas fa-calculator"></i>
        </div>
        <div class="action-content">
          <h4>Simuler un Prêt</h4>
          <p>Calculez votre capacité d'emprunt</p>
        </div>
        <div class="action-arrow">
          <i class="fas fa-chevron-right"></i>
        </div>
      </a>
    </div>
  </section>

  <!-- Recent Activity Section -->
  <?php if (!empty($recentPrets)): ?>
  <section class="activity-section">
    <div class="section-header">
      <h3 class="section-title">Activité Récente</h3>
      <p class="section-subtitle">Vos dernières demandes de prêts</p>
    </div>
    
    <div class="activity-list">
      <?php foreach ($recentPrets as $pret): ?>
        <div class="activity-item">
          <div class="activity-icon <?= !empty($pret['date_acceptation']) ? 'success' : (!empty($pret['date_refus']) ? 'danger' : 'warning') ?>">
            <i class="fas <?= !empty($pret['date_acceptation']) ? 'fa-check' : (!empty($pret['date_refus']) ? 'fa-times' : 'fa-clock') ?>"></i>
          </div>
          <div class="activity-content">
            <div class="activity-header">
              <h5 class="activity-title">Prêt #<?= $pret['id'] ?></h5>
              <span class="activity-time"><?= date('d/m/Y', strtotime($pret['date_creation'])) ?></span>
            </div>
            <p class="activity-description">
              Montant: <?= number_format($pret['montant'], 0, ',', ' ') ?> Ar - 
              Durée: <?= $pret['duree'] ?> mois - 
              Statut: <?= !empty($pret['date_acceptation']) ? 'Approuvé' : (!empty($pret['date_refus']) ? 'Refusé' : 'En attente') ?>
            </p>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  </section>
  <?php endif; ?>
</div>

<style>
.client-dashboard {
  display: flex;
  flex-direction: column;
  gap: 2rem;
  padding: 0;
}

/* Welcome Section */
.welcome-section {
  margin-bottom: 1rem;
}

.welcome-card {
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

.welcome-card::before {
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

.welcome-content {
  position: relative;
  z-index: 2;
}

.welcome-title {
  font-size: 1.75rem;
  font-weight: 700;
  margin: 0 0 0.5rem 0;
}

.welcome-subtitle {
  font-size: 1rem;
  opacity: 0.9;
  margin: 0;
}

.welcome-icon {
  font-size: 3rem;
  opacity: 0.3;
  position: relative;
  z-index: 1;
}

/* Stats Section */
.stats-section {
  margin-bottom: 1rem;
}

.stats-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
  gap: 1.5rem;
}

.stat-card {
  background: white;
  border-radius: 12px;
  padding: 1.5rem;
  box-shadow: var(--card-shadow);
  display: flex;
  align-items: center;
  gap: 1rem;
  transition: all 0.3s ease;
  position: relative;
  overflow: hidden;
}

.stat-card::before {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  height: 4px;
  background: var(--primary-color);
}

.stat-card.success::before { background: var(--accent-color); }
.stat-card.warning::before { background: #F59E0B; }
.stat-card.info::before { background: var(--secondary-color); }

.stat-card:hover {
  transform: translateY(-4px);
  box-shadow: var(--card-shadow-hover);
}

.stat-icon {
  width: 60px;
  height: 60px;
  border-radius: 12px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 1.5rem;
  color: white;
  background: linear-gradient(135deg, var(--primary-color), var(--primary-dark));
}

.stat-card.success .stat-icon { background: linear-gradient(135deg, var(--accent-color), #059669); }
.stat-card.warning .stat-icon { background: linear-gradient(135deg, #F59E0B, #D97706); }
.stat-card.info .stat-icon { background: linear-gradient(135deg, var(--secondary-color), #4F46E5); }

.stat-content {
  flex: 1;
}

.stat-value {
  font-size: 2rem;
  font-weight: 800;
  color: var(--text-primary);
  margin-bottom: 0.25rem;
  line-height: 1;
}

.stat-label {
  font-size: 0.875rem;
  color: var(--text-secondary);
  font-weight: 500;
}

.stat-trend {
  display: flex;
  align-items: center;
  gap: 0.25rem;
  font-size: 0.75rem;
  font-weight: 600;
  padding: 0.25rem 0.5rem;
  border-radius: 20px;
}

.stat-trend.positive {
  background: rgba(16, 185, 129, 0.1);
  color: var(--accent-color);
}

.stat-trend.neutral {
  background: rgba(107, 114, 128, 0.1);
  color: var(--text-secondary);
}

/* Sections */
.section-header {
  margin-bottom: 1.5rem;
}

.section-title {
  font-size: 1.25rem;
  font-weight: 700;
  color: var(--text-primary);
  margin: 0 0 0.25rem 0;
}

.section-subtitle {
  font-size: 0.875rem;
  color: var(--text-secondary);
  margin: 0;
}

/* Actions Section */
.actions-section {
  background: white;
  border-radius: 16px;
  padding: 2rem;
  box-shadow: var(--card-shadow);
}

.actions-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
  gap: 1.5rem;
}

.action-card {
  display: flex;
  align-items: center;
  gap: 1rem;
  padding: 1.5rem;
  border: 2px solid var(--border-color);
  border-radius: 12px;
  text-decoration: none;
  color: var(--text-primary);
  transition: all 0.3s ease;
  position: relative;
  overflow: hidden;
}

.action-card::before {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  height: 4px;
  background: var(--primary-color);
  transform: scaleX(0);
  transition: transform 0.3s ease;
}

.action-card:hover {
  transform: translateY(-2px);
  box-shadow: var(--card-shadow-hover);
  border-color: var(--primary-color);
  text-decoration: none;
  color: var(--text-primary);
}

.action-card:hover::before {
  transform: scaleX(1);
}

.action-card.secondary::before { background: var(--secondary-color); }

.action-icon {
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

.action-card.secondary .action-icon {
  background: linear-gradient(135deg, var(--secondary-color), #4F46E5);
}

.action-content {
  flex: 1;
}

.action-content h4 {
  font-size: 1rem;
  font-weight: 600;
  margin: 0 0 0.25rem 0;
  color: var(--text-primary);
}

.action-content p {
  font-size: 0.875rem;
  color: var(--text-secondary);
  margin: 0;
}

.action-arrow {
  color: var(--text-secondary);
  transition: all 0.3s ease;
}

.action-card:hover .action-arrow {
  color: var(--primary-color);
  transform: translateX(4px);
}

/* Activity Section */
.activity-section {
  background: white;
  border-radius: 16px;
  padding: 2rem;
  box-shadow: var(--card-shadow);
}

.activity-list {
  display: flex;
  flex-direction: column;
  gap: 1rem;
}

.activity-item {
  display: flex;
  align-items: flex-start;
  gap: 1rem;
  padding: 1rem;
  border-radius: 12px;
  transition: all 0.3s ease;
}

.activity-item:hover {
  background: #F8FAFC;
  transform: translateX(4px);
}

.activity-icon {
  width: 40px;
  height: 40px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  color: white;
  font-size: 1rem;
  flex-shrink: 0;
}

.activity-icon.success { background: linear-gradient(135deg, var(--accent-color), #059669); }
.activity-icon.warning { background: linear-gradient(135deg, #F59E0B, #D97706); }
.activity-icon.danger { background: linear-gradient(135deg, #EF4444, #DC2626); }

.activity-content {
  flex: 1;
}

.activity-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  margin-bottom: 0.5rem;
}

.activity-title {
  font-size: 0.875rem;
  font-weight: 600;
  color: var(--text-primary);
  margin: 0;
}

.activity-time {
  font-size: 0.75rem;
  color: var(--text-secondary);
}

.activity-description {
  font-size: 0.875rem;
  color: var(--text-secondary);
  margin: 0;
  line-height: 1.4;
}

/* Responsive Design */
@media (max-width: 768px) {
  .client-dashboard {
    gap: 1.5rem;
  }
  
  .welcome-card {
    padding: 1.5rem;
    flex-direction: column;
    text-align: center;
    gap: 1rem;
  }
  
  .welcome-title {
    font-size: 1.5rem;
  }
  
  .stats-grid {
    grid-template-columns: repeat(2, 1fr);
  }
  
  .actions-grid {
    grid-template-columns: 1fr;
  }
  
  .actions-section,
  .activity-section {
    padding: 1.5rem;
  }
}

@media (max-width: 480px) {
  .client-dashboard {
    gap: 1rem;
  }
  
  .stats-grid {
    grid-template-columns: 1fr;
  }
  
  .stat-card {
    padding: 1rem;
  }
  
  .stat-value {
    font-size: 1.75rem;
  }
  
  .welcome-card {
    padding: 1rem;
  }
  
  .actions-section,
  .activity-section {
    padding: 1rem;
  }
}

/* Animation for counter values */
@keyframes countUp {
  from {
    opacity: 0;
    transform: translateY(10px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.stat-value {
  animation: countUp 0.6s ease-out;
}
</style>

<script>
// Counter Animation
function animateCounters() {
  const counters = document.querySelectorAll('.stat-value');
  
  counters.forEach(counter => {
    const target = parseInt(counter.getAttribute('data-target'));
    const increment = target / 50;
    let current = 0;
    
    const updateCounter = () => {
      if (current < target) {
        current += increment;
        counter.textContent = Math.floor(current).toLocaleString('fr-FR');
        requestAnimationFrame(updateCounter);
      } else {
        counter.textContent = target.toLocaleString('fr-FR');
      }
    };
    
    // Start animation when element is in viewport
    const observer = new IntersectionObserver((entries) => {
      entries.forEach(entry => {
        if (entry.isIntersecting) {
          updateCounter();
          observer.unobserve(entry.target);
        }
      });
    });
    
    observer.observe(counter);
  });
}

// Initialize counters
animateCounters();
</script>
