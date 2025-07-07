<div class="d-flex justify-content-between align-items-center mb-3">
  <h3>Liste des Prêts</h3>
  <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createModal">
    <i class="fas fa-plus"></i> Ajouter un Prêt
  </button>
</div>

<div class="table-responsive">
  <table class="table table-striped">
    <thead>
      <tr>
        <th>ID</th>
        <th>Client</th>
        <th>Type de Prêt</th>
        <th>Montant</th>
        <th>Durée (mois)</th>
        <th>Status</th>
        <th>Date Création</th>
        <th>Actions</th>
      </tr>
    </thead>
    <tbody>
      <?php if (empty($prets)): ?>
        <tr>
          <td colspan="8" class="text-center">Aucun prêt trouvé</td>
        </tr>
      <?php else: ?>
        <?php foreach ($prets as $pret): ?>
          <tr>
            <td><?= $pret['id'] ?></td>
            <td>
              <?php
              $clientName = 'N/A';
              if ($clients) {
                foreach ($clients as $client) {
                  if ($client['id'] == $pret['client_id']) {
                    $clientName = $client['nom'] . ' ' . $client['prenom'];
                    break;
                  }
                }
              }
              echo htmlspecialchars($clientName);
              ?>
            </td>
            <td>
              <?php
              $typeName = 'N/A';
              $typeDetails = '';
              if ($typePrets) {
                foreach ($typePrets as $type) {
                  if ($type['id'] == $pret['type_pret_id']) {
                    $typeName = $type['nom'];
                    $typeDetails = $type['taux_interet'] . '%';
                    if (isset($type['taux_assurance']) && $type['taux_assurance'] > 0) {
                      $typeDetails .= ' + ' . $type['taux_assurance'] . '% assurance';
                    }
                    break;
                  }
                }
              }
              echo htmlspecialchars($typeName) . '<br><small class="text-muted">' . $typeDetails . '</small>';
              ?>
            </td>
            <td><?= number_format($pret['montant'], 0) ?> Ar</td>
            <td><?= $pret['duree'] ?></td>
            <td>
              <?php
              if ($pret['date_acceptation']) {
                echo '<span class="badge bg-success">Accepté</span>';
              } elseif ($pret['date_refus']) {
                echo '<span class="badge bg-danger">Refusé</span>';
              } else {
                echo '<span class="badge bg-warning">En attente</span>';
              }
              ?>
            </td>
            <td><?= date('d/m/Y', strtotime($pret['date_creation'])) ?></td>
            <td>
              <button class="btn btn-sm btn-outline-primary" onclick="editPret(<?= htmlspecialchars(json_encode($pret)) ?>)">
                <i class="fas fa-edit"></i>
              </button>
              <button class="btn btn-sm btn-outline-danger" onclick="deletePret(<?= $pret['id'] ?>)">
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
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Créer un Prêt</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <form id="createForm">
        <div class="modal-body">
          <div class="mb-3">
            <label for="create_client_id" class="form-label">Client</label>
            <select class="form-select" id="create_client_id" name="client_id" required>
              <option value="">Sélectionner un client</option>
              <?php if ($clients): ?>
                <?php foreach ($clients as $client): ?>
                  <option value="<?= $client['id'] ?>"><?= htmlspecialchars($client['nom'] . ' ' . $client['prenom']) ?></option>
                <?php endforeach; ?>
              <?php endif; ?>
            </select>
          </div>
          <div class="mb-3">
            <label for="create_type_pret_id" class="form-label">Type de Prêt</label>
            <select class="form-select" id="create_type_pret_id" name="type_pret_id" required>
              <option value="">Sélectionner un type</option>
              <?php if ($typePrets): ?>
                <?php foreach ($typePrets as $type): ?>
                  <option value="<?= $type['id'] ?>">
                    <?= htmlspecialchars($type['nom']) ?> 
                    (<?= $type['taux_interet'] ?>%<?= isset($type['taux_assurance']) && $type['taux_assurance'] > 0 ? ' + ' . $type['taux_assurance'] . '% assurance' : '' ?>)
                  </option>
                <?php endforeach; ?>
              <?php endif; ?>
            </select>
          </div>
          <div class="mb-3">
            <label for="create_montant" class="form-label">Montant</label>
            <input type="number" class="form-control" id="create_montant" name="montant" required>
          </div>
          <div class="mb-3">
            <label for="create_duree" class="form-label">Durée (mois)</label>
            <input type="number" class="form-control" id="create_duree" name="duree" required>
          </div>
          <div class="mb-3">
            <label for="create_date_creation" class="form-label">Date de Création</label>
            <input type="datetime-local" class="form-control" id="create_date_creation" name="date_creation" required>
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
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Modifier le Prêt</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <form id="editForm">
        <input type="hidden" id="edit_id" name="id">
        <div class="modal-body">
          <div class="mb-3">
            <label for="edit_client_id" class="form-label">Client</label>
            <select class="form-select" id="edit_client_id" name="client_id" required>
              <option value="">Sélectionner un client</option>
              <?php if ($clients): ?>
                <?php foreach ($clients as $client): ?>
                  <option value="<?= $client['id'] ?>"><?= htmlspecialchars($client['nom'] . ' ' . $client['prenom']) ?></option>
                <?php endforeach; ?>
              <?php endif; ?>
            </select>
          </div>
          <div class="mb-3">
            <label for="edit_type_pret_id" class="form-label">Type de Prêt</label>
            <select class="form-select" id="edit_type_pret_id" name="type_pret_id" required>
              <option value="">Sélectionner un type</option>
              <?php if ($typePrets): ?>
                <?php foreach ($typePrets as $type): ?>
                  <option value="<?= $type['id'] ?>">
                    <?= htmlspecialchars($type['nom']) ?> 
                    (<?= $type['taux_interet'] ?>%<?= isset($type['taux_assurance']) && $type['taux_assurance'] > 0 ? ' + ' . $type['taux_assurance'] . '% assurance' : '' ?>)
                  </option>
                <?php endforeach; ?>
              <?php endif; ?>
            </select>
          </div>
          <div class="mb-3">
            <label for="edit_montant" class="form-label">Montant</label>
            <input type="number" class="form-control" id="edit_montant" name="montant" required>
          </div>
          <div class="mb-3">
            <label for="edit_duree" class="form-label">Durée (mois)</label>
            <input type="number" class="form-control" id="edit_duree" name="duree" required>
          </div>
          <div class="mb-3">
            <label for="edit_date_acceptation" class="form-label">Date d'Acceptation</label>
            <input type="datetime-local" class="form-control" id="edit_date_acceptation" name="date_acceptation">
          </div>
          <div class="mb-3">
            <label for="edit_date_refus" class="form-label">Date de Refus</label>
            <input type="datetime-local" class="form-control" id="edit_date_refus" name="date_refus">
          </div>
          <div class="mb-3">
            <label for="edit_date_creation" class="form-label">Date de Création</label>
            <input type="datetime-local" class="form-control" id="edit_date_creation" name="date_creation" required>
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
  
  fetch('<?= route('/prets') ?>', {
    method: 'POST',
    headers: {'Content-Type': 'application/json'},
    body: JSON.stringify(data)
  })
  .then(response => response.json())
  .then(data => {
    alert(data.message || 'Prêt créé');
    location.reload();
  });
});

