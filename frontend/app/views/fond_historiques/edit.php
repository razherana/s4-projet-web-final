<div class="row">
  <div class="col-md-8">
    <div class="card">
      <div class="card-header">
        <h5 class="card-title">Modifier l'Historique de Fond</h5>
      </div>
      <div class="card-body">
        <form method="POST" action="<?= route('/fond-historiques/' . $fondHistorique['id']) ?>">
          <input type="hidden" name="_method" value="PUT">

          <div class="mb-3">
            <label for="fond_id" class="form-label">Fond</label>
            <select class="form-select" id="fond_id" name="fond_id" required>
              <option value="">Sélectionner un fond</option>
              <?php if ($fonds): ?>
                <?php foreach ($fonds as $fond): ?>
                  <option value="<?= $fond['id'] ?>" <?= $fond['id'] == $fondHistorique['fond_id'] ? 'selected' : '' ?>>
                    Fond #<?= $fond['id'] ?> - <?= number_format($fond['montant_initial'], 0) ?> Ar
                  </option>
                <?php endforeach; ?>
              <?php endif; ?>
            </select>
          </div>

          <div class="mb-3">
            <label for="description" class="form-label">Description</label>
            <input type="text" class="form-control" id="description" name="description" value="<?= htmlspecialchars($fondHistorique['description']) ?>" required>
          </div>

          <div class="mb-3">
            <label for="montant" class="form-label">Montant</label>
            <input type="number" step="0.01" class="form-control" id="montant" name="montant" value="<?= $fondHistorique['montant'] ?>" required>
          </div>

          <div class="mb-3">
            <label for="est_sortie" class="form-label">Type d'Opération</label>
            <select class="form-select" id="est_sortie" name="est_sortie" required>
              <option value="">Sélectionner le type</option>
              <option value="0" <?= $fondHistorique['est_sortie'] == 0 ? 'selected' : '' ?>>Entrée</option>
              <option value="1" <?= $fondHistorique['est_sortie'] == 1 ? 'selected' : '' ?>>Sortie</option>
            </select>
          </div>

          <div class="d-grid gap-2 d-md-flex justify-content-md-end">
            <a href="<?= route('/fond-historiques') ?>" class="btn btn-secondary">Annuler</a>
            <button type="submit" class="btn btn-primary">Modifier</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>