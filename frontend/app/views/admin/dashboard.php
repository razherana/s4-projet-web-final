<div class="dashboard-wrapper">
  <!-- Quick Actions Section -->
  <section class="quick-actions-section">
    <div class="section-header">
      <h2 class="section-title">Actions Rapides</h2>
      <p class="section-subtitle">Accès rapide aux fonctionnalités principales</p>
    </div>
    
    <div class="actions-grid">
      <a href="<?= route('/admin/prets') ?>" class="action-card primary">
        <div class="action-icon">
          <i class="fas fa-plus"></i>
        </div>
        <div class="action-content">
          <h3>Nouveau Prêt</h3>
          <p>Créer une demande de prêt</p>
        </div>
        <div class="action-arrow">
          <i class="fas fa-arrow-right"></i>
        </div>
      </a>
      
      <a href="<?= route('/admin/clients') ?>" class="action-card secondary">
        <div class="action-icon">
          <i class="fas fa-user-plus"></i>
        </div>
        <div class="action-content">
          <h3>Ajouter Client</h3>
          <p>Enregistrer un nouveau client</p>
        </div>
        <div class="action-arrow">
          <i class="fas fa-arrow-right"></i>
        </div>
      </a>
      
      <a href="<?= route('/admin/fonds') ?>" class="action-card accent">
        <div class="action-icon">
          <i class="fas fa-money-bill-wave"></i>
        </div>
        <div class="action-content">
          <h3>Gérer Fonds</h3>
          <p>Administration des fonds</p>
        </div>
        <div class="action-arrow">
          <i class="fas fa-arrow-right"></i>
        </div>
      </a>
      
      <a href="<?= route('/admin/interets') ?>" class="action-card success">
        <div class="action-icon">
          <i class="fas fa-chart-line"></i>
        </div>
        <div class="action-content">
          <h3>Voir Intérêts</h3>
          <p>Analyse des revenus</p>
        </div>
        <div class="action-arrow">
          <i class="fas fa-arrow-right"></i>
        </div>
      </a>
    </div>
  </section>

  <!-- KPI Metrics Section -->
  <section class="metrics-section">
    <div class="section-header">
      <h2 class="section-title">Indicateurs Clés</h2>
      <p class="section-subtitle">Performance de votre activité financière</p>
    </div>
    
    <div class="metrics-grid">
      <div class="metric-card">
        <div class="metric-header">
          <div class="metric-icon clients">
            <i class="fas fa-users"></i>
          </div>
        </div>
        <div class="metric-content">
          <div class="metric-value" data-target="<?= $totalClients ?>">0</div>
          <div class="metric-label">Clients Actifs</div>
        </div>
        <div class="metric-progress">
          <div class="progress-fill" style="width: 85%"></div>
        </div>
      </div>

      <div class="metric-card">
        <div class="metric-header">
          <div class="metric-icon loans">
            <i class="fas fa-handshake"></i>
          </div>
        </div>
        <div class="metric-content">
          <div class="metric-value" data-target="<?= $totalPrets ?>">0</div>
          <div class="metric-label">Prêts Totaux</div>
        </div>
        <div class="metric-progress">
          <div class="progress-fill" style="width: 92%"></div>
        </div>
      </div>

      <div class="metric-card">
        <div class="metric-header">
          <div class="metric-icon funds">
            <i class="fas fa-piggy-bank"></i>
          </div>
        </div>
        <div class="metric-content">
          <div class="metric-value" data-target="<?= $totalMontantFonds ?>">0</div>
          <div class="metric-label">Fonds (Ar)</div>
        </div>
        <div class="metric-progress">
          <div class="progress-fill" style="width: 76%"></div>
        </div>
      </div>

      <div class="metric-card">
        <div class="metric-header">
          <div class="metric-icon approved">
            <i class="fas fa-check-circle"></i>
          </div>
        </div>
        <div class="metric-content">
          <div class="metric-value" data-target="<?= $pretsApprouves ?>">0</div>
          <div class="metric-label">Prêts Approuvés</div>
        </div>
        <div class="metric-progress">
          <div class="progress-fill" style="width: 88%"></div>
        </div>
      </div>
    </div>
  </section>

  <!-- Analytics Section -->
  <section class="analytics-section">
    <div class="charts-container">
      <!-- Main Chart -->
      <div class="chart-card main-chart">
        <div class="chart-header">
          <div class="chart-info">
            <h3 class="chart-title">Évolution des Prêts et Retours</h3>
            <p class="chart-subtitle">Tendances mensuelles sur 6 mois</p>
          </div>
          <div class="chart-controls">
            <button class="chart-btn active" data-period="6">6M</button>
            <button class="chart-btn" data-period="12">1A</button>
            <button class="chart-btn" data-period="24">2A</button>
          </div>
        </div>
        <div class="chart-body">
          <canvas id="revenueChart"></canvas>
        </div>
      </div>

      <!-- Secondary Charts -->
      <div class="secondary-charts">
        <div class="chart-card">
          <div class="chart-header">
            <div class="chart-info">
              <h3 class="chart-title">Statut des Prêts</h3>
              <p class="chart-subtitle">Répartition actuelle</p>
            </div>
          </div>
          <div class="chart-body">
            <canvas id="loanStatusChart"></canvas>
          </div>
          <div class="chart-legend">
            <div class="legend-item">
              <span class="legend-dot approved"></span>
              <span>Approuvés (<?= $pretsApprouves ?>)</span>
            </div>
            <div class="legend-item">
              <span class="legend-dot pending"></span>
              <span>En Attente (<?= $pretsEnAttente ?>)</span>
            </div>
            <div class="legend-item">
              <span class="legend-dot rejected"></span>
              <span>Refusés (<?= $pretsRefuses ?>)</span>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
