<div class="d-flex justify-content-between align-items-center mb-3">
  <h3>Liste des Historiques de Retour</h3>
  <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createModal">
    <i class="fas fa-plus"></i> Ajouter un Historique
  </button>
</div>

<div class="table-responsive">
  <table class="table table-striped">
    <thead>
      <tr>
        <th>ID</th>
        <th>Prêt</th>
        <th>Montant</th>
        <th>Date de Retour</th>
        <th>Actions</th>
      </tr>
    </thead>
    <tbody>
      <?php if (empty($pretRetourHistoriques)): ?>
        <tr>
          <td colspan="5" class="text-center">Aucun historique trouvé</td>
        </tr>
      <?php else: ?>
        <?php foreach ($pretRetourHistoriques as $historique): ?>
          <tr>
            <td><?= $historique['id'] ?></td>
            <td>
              <?php
              $pretInfo = 'N/A';
              if ($prets) {
                foreach ($prets as $pret) {
                  if ($pret['id'] == $historique['pret_id']) {
                    $pretInfo = 'Prêt #' . $pret['id'] . ' (' . number_format($pret['montant'], 0) . ' Ar)';
                    break;
                  }
                }
              }
              echo htmlspecialchars($pretInfo);
              ?>
            </td>
            <td><?= number_format($historique['montant'], 2) ?> Ar</td>
            <td><?= (new DateTime($historique['date_retour']))->format("d/m/Y") ?></td>
            <td>
              <button class="btn btn-sm btn-outline-primary" onclick="editHistorique(<?= htmlspecialchars(json_encode($historique)) ?>)">
                <i class="fas fa-edit"></i>
              </button>
              <button class="btn btn-sm btn-outline-danger" onclick="deleteHistorique(<?= $historique['id'] ?>)">
                <i class="fas fa-trash"></i>
              </button>
            </td>
          </tr>
        <?php endforeach; ?>
      <?php endif; ?>
    </tbody>
  </table>
</div>

<!-- Create Modal -->
<div class="modal fade" id="createModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Créer un Historique de Retour</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <form id="createForm">
        <div class="modal-body">
          <div class="mb-3">
            <label for="create_pret_id" class="form-label">Prêt</label>
            <select class="form-select" id="create_pret_id" name="pret_id" required>
              <option value="">Sélectionner un prêt</option>
              <?php if ($prets): ?>
                <?php foreach ($prets as $pret): ?>
                  <option value="<?= $pret['id'] ?>">Prêt #<?= $pret['id'] ?> - <?= number_format($pret['montant'], 0) ?> Ar</option>
                <?php endforeach; ?>
              <?php endif; ?>
            </select>
          </div>
          <div class="mb-3">
            <label for="create_montant" class="form-label">Montant</label>
            <input type="number" step="0.01" class="form-control" id="create_montant" name="montant" required>
          </div>
          <div class="mb-3">
            <label for="create_date_retour" class="form-label">Date de Retour</label>
            <input type="date" class="form-control" id="create_date_retour" name="date_retour" required>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
          <button type="submit" class="btn btn-primary">Créer</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Edit Modal -->
<div class="modal fade" id="editModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Modifier l'Historique de Retour</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <form id="editForm">
        <input type="hidden" id="edit_id" name="id">
        <div class="modal-body">
          <div class="mb-3">
            <label for="edit_pret_id" class="form-label">Prêt</label>
            <select class="form-select" id="edit_pret_id" name="pret_id" required>
              <option value="">Sélectionner un prêt</option>
              <?php if ($prets): ?>
                <?php foreach ($prets as $pret): ?>
                  <option value="<?= $pret['id'] ?>">Prêt #<?= $pret['id'] ?> - <?= number_format($pret['montant'], 0) ?> Ar</option>
                <?php endforeach; ?>
              <?php endif; ?>
            </select>
          </div>
          <div class="mb-3">
            <label for="edit_montant" class="form-label">Montant</label>
            <input type="number" step="0.01" class="form-control" id="edit_montant" name="montant" required>
          </div>
          <div class="mb-3">
            <label for="edit_date_retour" class="form-label">Date de Retour</label>
            <input type="date" class="form-control" id="edit_date_retour" name="date_retour" required>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
          <button type="submit" class="btn btn-primary">Modifier</button>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
  document.getElementById('createForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const formData = new FormData(this);
    const data = Object.fromEntries(formData);

    fetch('<?= route('/pret-retour-historiques') ?>', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json'
        },
        body: JSON.stringify(data)
      })
      .then(response => response.json())
      .then(data => {
        alert(data.message || 'Historique créé');
        location.reload();
      });
  });

  document.getElementById('editForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const formData = new FormData(this);
    const data = Object.fromEntries(formData);
    const id = data.id;
    delete data.id;

    fetch('<?= route('/pret-retour-historiques') ?>/' + id, {
        method: 'PUT',
        headers: {
          'Content-Type': 'application/json'
        },
        body: JSON.stringify(data)
      })
      .then(response => response.json())
      .then(data => {
        alert(data.message || 'Historique modifié');
        location.reload();
      });
  });

  function editHistorique(historique) {
    document.getElementById('edit_id').value = historique.id;
    document.getElementById('edit_pret_id').value = historique.pret_id;
    document.getElementById('edit_montant').value = historique.montant;
    document.getElementById('edit_date_retour').value = new Date(historique.date_retour * 1000).toISOString().split('T')[0];
    new bootstrap.Modal(document.getElementById('editModal')).show();
  }

  function deleteHistorique(id) {
    if (confirm('Êtes-vous sûr de vouloir supprimer cet élément ?')) {
      fetch('<?= route('/pret-retour-historiques') ?>/' + id, {
          method: 'DELETE'
        })
        .then(response => response.json())
        .then(data => {
          alert(data.message || 'Élément supprimé');
          location.reload();
        });
    }
  }
</script>