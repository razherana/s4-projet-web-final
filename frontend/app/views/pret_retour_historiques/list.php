<div class="d-flex justify-content-between align-items-center mb-3">
  <h3>Liste des Historiques de Retour</h3>
  <a href="<?= route('/pret-retour-historiques/create') ?>" class="btn btn-primary">
    <i class="fas fa-plus"></i> Ajouter un Historique
  </a>
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
            <td><?= date('d/m/Y', $historique['date_retour']) ?></td>
            <td>
              <a href="<?= route('/pret-retour-historiques/' . $historique['id'] . '/edit') ?>" class="btn btn-sm btn-outline-primary">
                <i class="fas fa-edit"></i>
              </a>
              <button onclick="confirmDelete('<?= route('/pret-retour-historiques/' . $historique['id']) ?>')" class="btn btn-sm btn-outline-danger">
                <i class="fas fa-trash"></i>
              </button>
            </td>
          </tr>
        <?php endforeach; ?>
      <?php endif; ?>
    </tbody>
  </table>
</div>