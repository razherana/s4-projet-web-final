<div class="row">
  <div class="col-md-8">
    <div class="card">
      <div class="card-header">
        <h5 class="card-title">Modifier l'Historique de Retour</h5>
      </div>
      <div class="card-body">
        <form method="POST" action="<?= route('/pret-retour-historiques/' . $pretRetourHistorique['id']) ?>">
          <input type="hidden" name="_method" value="PUT">

          <div class="mb-3">
            <label for="pret_id" class="form-label">Prêt</label>
            <select class="form-select" id="pret_id" name="pret_id" required>
              <option value="">Sélectionner un prêt</option>
              <?php if ($prets): ?>
                <?php foreach ($prets as $pret): ?>
                  <option value="<?= $pret['id'] ?>" <?= $pret['id'] == $pretRetourHistorique['pret_id'] ? 'selected' : '' ?>>
                    Prêt #<?= $pret['id'] ?> - <?= number_format($pret['montant'], 0) ?> Ar
                  </option>
                <?php endforeach; ?>
              <?php endif; ?>
            </select>
          </div>

          <div class="mb-3">
            <label for="montant" class="form-label">Montant</label>
            <input type="number" step="0.01" class="form-control" id="montant" name="montant" value="<?= $pretRetourHistorique['montant'] ?>" required>
          </div>

          <div class="mb-3">
            <label for="date_retour" class="form-label">Date de Retour</label>
            <input type="date" class="form-control" id="date_retour" name="date_retour" value="<?= date('Y-m-d', $pretRetourHistorique['date_retour']) ?>" required>
          </div>

          <div class="d-grid gap-2 d-md-flex justify-content-md-end">
            <a href="<?= route('/pret-retour-historiques') ?>" class="btn btn-secondary">Annuler</a>
            <button type="submit" class="btn btn-primary">Modifier</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>