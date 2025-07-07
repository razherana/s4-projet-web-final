<div class="d-flex justify-content-between align-items-center mb-3">
  <h3>Liste des Utilisateurs</h3>
  <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createModal">
    <i class="fas fa-plus"></i> Ajouter un Utilisateur
  </button>
</div>

<div class="table-responsive">
  <table class="table table-striped">
    <thead>
      <tr>
        <th>ID</th>
        <th>Nom</th>
        <th>Actions</th>
      </tr>
    </thead>
    <tbody>
      <?php if (empty($users)): ?>
        <tr>
          <td colspan="3" class="text-center">Aucun utilisateur trouvé</td>
        </tr>
      <?php else: ?>
        <?php foreach ($users as $user): ?>
          <tr>
            <td><?= $user['id'] ?></td>
            <td><?= htmlspecialchars($user['nom']) ?></td>
            <td>
              <button class="btn btn-sm btn-outline-primary" onclick="editUser(<?= htmlspecialchars(json_encode($user)) ?>)">
                <i class="fas fa-edit"></i>
              </button>
              <button class="btn btn-sm btn-outline-danger" onclick="deleteUser(<?= $user['id'] ?>)">
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
        <h5 class="modal-title">Créer un Utilisateur</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <form id="createForm">
        <div class="modal-body">
          <div class="mb-3">
            <label for="create_nom" class="form-label">Nom</label>
            <input type="text" class="form-control" id="create_nom" name="nom" required>
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
        <h5 class="modal-title">Modifier l'Utilisateur</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <form id="editForm">
        <input type="hidden" id="edit_id" name="id">
        <div class="modal-body">
          <div class="mb-3">
            <label for="edit_nom" class="form-label">Nom</label>
            <input type="text" class="form-control" id="edit_nom" name="nom" required>
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

    fetch('<?= route('/users') ?>', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json'
        },
        body: JSON.stringify(data)
      })
      .then(response => response.json())
      .then(data => {
        alert(data.message || 'Utilisateur créé');
        location.reload();
      });
  });

  document.getElementById('editForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const formData = new FormData(this);
    const data = Object.fromEntries(formData);
    const id = data.id;
    delete data.id;

    fetch('<?= route('/users') ?>/' + id, {
        method: 'PUT',
        headers: {
          'Content-Type': 'application/json'
        },
        body: JSON.stringify(data)
      })
      .then(response => response.json())
      .then(data => {
        alert(data.message || 'Utilisateur modifié');
        location.reload();
      });
  });

  function editUser(user) {
    document.getElementById('edit_id').value = user.id;
    document.getElementById('edit_nom').value = user.nom;
    new bootstrap.Modal(document.getElementById('editModal')).show();
  }

  function deleteUser(id) {
    if (confirm('Êtes-vous sûr de vouloir supprimer cet élément ?')) {
      fetch('<?= route('/users') ?>/' + id, {
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