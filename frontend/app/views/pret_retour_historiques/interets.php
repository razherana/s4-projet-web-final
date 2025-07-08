<div class="row">
  <div class="col-12">
    <div class="card mb-4">
      <div class="card-header">
        <h5 class="card-title">Calcul des Intérêts par Période</h5>
      </div>
      <div class="card-body">
        <form id="interetsForm" class="row g-3">
          <div class="col-md-3">
            <label for="mois1" class="form-label">Mois de début</label>
            <select class="form-select" id="mois1" name="mois1" required>
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
          <div class="col-md-3">
            <label for="annee1" class="form-label">Mois de début</label>
            <input type="number" class="form-control" id="annee1" name="annee1" min="2020" max="2030" value="<?= date('Y') ?>" required>
          </div>
          <div class="col-md-3">
            <label for="mois2" class="form-label">Mois de fin (optionnel)</label>
            <select class="form-select" id="mois2" name="mois2">
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
          <div class="col-md-3">
            <label for="annee2" class="form-label">Mois de fin (optionnel)</label>
            <input type="number" class="form-control" id="annee2" name="annee2" min="2020" max="2030" value="<?= date('Y') ?>">
          </div>
          <div class="col-12">
            <button type="submit" class="btn btn-primary">
              <i class="fas fa-chart-line"></i> Calculer les Intérêts
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<div class="row" id="chartContainer" style="display: none;">
  <div class="col-12">
    <div class="card">
      <div class="card-header">
        <h5 class="card-title">Évolution des Intérêts par Mois</h5>
      </div>
      <div class="card-body">
        <canvas id="interetsChart" width="400" height="200"></canvas>
      </div>
    </div>
  </div>
</div>

<div class="row mt-4" id="summaryContainer" style="display: none;">
  <div class="col-md-4">
    <div class="card text-white bg-primary">
      <div class="card-body">
        <h5 class="card-title">Total des Intérêts</h5>
        <h3 id="totalInterets">0 Ar</h3>
      </div>
    </div>
  </div>
  <div class="col-md-4">
    <div class="card text-white bg-success">
      <div class="card-body">
        <h5 class="card-title">Moyenne Mensuelle</h5>
        <h3 id="moyenneInterets">0 Ar</h3>
      </div>
    </div>
  </div>
  <div class="col-md-4">
    <div class="card text-white bg-info">
      <div class="card-body">
        <h5 class="card-title">Meilleur Mois</h5>
        <h3 id="meilleurMois">-</h3>
      </div>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
  let interetsChart = null;

  document.getElementById('interetsForm').addEventListener('submit', function(e) {
    e.preventDefault();

    const formData = new FormData(this);
    const params = new URLSearchParams();

    // Add required parameters
    params.append('mois1', formData.get('mois1'));
    params.append('annee1', formData.get('annee1'));

    // Add optional parameters if provided
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
          alert('Erreur: ' + data.error);
          return;
        }

        displayChart(data);
        displaySummary(data);
      })
      .catch(error => {
        console.error('Error:', error);
        alert('Erreur lors du calcul des intérêts');
      });
  });

  function displayChart(data) {
    const ctx = document.getElementById('interetsChart').getContext('2d');

    // Destroy existing chart if it exists
    if (interetsChart) {
      interetsChart.destroy();
    }

    const labels = Object.keys(data);
    const values = Object.values(data);

    interetsChart = new Chart(ctx, {
      type: 'line',
      data: {
        labels: labels.map(label => {
          const [year, month] = label.split('-');
          const monthNames = ['Jan', 'Fév', 'Mar', 'Avr', 'Mai', 'Jun',
            'Jul', 'Aoû', 'Sep', 'Oct', 'Nov', 'Déc'
          ];
          return monthNames[parseInt(month) - 1] + ' ' + year;
        }),
        datasets: [{
          label: 'Intérêts (Ar)',
          data: values,
          borderColor: 'rgb(75, 192, 192)',
          backgroundColor: 'rgba(75, 192, 192, 0.2)',
          tension: 0.1,
          fill: true
        }]
      },
      options: {
        responsive: true,
        scales: {
          y: {
            beginAtZero: true,
            ticks: {
              callback: function(value) {
                return new Intl.NumberFormat('fr-FR').format(value) + ' Ar';
              }
            }
          }
        },
        plugins: {
          tooltip: {
            callbacks: {
              label: function(context) {
                return 'Intérêts: ' + new Intl.NumberFormat('fr-FR').format(context.parsed.y) + ' Ar';
              }
            }
          }
        }
      }
    });

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
          'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre'
        ];
        meilleurMois = monthNames[parseInt(monthNum) - 1] + ' ' + year;
      }
    }

    document.getElementById('totalInterets').textContent = new Intl.NumberFormat('fr-FR').format(total) + ' Ar';
    document.getElementById('moyenneInterets').textContent = new Intl.NumberFormat('fr-FR').format(moyenne) + ' Ar';
    document.getElementById('meilleurMois').textContent = meilleurMois;

    document.getElementById('summaryContainer').style.display = 'block';
  }
</script>