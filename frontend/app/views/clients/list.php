<div class="d-flex justify-content-between align-items-center mb-3">
  <h3>Liste des Clients</h3>
  <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createModal">
    <i class="fas fa-plus"></i> Ajouter un Client
  </button>
</div>

<div class="table-responsive">
  <table class="table table-striped">
    <thead>
      <tr>
        <th>ID</th>
        <th>Email</th>
        <th>Nom</th>
        <th>Prénom</th>
        <th>Utilisateur</th>
        <th>Actions</th>
      </tr>
    </thead>
    <tbody>
      <?php if (empty($clients)): ?>
        <tr>
          <td colspan="6" class="text-center">Aucun client trouvé</td>
        </tr>
      <?php else: ?>
        <?php foreach ($clients as $client): ?>
          <tr>
            <td><?= $client['id'] ?></td>
            <td><?= htmlspecialchars($client['email']) ?></td>
            <td><?= htmlspecialchars($client['nom']) ?></td>
            <td><?= htmlspecialchars($client['prenom']) ?></td>
            <td>
              <?php
              $userName = 'N/A';
              if ($users) {
                foreach ($users as $user) {
                  if ($user['id'] == $client['user_id']) {
                    $userName = $user['nom'];
                    break;
                  }
                }
              }
              echo htmlspecialchars($userName);
              ?>
            </td>
            <td>
              <button class="btn btn-sm btn-outline-primary" onclick="editClient(<?= htmlspecialchars(json_encode($client)) ?>)">
                <i class="fas fa-edit"></i>
              </button>
              <button class="btn btn-sm btn-outline-danger" onclick="deleteClient(<?= $client['id'] ?>)">
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
        <h5 class="modal-title">Créer un Client</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <form id="createForm">
        <div class="modal-body">
          <div class="mb-3">
            <label for="create_email" class="form-label">Email</label>
            <input type="email" class="form-control" id="create_email" name="email" required>
          </div>
          <div class="mb-3">
            <label for="create_password" class="form-label">Mot de passe</label>
            <input type="password" class="form-control" id="create_password" name="password" required>
          </div>
          <div class="mb-3">
            <label for="create_nom" class="form-label">Nom</label>
            <input type="text" class="form-control" id="create_nom" name="nom" required>
          </div>
          <div class="mb-3">
            <label for="create_prenom" class="form-label">Prénom</label>
            <input type="text" class="form-control" id="create_prenom" name="prenom" required>
          </div>
          <div class="mb-3">
            <label for="create_user_id" class="form-label">Utilisateur</label>
            <select class="form-select" id="create_user_id" name="user_id" required>
              <option value="">Sélectionner un utilisateur</option>
              <?php if ($users): ?>
                <?php foreach ($users as $user): ?>
                  <option value="<?= $user['id'] ?>"><?= htmlspecialchars($user['nom']) ?></option>
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
        <h5 class="modal-title">Modifier le Client</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <form id="editForm">
        <input type="hidden" id="edit_id" name="id">
        <div class="modal-body">
          <div class="mb-3">
            <label for="edit_email" class="form-label">Email</label>
            <input type="email" class="form-control" id="edit_email" name="email" required>
          </div>
          <div class="mb-3">
            <label for="edit_password" class="form-label">Mot de passe</label>
            <input type="password" class="form-control" id="edit_password" name="password" placeholder="Laissez vide pour ne pas changer">
          </div>
          <div class="mb-3">
            <label for="edit_nom" class="form-label">Nom</label>
            <input type="text" class="form-control" id="edit_nom" name="nom" required>
          </div>
          <div class="mb-3">
            <label for="edit_prenom" class="form-label">Prénom</label>
            <input type="text" class="form-control" id="edit_prenom" name="prenom" required>
          </div>
          <div class="mb-3">
            <label for="edit_user_id" class="form-label">Utilisateur</label>
            <select class="form-select" id="edit_user_id" name="user_id" required>
              <option value="">Sélectionner un utilisateur</option>
              <?php if ($users): ?>
                <?php foreach ($users as $user): ?>
                  <option value="<?= $user['id'] ?>"><?= htmlspecialchars($user['nom']) ?></option>
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
  document.getElementById('createForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const formData = new FormData(this);
    const data = Object.fromEntries(formData);

    fetch('<?= route('/clients') ?>', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json'
        },
        body: JSON.stringify(data)
      })
      .then(response => response.json())
      .then(data => {
        alert(data.message || 'Client créé');
        location.reload();
      });
  });

  document.getElementById('editForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const formData = new FormData(this);
    const data = Object.fromEntries(formData);
    const id = data.id;
    delete data.id;

    // Remove password if empty
    if (!data.password) {
      delete data.password;
    }

    fetch('<?= route('/clients') ?>/' + id, {
        method: 'PUT',
        headers: {
          'Content-Type': 'application/json'
        },
        body: JSON.stringify(data)
      })
      .then(response => response.json())
      .then(data => {
        alert(data.message || 'Client modifié');
        location.reload();
      });
  });

  function editClient(client) {
    document.getElementById('edit_id').value = client.id;
    document.getElementById('edit_email').value = client.email;
    document.getElementById('edit_nom').value = client.nom;
    document.getElementById('edit_prenom').value = client.prenom;
    document.getElementById('edit_user_id').value = client.user_id;
    document.getElementById('edit_password').value = '';
    new bootstrap.Modal(document.getElementById('editModal')).show();
  }

  function deleteClient(id) {
    if (confirm('Êtes-vous sûr de vouloir supprimer cet élément ?')) {
      fetch('<?= route('/clients') ?>/' + id, {
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