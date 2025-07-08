<div class="interests-dashboard">
  <!-- Period Selection Section -->
  <div class="period-section">
    <div class="period-card">
      <div class="card-header-custom">
        <div class="header-icon">
          <i class="fas fa-calendar-alt"></i>
        </div>
        <div class="header-content">
          <h2 class="header-title">Période d'Analyse</h2>
          <p class="header-subtitle">Sélectionnez la période pour analyser les intérêts</p>
        </div>
      </div>
      
      <form id="interetsForm" class="period-form">
        <div class="form-grid">
          <div class="form-group">
            <label for="mois1" class="form-label">Mois de début</label>
            <select class="form-control" id="mois1" name="mois1" required>
              <option value="">Sélectionner...</option>
              <option value="1">Janvier</option>
              <option value="2">Février</option>
              <option value="3">Mars</option>
              <option value="4">Avril</option>
              <option value="5">Mai</option>
              <option value="6">Juin</option>
              <option value="7">Juillet</option>
              <option value="8">Août</option>
              <option value="9">Septembre</option>
              <option value="10">Octobre</option>
              <option value="11">Novembre</option>
              <option value="12">Décembre</option>
            </select>
          </div>
          
          <div class="form-group">
            <label for="annee1" class="form-label">Mois de début</label>
            <input type="number" class="form-control" id="annee1" name="annee1" min="2020" max="2030" value="<?= date('Y') ?>" required>
          </div>
          
          <div class="form-group">
            <label for="mois2" class="form-label">Mois de fin (optionnel)</label>
            <select class="form-control" id="mois2" name="mois2">
              <option value="">Même mois</option>
              <option value="1">Janvier</option>
              <option value="2">Février</option>
              <option value="3">Mars</option>
              <option value="4">Avril</option>
              <option value="5">Mai</option>
              <option value="6">Juin</option>
              <option value="7">Juillet</option>
              <option value="8">Août</option>
              <option value="9">Septembre</option>
              <option value="10">Octobre</option>
              <option value="11">Novembre</option>
              <option value="12">Décembre</option>
            </select>
          </div>
          
          <div class="form-group">
            <label for="annee2" class="form-label">Mois de fin (optionnel)</label>
            <input type="number" class="form-control" id="annee2" name="annee2" min="2020" max="2030" value="<?= date('Y') ?>">
          </div>
        </div>
        
        <div class="form-actions">
          <button type="submit" class="btn-analyze">
            <i class="fas fa-chart-line"></i>
            <span>Analyser les Intérêts</span>
          </button>
        </div>
      </form>
    </div>
  </div>

  <!-- Summary Cards Section -->
  <div class="summary-section" id="summaryContainer" style="display: none;">
    <div class="summary-grid">
      <div class="summary-card primary">
        <div class="card-icon">
          <i class="fas fa-coins"></i>
        </div>
        <div class="card-content">
          <div class="metric-value" id="totalInterets">0 Ar</div>
          <div class="metric-label">Total des Intérêts</div>
          <div class="metric-change" id="totalChange">+0.00%</div>
        </div>
      </div>
      
      <div class="summary-card warning">
        <div class="card-icon">
          <i class="fas fa-chart-bar"></i>
        </div>
        <div class="card-content">
          <div class="metric-value" id="moyenneInterets">0 Ar</div>
          <div class="metric-label">Moyenne Mensuelle</div>
          <div class="metric-change" id="moyenneChange">+1.31%</div>
        </div>
      </div>
      
      <div class="summary-card success">
        <div class="card-icon">
          <i class="fas fa-trophy"></i>
        </div>
        <div class="card-content">
          <div class="metric-value" id="meilleurMois">-</div>
          <div class="metric-label">Meilleur Mois</div>
          <div class="metric-change" id="meilleurMontant">0 Ar</div>
        </div>
      </div>
    </div>
  </div>

  <!-- Charts Section -->
  <div class="charts-section" id="chartContainer" style="display: none;">
    <div class="charts-grid">
      <!-- Main Chart -->
      <div class="main-chart-card">
        <div class="chart-header">
          <div class="chart-title">
            <i class="fas fa-chart-area"></i>
            <span>Évolution des Intérêts</span>
          </div>
          <div class="chart-controls">
            <button type="button" class="chart-btn active" onclick="switchChart('line')">
              <i class="fas fa-chart-line"></i>
            </button>
            <button type="button" class="chart-btn" onclick="switchChart('bar')">
              <i class="fas fa-chart-bar"></i>
            </button>
            <button type="button" class="chart-btn" onclick="switchChart('area')">
              <i class="fas fa-chart-area"></i>
            </button>
          </div>
        </div>
        <div class="chart-container">
          <canvas id="interetsChart"></canvas>
        </div>
      </div>
      
      <!-- Pie Chart -->
      <div class="pie-chart-card">
        <div class="chart-header">
          <div class="chart-title">
            <i class="fas fa-chart-pie"></i>
            <span>Répartition</span>
          </div>
        </div>
        <div class="chart-container">
          <canvas id="pieChart"></canvas>
        </div>
      </div>
    </div>
  </div>

  <!-- Details Section -->
  <div class="details-section" id="detailsContainer" style="display: none;">
    <div class="details-card">
      <div class="details-header">
        <div class="details-title">
          <i class="fas fa-table"></i>
          <span>Détails par Mois</span>
        </div>
        <div class="details-actions">
          <button class="action-btn">
            <i class="fas fa-download"></i>
            <span>Exporter</span>
          </button>
        </div>
      </div>
      <div class="details-content">
        <div class="table-container">
          <table class="details-table" id="detailsTable">
            <thead>
              <tr>
                <th>Mois</th>
                <th>Intérêts (Ar)</th>
                <th>Évolution</th>
                <th>% du Total</th>
              </tr>
            </thead>
            <tbody>
              <!-- Populated by JavaScript -->
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>

