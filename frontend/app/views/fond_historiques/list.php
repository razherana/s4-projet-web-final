<div class="d-flex justify-content-between align-items-center mb-3">
  <h3>Liste des Historiques de Fonds</h3>
  <a href="<?= route('/fond-historiques/create') ?>" class="btn btn-primary">
    <i class="fas fa-plus"></i> Ajouter un Historique
  </a>
</div>

<div class="table-responsive">
  <table class="table table-striped">
    <thead>
      <tr>
        <th>ID</th>
        <th>Fond</th>
        <th>Description</th>
        <th>Montant</th>
        <th>Type</th>
        <th>Date</th>
        <th>Actions</th>
      </tr>
    </thead>
    <tbody>
      <?php if (empty($fondHistoriques)): ?>
        <tr>
          <td colspan="7" class="text-center">Aucun historique trouvé</td>
        </tr>
      <?php else: ?>
        <?php foreach ($fondHistoriques as $historique): ?>
          <tr>
            <td><?= $historique['id'] ?></td>
            <td>
              <?php
              $fondInfo = 'N/A';
              if ($fonds) {
                foreach ($fonds as $fond) {
                  if ($fond['id'] == $historique['fond_id']) {
                    $fondInfo = 'Fond #' . $fond['id'] . ' (' . number_format($fond['montant_initial'], 0) . ' Ar)';
                    break;
                  }
                }
              }
              echo htmlspecialchars($fondInfo);
              ?>
            </td>
            <td><?= htmlspecialchars($historique['description']) ?></td>
            <td><?= number_format($historique['montant'], 2) ?> Ar</td>
            <td>
              <?php if ($historique['est_sortie']): ?>
                <span class="badge bg-danger">Sortie</span>
              <?php else: ?>
                <span class="badge bg-success">Entrée</span>
              <?php endif; ?>
            </td>
            <td><?= date('d/m/Y H:i', strtotime($historique['date'])) ?></td>
            <td>
              <a href="<?= route('/fond-historiques/' . $historique['id'] . '/edit') ?>" class="btn btn-sm btn-outline-primary">
                <i class="fas fa-edit"></i>
              </a>
              <button onclick="confirmDelete('<?= route('/fond-historiques/' . $historique['id']) ?>')" class="btn btn-sm btn-outline-danger">
                <i class="fas fa-trash"></i>
              </button>
            </td>
          </tr>
        <?php endforeach; ?>
      <?php endif; ?>
    </tbody>
  </table>
</div>