document.getElementById('editForm').addEventListener('submit', function(e) {
  e.preventDefault();
  const formData = new FormData(this);
  const data = Object.fromEntries(formData);
  const id = data.id;
  delete data.id;
  
  // Remove empty date fields
  if (!data.date_acceptation) delete data.date_acceptation;
  if (!data.date_refus) delete data.date_refus;
  
  fetch('<?= route('/prets') ?>/' + id, {
    method: 'PUT',
    headers: {'Content-Type': 'application/json'},
    body: JSON.stringify(data)
  })
  .then(response => response.json())
  .then(data => {
    alert(data.message || 'Prêt modifié');
    location.reload();
  });
});

function editPret(pret) {
  document.getElementById('edit_id').value = pret.id;
  document.getElementById('edit_client_id').value = pret.client_id;
  document.getElementById('edit_type_pret_id').value = pret.type_pret_id;
  document.getElementById('edit_montant').value = pret.montant;
  document.getElementById('edit_duree').value = pret.duree;
  document.getElementById('edit_date_acceptation').value = pret.date_acceptation ? new Date(pret.date_acceptation).toISOString().slice(0, 16) : '';
  document.getElementById('edit_date_refus').value = pret.date_refus ? new Date(pret.date_refus).toISOString().slice(0, 16) : '';
  document.getElementById('edit_date_creation').value = new Date(pret.date_creation).toISOString().slice(0, 16);
  new bootstrap.Modal(document.getElementById('editModal')).show();
}

function deletePret(id) {
  if (confirm('Êtes-vous sûr de vouloir supprimer cet élément ?')) {
    fetch('<?= route('/prets') ?>/' + id, { method: 'DELETE' })
      .then(response => response.json())
      .then(data => {
        alert(data.message || 'Élément supprimé');
        location.reload();
      });
  }
}
</script>