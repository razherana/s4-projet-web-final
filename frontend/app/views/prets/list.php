<div class="d-flex justify-content-between align-items-center mb-3">
  <h3>Liste des Prêts</h3>
  <a href="<?= route('/prets/create') ?>" class="btn btn-primary">
    <i class="fas fa-plus"></i> Ajouter un Prêt
  </a>
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
              if ($typePrets) {
                foreach ($typePrets as $type) {
                  if ($type['id'] == $pret['type_pret_id']) {
                    $typeName = $type['nom'];
                    break;
                  }
                }
              }
              echo htmlspecialchars($typeName);
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
              <a href="<?= route('/prets/' . $pret['id'] . '/edit') ?>" class="btn btn-sm btn-outline-primary">
                <i class="fas fa-edit"></i>
              </a>
              <button onclick="confirmDelete('<?= route('/prets/' . $pret['id']) ?>')" class="btn btn-sm btn-outline-danger">
                <i class="fas fa-trash"></i>
              </button>
            </td>
          </tr>
        <?php endforeach; ?>
      <?php endif; ?>
    </tbody>
  </table>
</div>