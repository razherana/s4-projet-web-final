<div class="d-flex justify-content-between align-items-center mb-3">
  <h3>Liste des Types de Prêts</h3>
  <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createModal">
    <i class="fas fa-plus"></i> Ajouter un Type de Prêt
  </button>
</div>

<div class="table-responsive">
  <table class="table table-striped">
    <thead>
      <tr>
        <th>ID</th>
        <th>Nom</th>
        <th>Taux d'Intérêt (%)</th>
        <th>Durée Min (mois)</th>
        <th>Durée Max (mois)</th>
        <th>Actions</th>
      </tr>
    </thead>
    <tbody>
      <?php if (empty($typePrets)): ?>
        <tr>
          <td colspan="6" class="text-center">Aucun type de prêt trouvé</td>
        </tr>
      <?php else: ?>
        <?php foreach ($typePrets as $typePret): ?>
          <tr>
            <td><?= $typePret['id'] ?></td>
            <td><?= htmlspecialchars($typePret['nom']) ?></td>
            <td><?= number_format($typePret['taux_interet'], 2) ?>%</td>
            <td><?= $typePret['duree_min'] ?></td>
            <td><?= $typePret['duree_max'] ?></td>
            <td>
              <button class="btn btn-sm btn-outline-primary" onclick="editTypePret(<?= htmlspecialchars(json_encode($typePret)) ?>)">
                <i class="fas fa-edit"></i>
              </button>
              <button class="btn btn-sm btn-outline-danger" onclick="deleteTypePret(<?= $typePret['id'] ?>)">
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
        <h5 class="modal-title">Créer un Type de Prêt</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <form id="createForm">
        <div class="modal-body">
          <div class="mb-3">
            <label for="create_nom" class="form-label">Nom</label>
            <input type="text" class="form-control" id="create_nom" name="nom" required>
          </div>
          <div class="mb-3">
            <label for="create_taux_interet" class="form-label">Taux d'Intérêt (%)</label>
            <input type="number" step="0.01" class="form-control" id="create_taux_interet" name="taux_interet" required>
          </div>
          <div class="mb-3">
            <label for="create_duree_min" class="form-label">Durée Minimale (mois)</label>
            <input type="number" class="form-control" id="create_duree_min" name="duree_min" required>
          </div>
          <div class="mb-3">
            <label for="create_duree_max" class="form-label">Durée Maximale (mois)</label>
            <input type="number" class="form-control" id="create_duree_max" name="duree_max" required>
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
        <h5 class="modal-title">Modifier le Type de Prêt</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <form id="editForm">
        <input type="hidden" id="edit_id" name="id">
        <div class="modal-body">
          <div class="mb-3">
            <label for="edit_nom" class="form-label">Nom</label>
            <input type="text" class="form-control" id="edit_nom" name="nom" required>
          </div>
          <div class="mb-3">
            <label for="edit_taux_interet" class="form-label">Taux d'Intérêt (%)</label>
            <input type="number" step="0.01" class="form-control" id="edit_taux_interet" name="taux_interet" required>
          </div>
          <div class="mb-3">
            <label for="edit_duree_min" class="form-label">Durée Minimale (mois)</label>
            <input type="number" class="form-control" id="edit_duree_min" name="duree_min" required>
          </div>
          <div class="mb-3">
            <label for="edit_duree_max" class="form-label">Durée Maximale (mois)</label>
            <input type="number" class="form-control" id="edit_duree_max" name="duree_max" required>
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

    fetch('<?= route('/type-prets') ?>', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json'
        },
        body: JSON.stringify(data)
      })
      .then(response => response.json())
      .then(data => {
        alert(data.message || 'Type de prêt créé');
        location.reload();
      });
  });

  document.getElementById('editForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const formData = new FormData(this);
    const data = Object.fromEntries(formData);
    const id = data.id;
    delete data.id;

    fetch('<?= route('/type-prets') ?>/' + id, {
        method: 'PUT',
        headers: {
          'Content-Type': 'application/json'
        },
        body: JSON.stringify(data)
      })
      .then(response => response.json())
      .then(data => {
        alert(data.message || 'Type de prêt modifié');
        location.reload();
      });
  });

  function editTypePret(typePret) {
    document.getElementById('edit_id').value = typePret.id;
    document.getElementById('edit_nom').value = typePret.nom;
    document.getElementById('edit_taux_interet').value = typePret.taux_interet;
    document.getElementById('edit_duree_min').value = typePret.duree_min;
    document.getElementById('edit_duree_max').value = typePret.duree_max;
    new bootstrap.Modal(document.getElementById('editModal')).show();
  }

  function deleteTypePret(id) {
    if (confirm('Êtes-vous sûr de vouloir supprimer cet élément ?')) {
      fetch('<?= route('/type-prets') ?>/' + id, {
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