<style>
.interests-dashboard {
  display: flex;
  flex-direction: column;
  gap: 2rem;
  padding: 0;
}

/* Period Selection Section */
.period-section {
  width: 100%;
}

.period-card {
  background: white;
  border-radius: 16px;
  box-shadow: var(--card-shadow);
  overflow: hidden;
}

.card-header-custom {
  display: flex;
  align-items: center;
  gap: 1rem;
  padding: 1.5rem;
  background: linear-gradient(135deg, #F8FAFC 0%, #E2E8F0 100%);
  border-bottom: 1px solid var(--border-color);
}

.header-icon {
  width: 48px;
  height: 48px;
  background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
  border-radius: 12px;
  display: flex;
  align-items: center;
  justify-content: center;
  color: white;
  font-size: 1.2rem;
}

.header-content {
  flex: 1;
}

.header-title {
  font-size: 1.5rem;
  font-weight: 700;
  color: var(--text-primary);
  margin: 0 0 0.25rem 0;
}

.header-subtitle {
  font-size: 0.875rem;
  color: var(--text-secondary);
  margin: 0;
}

.period-form {
  padding: 1.5rem;
}

.form-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
  gap: 1.5rem;
  margin-bottom: 2rem;
}

.form-group {
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
}

.form-label {
  font-size: 0.875rem;
  font-weight: 600;
  color: var(--text-primary);
  margin: 0;
}

.form-actions {
  display: flex;
  justify-content: center;
}

.btn-analyze {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  padding: 1rem 2rem;
  background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark) 100%);
  color: white;
  border: none;
  border-radius: 12px;
  font-size: 1rem;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s ease;
}

.btn-analyze:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 15px rgba(59, 130, 246, 0.3);
}

/* Summary Section */
.summary-section {
  width: 100%;
}

.summary-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
  gap: 1.5rem;
}

.summary-card {
  background: white;
  border-radius: 16px;
  padding: 1.5rem;
  box-shadow: var(--card-shadow);
  display: flex;
  align-items: center;
  gap: 1rem;
  position: relative;
  overflow: hidden;
  transition: all 0.3s ease;
}

.summary-card:hover {
  transform: translateY(-4px);
  box-shadow: var(--card-shadow-hover);
}

.summary-card::before {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  height: 4px;
  background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
}

