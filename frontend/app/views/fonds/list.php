<div class="d-flex justify-content-between align-items-center mb-3">
  <h3>Liste des Fonds</h3>
  <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createModal">
    <i class="fas fa-plus"></i> Ajouter un Fond
  </button>
</div>

<div class="table-responsive">
  <table class="table table-striped">
    <thead>
      <tr>
        <th>ID</th>
        <th>Montant Initial</th>
        <th>Description</th>
        <th>Source</th>
        <th>Date Ajout</th>
        <th>Actions</th>
      </tr>
    </thead>
    <tbody>
      <?php if (empty($fonds)): ?>
        <tr>
          <td colspan="6" class="text-center">Aucun fond trouvé</td>
        </tr>
      <?php else: ?>
        <?php foreach ($fonds as $fond): ?>
          <tr>
            <td><?= $fond['id'] ?></td>
            <td><?= number_format($fond['montant_initial'], 2) ?> Ar</td>
            <td><?= htmlspecialchars($fond['description']) ?></td>
            <td>
              <?php
              $sourceName = 'N/A';
              if ($sourceFonds) {
                foreach ($sourceFonds as $source) {
                  if ($source['id'] == $fond['source_fond_id']) {
                    $sourceName = $source['nom'];
                    break;
                  }
                }
              }
              echo htmlspecialchars($sourceName);
              ?>
            </td>
            <td><?= date('d/m/Y H:i', strtotime($fond['date_ajout'])) ?></td>
            <td>
              <button class="btn btn-sm btn-outline-primary" onclick="editFond(<?= htmlspecialchars(json_encode($fond)) ?>)">
                <i class="fas fa-edit"></i>
              </button>
              <button class="btn btn-sm btn-outline-danger" onclick="deleteFond(<?= $fond['id'] ?>)">
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
        <h5 class="modal-title">Créer un Fond</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <form id="createForm">
        <div class="modal-body">
          <div class="mb-3">
            <label for="create_montant_initial" class="form-label">Montant Initial</label>
            <input type="number" step="0.01" class="form-control" id="create_montant_initial" name="montant_initial" required>
          </div>
          <div class="mb-3">
            <label for="create_description" class="form-label">Description</label>
            <textarea class="form-control" id="create_description" name="description" rows="3" required></textarea>
          </div>
          <div class="mb-3">
            <label for="create_source_fond_id" class="form-label">Source de Fond</label>
            <select class="form-select" id="create_source_fond_id" name="source_fond_id" required>
              <option value="">Sélectionner une source</option>
              <?php if ($sourceFonds): ?>
                <?php foreach ($sourceFonds as $source): ?>
                  <option value="<?= $source['id'] ?>"><?= htmlspecialchars($source['nom']) ?></option>
                <?php endforeach; ?>
              <?php endif; ?>
            </select>
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
        <h5 class="modal-title">Modifier le Fond</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <form id="editForm">
        <input type="hidden" id="edit_id" name="id">
        <div class="modal-body">
          <div class="mb-3">
            <label for="edit_montant_initial" class="form-label">Montant Initial</label>
            <input type="number" step="0.01" class="form-control" id="edit_montant_initial" name="montant_initial" required>
          </div>
          <div class="mb-3">
            <label for="edit_description" class="form-label">Description</label>
            <textarea class="form-control" id="edit_description" name="description" rows="3" required></textarea>
          </div>
          <div class="mb-3">
            <label for="edit_source_fond_id" class="form-label">Source de Fond</label>
            <select class="form-select" id="edit_source_fond_id" name="source_fond_id" required>
              <option value="">Sélectionner une source</option>
              <?php if ($sourceFonds): ?>
                <?php foreach ($sourceFonds as $source): ?>
                  <option value="<?= $source['id'] ?>"><?= htmlspecialchars($source['nom']) ?></option>
                <?php endforeach; ?>
              <?php endif; ?>
            </select>
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
  // Create form submission
  document.getElementById('createForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const formData = new FormData(this);
    const data = Object.fromEntries(formData);

    fetch('<?= route('/fonds') ?>', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json'
        },
        body: JSON.stringify(data)
      })
      .then(response => response.json())
      .then(data => {
        alert(data.message || 'Fond créé');
        location.reload();
      })
      .catch(error => {
        console.error('Error:', error);
        alert('Erreur lors de la création');
      });
  });

  // Edit form submission
  document.getElementById('editForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const formData = new FormData(this);
    const data = Object.fromEntries(formData);
    const id = data.id;
    delete data.id;

    fetch('<?= route('/fonds') ?>/' + id, {
        method: 'PUT',
        headers: {
          'Content-Type': 'application/json'
        },
        body: JSON.stringify(data)
      })
      .then(response => response.json())
      .then(data => {
        alert(data.message || 'Fond modifié');
        location.reload();
      })
      .catch(error => {
        console.error('Error:', error);
        alert('Erreur lors de la modification');
      });
  });

  // Edit function
  function editFond(fond) {
    document.getElementById('edit_id').value = fond.id;
    document.getElementById('edit_montant_initial').value = fond.montant_initial;
    document.getElementById('edit_description').value = fond.description;
    document.getElementById('edit_source_fond_id').value = fond.source_fond_id;

    new bootstrap.Modal(document.getElementById('editModal')).show();
  }

  // Delete function
  function deleteFond(id) {
    if (confirm('Êtes-vous sûr de vouloir supprimer cet élément ?')) {
      fetch('<?= route('/fonds') ?>/' + id, {
          method: 'DELETE'
        })
        .then(response => response.json())
        .then(data => {
          alert(data.message || 'Élément supprimé');
          location.reload();
        })
        .catch(error => {
          console.error('Error:', error);
          alert('Erreur lors de la suppression');
        });
    }
  }
</script>