</div>

<style>
.dashboard-wrapper {
  display: flex;
  flex-direction: column;
  gap: 2.5rem;
  padding: 0;
}

/* Section Headers */
.section-header {
  margin-bottom: 1.5rem;
}

.section-title {
  font-size: 1.5rem;
  font-weight: 700;
  color: var(--text-primary);
  margin: 0 0 0.25rem 0;
}

.section-subtitle {
  font-size: 0.875rem;
  color: var(--text-secondary);
  margin: 0;
}

/* Quick Actions Section */
.quick-actions-section {
  background: white;
  border-radius: 16px;
  padding: 2rem;
  box-shadow: var(--card-shadow);
}

.actions-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
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
.action-card.accent::before { background: var(--accent-color); }
.action-card.success::before { background: #10B981; }

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

.action-card.accent .action-icon {
  background: linear-gradient(135deg, var(--accent-color), #059669);
}

.action-card.success .action-icon {
  background: linear-gradient(135deg, #10B981, #059669);
}

.action-content {
  flex: 1;
}

.action-content h3 {
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

/* Metrics Section */
.metrics-section {
  background: white;
  border-radius: 16px;
  padding: 2rem;
  box-shadow: var(--card-shadow);
}

.metrics-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
  gap: 1.5rem;
}

.metric-card {
  padding: 1.5rem;
  border: 1px solid var(--border-color);
  border-radius: 12px;
  transition: all 0.3s ease;
  position: relative;
}

.metric-card:hover {
  transform: translateY(-2px);
  box-shadow: var(--card-shadow-hover);
  border-color: var(--primary-color);
}

.metric-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  margin-bottom: 1rem;
}

.metric-icon {
  width: 40px;
  height: 40px;
  border-radius: 10px;
  display: flex;
  align-items: center;
  justify-content: center;
  color: white;
  font-size: 1.125rem;
}

.metric-icon.clients { background: linear-gradient(135deg, var(--primary-color), var(--primary-dark)); }
.metric-icon.loans { background: linear-gradient(135deg, var(--secondary-color), #4F46E5); }
.metric-icon.funds { background: linear-gradient(135deg, var(--accent-color), #059669); }
.metric-icon.approved { background: linear-gradient(135deg, #10B981, #059669); }

.metric-trend {
  display: flex;
  align-items: center;
  gap: 0.25rem;
  font-size: 0.75rem;
  font-weight: 600;
  padding: 0.25rem 0.5rem;
  border-radius: 20px;
  background: rgba(16, 185, 129, 0.1);
  color: #10B981;
}

.metric-content {
  margin-bottom: 1rem;
}

.metric-value {
  font-size: 2rem;
  font-weight: 800;
  color: var(--text-primary);
  margin-bottom: 0.25rem;
  line-height: 1;
}

.metric-label {
  font-size: 0.875rem;
  color: var(--text-secondary);
  font-weight: 500;
}

.metric-progress {
  height: 4px;
  background: #F3F4F6;
  border-radius: 2px;
  overflow: hidden;
}

.progress-fill {
  height: 100%;
  background: linear-gradient(90deg, var(--primary-color), var(--secondary-color));
  border-radius: 2px;
  transition: width 2s ease-in-out;
}

/* Analytics Section */
.analytics-section {
  display: flex;
  flex-direction: column;
  gap: 1.5rem;
}

.charts-container {
  display: grid;
  grid-template-columns: 2fr 1fr;
  gap: 1.5rem;
}

.secondary-charts {
  display: flex;
  flex-direction: column;
  gap: 1.5rem;
}

.chart-card {
  background: white;
  border-radius: 16px;
  box-shadow: var(--card-shadow);
  overflow: hidden;
  transition: all 0.3s ease;
}

.chart-card:hover {
  transform: translateY(-2px);
  box-shadow: var(--card-shadow-hover);
}

.chart-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 1.5rem;
  border-bottom: 1px solid var(--border-color);
}

.chart-info h3 {
  font-size: 1.125rem;
  font-weight: 600;
  color: var(--text-primary);
  margin: 0 0 0.25rem 0;
}

.chart-info p {
  font-size: 0.875rem;
  color: var(--text-secondary);
  margin: 0;
}

.chart-controls {
  display: flex;
  gap: 0.5rem;
}

.chart-btn {
  padding: 0.5rem 1rem;
  border: 2px solid var(--border-color);
  border-radius: 8px;
  background: white;
  color: var(--text-secondary);
  font-size: 0.875rem;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.2s ease;
}

.chart-btn:hover,
.chart-btn.active {
  border-color: var(--primary-color);
  background: var(--primary-color);
  color: white;
}

.chart-body {
  padding: 1.5rem;
  position: relative;
  height: 300px;
}

.main-chart .chart-body {
  height: 400px;
}

.chart-legend {
  padding: 0 1.5rem 1.5rem;
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
}

.legend-item {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  font-size: 0.875rem;
  color: var(--text-secondary);
}

.legend-dot {
  width: 12px;
  height: 12px;
  border-radius: 50%;
}

.legend-dot.approved { background: #10B981; }
.legend-dot.pending { background: #F59E0B; }
.legend-dot.rejected { background: #EF4444; }

/* Activity Section */
.activity-section {
  background: white;
  border-radius: 16px;
  padding: 2rem;
  box-shadow: var(--card-shadow);
}

.activity-timeline {
  display: flex;
  flex-direction: column;
  gap: 1.5rem;
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

.activity-marker {
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

.activity-marker.success { background: linear-gradient(135deg, #10B981, #059669); }
.activity-marker.primary { background: linear-gradient(135deg, var(--primary-color), var(--primary-dark)); }
.activity-marker.warning { background: linear-gradient(135deg, #F59E0B, #D97706); }
.activity-marker.info { background: linear-gradient(135deg, var(--accent-color), #059669); }

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
@media (max-width: 1200px) {
  .charts-container {
    grid-template-columns: 1fr;
  }
  
  .secondary-charts {
    flex-direction: row;
  }
}

@media (max-width: 768px) {
  .dashboard-wrapper {
    gap: 1.5rem;
  }
  
  .actions-grid {
    grid-template-columns: 1fr;
  }
  
  .metrics-grid {
    grid-template-columns: repeat(2, 1fr);
  }
  
  .secondary-charts {
    flex-direction: column;
  }
  
  .chart-header {
    flex-direction: column;
    align-items: flex-start;
    gap: 1rem;
  }
  
  .activity-header {
    flex-direction: column;
    align-items: flex-start;
    gap: 0.25rem;
  }
}

@media (max-width: 480px) {
  .dashboard-wrapper {
    gap: 1rem;
  }
  
  .quick-actions-section,
  .metrics-section,
  .activity-section {
    padding: 1.5rem;
  }
  
  .metrics-grid {
    grid-template-columns: 1fr;
  }
  
  .action-card {
    padding: 1rem;
  }
  
  .metric-card {
    padding: 1rem;
  }
  
  .metric-value {
    font-size: 1.75rem;
  }
  
  .chart-body {
    height: 250px;
    padding: 1rem;
  }
  
  .main-chart .chart-body {
    height: 300px;
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

.metric-value {
  animation: countUp 0.6s ease-out;
}
</style>

<!-- Include required libraries -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
// Counter Animation
function animateCounters() {
  const counters = document.querySelectorAll('.metric-value');
  
  counters.forEach(counter => {
    const target = parseInt(counter.getAttribute('data-target'));
    const increment = target / 100;
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

// Chart Data
const monthlyData = <?= json_encode($monthlyPrets) ?>;
const monthlyRetours = <?= json_encode($monthlyRetours) ?>;

// Revenue Flow Chart
const revenueCtx = document.getElementById('revenueChart').getContext('2d');
const revenueChart = new Chart(revenueCtx, {
  type: 'line',
  data: {
    labels: Object.keys(monthlyData).map(month => {
      const [year, monthNum] = month.split('-');
      const monthNames = ['Jan', 'Fév', 'Mar', 'Avr', 'Mai', 'Jun',
                         'Jul', 'Aoû', 'Sep', 'Oct', 'Nov', 'Déc'];
      return monthNames[parseInt(monthNum) - 1] + ' ' + year.slice(-2);
    }),
    datasets: [{
      label: 'Prêts Accordés',
      data: Object.values(monthlyData),
      borderColor: '#3B82F6',
      backgroundColor: 'rgba(59, 130, 246, 0.1)',
      tension: 0.4,
      fill: true,
      pointBackgroundColor: '#3B82F6',
      pointBorderColor: '#fff',
      pointBorderWidth: 2,
      pointRadius: 4
    }, {
      label: 'Remboursements',
      data: Object.values(monthlyRetours),
      borderColor: '#10B981',
      backgroundColor: 'rgba(16, 185, 129, 0.1)',
      tension: 0.4,
      fill: true,
      pointBackgroundColor: '#10B981',
      pointBorderColor: '#fff',
      pointBorderWidth: 2,
      pointRadius: 4
    }]
  },
  options: {
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
      legend: {
        position: 'top',
        labels: {
          usePointStyle: true,
          padding: 20,
          font: {
            size: 12,
            weight: '500'
          }
        }
      },
      tooltip: {
        backgroundColor: 'rgba(17, 24, 39, 0.9)',
        titleColor: 'white',
        bodyColor: 'white',
        borderColor: '#3B82F6',
        borderWidth: 1,
        cornerRadius: 8,
        callbacks: {
          label: function(context) {
            return context.dataset.label + ': ' + context.parsed.y.toLocaleString('fr-FR') + ' Ar';
          }
        }
      }
    },
    scales: {
      y: {
        beginAtZero: true,
        grid: {
          color: '#F3F4F6'
        },
        ticks: {
          font: {
            size: 11
          },
          callback: function(value) {
            return value.toLocaleString('fr-FR') + ' Ar';
          }
        }
      },
      x: {
        grid: {
          display: false
        },
        ticks: {
          font: {
            size: 11
          }
        }
      }
    },
    interaction: {
      intersect: false,
      mode: 'index'
    }
  }
});

// Loan Status Pie Chart
const statusCtx = document.getElementById('loanStatusChart').getContext('2d');
const statusChart = new Chart(statusCtx, {
  type: 'doughnut',
  data: {
    labels: ['Approuvés', 'En Attente', 'Refusés'],
    datasets: [{
      data: [<?= $pretsApprouves ?>, <?= $pretsEnAttente ?>, <?= $pretsRefuses ?>],
      backgroundColor: ['#10B981', '#F59E0B', '#EF4444'],
      borderColor: '#fff',
      borderWidth: 3,
      hoverOffset: 4
    }]
  },
  options: {
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
      legend: {
        display: false
      },
      tooltip: {
        backgroundColor: 'rgba(17, 24, 39, 0.9)',
        titleColor: 'white',
        bodyColor: 'white',
        callbacks: {
          label: function(context) {
            const total = context.dataset.data.reduce((a, b) => a + b, 0);
            const percentage = total > 0 ? ((context.parsed / total) * 100).toFixed(1) : 0;
            return context.label + ': ' + context.parsed + ' (' + percentage + '%)';
          }
        }
      }
    },
    cutout: '60%'
  }
});

// Initialize counters
animateCounters();

// Chart period controls
document.querySelectorAll('.chart-btn').forEach(button => {
  button.addEventListener('click', function() {
    document.querySelectorAll('.chart-btn').forEach(btn => btn.classList.remove('active'));
    this.classList.add('active');
    
    console.log('Period changed to:', this.dataset.period);
  });
});

// Add smooth scroll behavior for better UX
document.documentElement.style.scrollBehavior = 'smooth';
</script>