.summary-card.warning::before {
  background: linear-gradient(135deg, #F59E0B, #D97706);
}

.summary-card.success::before {
  background: linear-gradient(135deg, var(--accent-color), #059669);
}

.card-icon {
  width: 60px;
  height: 60px;
  border-radius: 12px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 1.5rem;
  background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
  color: white;
}

.summary-card.warning .card-icon {
  background: linear-gradient(135deg, #F59E0B, #D97706);
}

.summary-card.success .card-icon {
  background: linear-gradient(135deg, var(--accent-color), #059669);
}

.card-content {
  flex: 1;
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
  margin-bottom: 0.5rem;
}

.metric-change {
  font-size: 0.75rem;
  padding: 0.25rem 0.5rem;
  border-radius: 20px;
  background: rgba(59, 130, 246, 0.1);
  color: var(--primary-color);
  display: inline-flex;
  align-items: center;
  gap: 0.25rem;
  font-weight: 600;
}

/* Charts Section */
.charts-section {
  width: 100%;
}

.charts-grid {
  display: grid;
  grid-template-columns: 2fr 1fr;
  gap: 1.5rem;
}

.main-chart-card,
.pie-chart-card {
  background: white;
  border-radius: 16px;
  box-shadow: var(--card-shadow);
  overflow: hidden;
}

.chart-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 1.5rem;
  border-bottom: 1px solid var(--border-color);
}

.chart-title {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  font-size: 1.125rem;
  font-weight: 600;
  color: var(--text-primary);
}

.chart-controls {
  display: flex;
  gap: 0.5rem;
}

.chart-btn {
  width: 36px;
  height: 36px;
  border: 2px solid var(--border-color);
  background: white;
  border-radius: 8px;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  transition: all 0.2s ease;
  color: var(--text-secondary);
}

.chart-btn:hover,
.chart-btn.active {
  border-color: var(--primary-color);
  background: var(--primary-color);
  color: white;
}

.chart-container {
  padding: 1.5rem;
  position: relative;
  height: 400px;
}

.pie-chart-card .chart-container {
  height: 350px;
}

/* Details Section */
.details-section {
  width: 100%;
}

.details-card {
  background: white;
  border-radius: 16px;
  box-shadow: var(--card-shadow);
  overflow: hidden;
}

.details-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 1.5rem;
  border-bottom: 1px solid var(--border-color);
}

.details-title {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  font-size: 1.125rem;
  font-weight: 600;
  color: var(--text-primary);
}

.details-actions {
  display: flex;
  gap: 0.5rem;
}

.action-btn {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  padding: 0.5rem 1rem;
  border: 2px solid var(--border-color);
  background: white;
  border-radius: 8px;
  font-size: 0.875rem;
  color: var(--text-secondary);
  cursor: pointer;
  transition: all 0.2s ease;
}

.action-btn:hover {
  border-color: var(--primary-color);
  color: var(--primary-color);
}

.details-content {
  padding: 1.5rem;
}

.table-container {
  overflow-x: auto;
}

.details-table {
  width: 100%;
  border-collapse: collapse;
  font-size: 0.875rem;
}

.details-table th {
  padding: 1rem;
  text-align: left;
  font-weight: 600;
  color: var(--text-primary);
  border-bottom: 2px solid var(--border-color);
  background: #F8FAFC;
}

.details-table td {
  padding: 1rem;
  border-bottom: 1px solid var(--border-color);
  color: var(--text-secondary);
}

.details-table tr:hover {
  background: #F8FAFC;
}

/* Progress bars in table */
.progress {
  height: 20px;
  background: #E5E7EB;
  border-radius: 10px;
  overflow: hidden;
}

.progress-bar {
  height: 100%;
  background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
  display: flex;
  align-items: center;
  justify-content: center;
  color: white;
  font-size: 0.75rem;
  font-weight: 600;
  transition: width 0.3s ease;
}

/* Responsive Design */
@media (max-width: 1200px) {
  .charts-grid {
    grid-template-columns: 1fr;
  }
  
  .pie-chart-card {
    order: -1;
  }
}

@media (max-width: 768px) {
  .interests-dashboard {
    gap: 1.5rem;
  }
  
  .form-grid {
    grid-template-columns: 1fr;
    gap: 1rem;
  }
  
  .summary-grid {
    grid-template-columns: 1fr;
  }
  
  .card-header-custom {
    flex-direction: column;
    text-align: center;
    gap: 1rem;
  }
  
  .chart-header {
    flex-direction: column;
    gap: 1rem;
    align-items: flex-start;
  }
  
  .details-header {
    flex-direction: column;
    gap: 1rem;
    align-items: flex-start;
  }
  
  .metric-value {
    font-size: 1.75rem;
  }
  
  .chart-container {
    height: 300px;
  }
}

@media (max-width: 480px) {
  .interests-dashboard {
    gap: 1rem;
  }
  
  .period-form,
  .details-content {
    padding: 1rem;
  }
  
  .summary-card {
    flex-direction: column;
    text-align: center;
    padding: 1rem;
  }
  
  .metric-value {
    font-size: 1.5rem;
  }
  
  .btn-analyze {
    padding: 0.75rem 1.5rem;
    font-size: 0.875rem;
  }
}
</style>

<script>
let interetsChart = null;
let pieChart = null;
let currentChartType = 'line';
let currentData = null;
let isLoading = false;

function submitForm() {
  if (isLoading) return;
  
  isLoading = true;
  
  const formData = new FormData(document.getElementById('interetsForm'));
  const params = new URLSearchParams();

  params.append('mois1', formData.get('mois1'));
  params.append('annee1', formData.get('annee1'));

  if (formData.get('mois2')) {
    params.append('mois2', formData.get('mois2'));
  }
  if (formData.get('annee2')) {
    params.append('annee2', formData.get('annee2'));
  }

  fetch('<?= $config['api']['base_url'] ?>/pret-retour-historiques/interets?' + params.toString())
    .then(response => response.json())
    .then(data => {
      if (data.error) {
        console.error('API Error:', data.error);
        return;
      }

      currentData = data;
      displayChart(data);
      displaySummary(data);
      displayDetails(data);
      displayPieChart(data);
    })
    .catch(error => {
      console.error('Fetch Error:', error);
    })
    .finally(() => {
      isLoading = false;
    });
}

document.getElementById('interetsForm').addEventListener('submit', function(e) {
  e.preventDefault();
  submitForm();
});

function displayChart(data) {
  const ctx = document.getElementById('interetsChart').getContext('2d');

  if (interetsChart) {
    interetsChart.destroy();
  }

  const labels = Object.keys(data);
  const values = Object.values(data);

  const chartConfig = {
    type: currentChartType,
    data: {
      labels: labels.map(label => {
        const [year, month] = label.split('-');
        const monthNames = ['Jan', 'Fév', 'Mar', 'Avr', 'Mai', 'Jun',
          'Jul', 'Aoû', 'Sep', 'Oct', 'Nov', 'Déc'];
        return monthNames[parseInt(month) - 1] + ' ' + year;
      }),
      datasets: [{
        label: 'Intérêts (Ar)',
        data: values,
        borderColor: 'rgba(59, 130, 246, 1)',
        backgroundColor: currentChartType === 'line' ? 'rgba(59, 130, 246, 0.1)' : 'rgba(59, 130, 246, 0.8)',
        tension: 0.4,
        fill: currentChartType !== 'bar',
        borderWidth: 3
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
          backgroundColor: 'rgba(0, 0, 0, 0.8)',
          titleColor: 'white',
          bodyColor: 'white',
          borderColor: 'rgba(59, 130, 246, 1)',
          borderWidth: 1,
          callbacks: {
            label: function(context) {
              return 'Intérêts: ' + new Intl.NumberFormat('fr-FR').format(context.parsed.y) + ' Ar';
            }
          }
        }
      },
      scales: {
        y: {
          beginAtZero: true,
          grid: {
            color: 'rgba(0, 0, 0, 0.05)'
          },
          ticks: {
            callback: function(value) {
              return new Intl.NumberFormat('fr-FR').format(value) + ' Ar';
            }
          }
        },
        x: {
          grid: {
            display: false
          }
        }
      }
    }
  };

  interetsChart = new Chart(ctx, chartConfig);
  document.getElementById('chartContainer').style.display = 'block';
}

function displaySummary(data) {
  const values = Object.values(data);
  const total = values.reduce((sum, val) => sum + val, 0);
  const moyenne = values.length > 0 ? total / values.length : 0;

  let meilleurMois = '-';
  let maxValue = 0;

  for (const [month, value] of Object.entries(data)) {
    if (value > maxValue) {
      maxValue = value;
      const [year, monthNum] = month.split('-');
      const monthNames = ['Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin',
        'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre'];
      meilleurMois = monthNames[parseInt(monthNum) - 1] + ' ' + year;
    }
  }

  // Animate numbers
  animateValue('totalInterets', 0, total, 1000, ' Ar');
  animateValue('moyenneInterets', 0, moyenne, 1000, ' Ar');
  
  document.getElementById('meilleurMois').textContent = meilleurMois;
  document.getElementById('meilleurMontant').textContent = new Intl.NumberFormat('fr-FR').format(maxValue) + ' Ar';

  document.getElementById('summaryContainer').style.display = 'block';
}

function displayDetails(data) {
  const tbody = document.querySelector('#detailsTable tbody');
  tbody.innerHTML = '';
  
  const values = Object.values(data);
  const total = values.reduce((sum, val) => sum + val, 0);
  
  let previousValue = null;
  
  Object.entries(data).forEach(([month, value]) => {
    const [year, monthNum] = month.split('-');
    const monthNames = ['Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin',
      'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre'];
    const monthName = monthNames[parseInt(monthNum) - 1] + ' ' + year;
    
    const percentage = total > 0 ? (value / total * 100).toFixed(1) : 0;
    
    let evolution = '';
    if (previousValue !== null && previousValue !== 0) {
      const change = ((value - previousValue) / previousValue * 100);
      const arrow = change >= 0 ? '↗️' : '↘️';
      const color = change >= 0 ? 'text-success' : 'text-danger';
      evolution = `<span class="${color}">${arrow} ${Math.abs(change).toFixed(1)}%</span>`;
    } else {
      evolution = '<span class="text-muted">-</span>';
    }
    
    const row = `
      <tr>
        <td>${monthName}</td>
        <td>${new Intl.NumberFormat('fr-FR').format(value)} Ar</td>
        <td>${evolution}</td>
        <td><div class="progress">
          <div class="progress-bar" style="width: ${percentage}%">${percentage}%</div>
        </div></td>
      </tr>
    `;
    
    tbody.innerHTML += row;
    previousValue = value;
  });
  
  document.getElementById('detailsContainer').style.display = 'block';
}

function displayPieChart(data) {
  const ctx = document.getElementById('pieChart').getContext('2d');

  if (pieChart) {
    pieChart.destroy();
  }

  const labels = Object.keys(data).map(label => {
    const [year, month] = label.split('-');
    const monthNames = ['Jan', 'Fév', 'Mar', 'Avr', 'Mai', 'Jun',
      'Jul', 'Aoû', 'Sep', 'Oct', 'Nov', 'Déc'];
    return monthNames[parseInt(month) - 1] + ' ' + year;
  });
  
  const values = Object.values(data);
  const colors = [
    '#3B82F6', '#6366F1', '#10B981', '#F59E0B', 
    '#EF4444', '#8B5CF6', '#06B6D4', '#84CC16'
  ];

  pieChart = new Chart(ctx, {
    type: 'doughnut',
    data: {
      labels: labels,
      datasets: [{
        data: values,
        backgroundColor: colors.slice(0, values.length),
        borderWidth: 3,
        borderColor: '#fff'
      }]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      plugins: {
        legend: {
          position: 'bottom',
          labels: {
            padding: 20,
            usePointStyle: true
          }
        },
        tooltip: {
          callbacks: {
            label: function(context) {
              const total = values.reduce((a, b) => a + b, 0);
              const percentage = total > 0 ? ((context.parsed / total) * 100).toFixed(1) : 0;
              return context.label + ': ' + new Intl.NumberFormat('fr-FR').format(context.parsed) + ' Ar (' + percentage + '%)';
            }
          }
        }
      }
    }});
}

function switchChart(type) {
  currentChartType = type;
  
  // Update button states
  document.querySelectorAll('.chart-btn').forEach(btn => btn.classList.remove('active'));
  event.target.closest('.chart-btn').classList.add('active');
  
  if (currentData) {
    displayChart(currentData);
  }
}

function animateValue(elementId, start, end, duration, suffix = '') {
  const element = document.getElementById(elementId);
  if (!element) return;
  
  const range = end - start;
  const increment = range / (duration / 16);
  let current = start;
  
  const timer = setInterval(() => {
    current += increment;
    if ((increment > 0 && current >= end) || (increment < 0 && current <= end)) {
      current = end;
      clearInterval(timer);
    }
    element.textContent = new Intl.NumberFormat('fr-FR').format(Math.floor(current)) + suffix;
  }, 16);
}

// Initialize the page
document.addEventListener('DOMContentLoaded', function() {
  // Set current month and year as default values but don't auto-submit
  const currentMonth = new Date().getMonth() + 1;
  const currentYear = new Date().getFullYear();
  
  document.getElementById('mois1').value = currentMonth;
  document.getElementById('annee1').value = currentYear;
  
  // Removed auto-loading - wait for user input
});
</script>
