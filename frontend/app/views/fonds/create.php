<div class="row">
  <div class="col-md-8">
    <div class="card">
      <div class="card-header">
        <h5 class="card-title">Créer un Fond</h5>
      </div>
      <div class="card-body">
        <form method="POST" action="<?= route('/fonds') ?>">
          <div class="mb-3">
            <label for="montant_initial" class="form-label">Montant Initial</label>
            <input type="number" step="0.01" class="form-control" id="montant_initial" name="montant_initial" required>
          </div>

          <div class="mb-3">
            <label for="description" class="form-label">Description</label>
            <textarea class="form-control" id="description" name="description" rows="3" required></textarea>
          </div>

          <div class="mb-3">
            <label for="source_fond_id" class="form-label">Source de Fond</label>
            <select class="form-select" id="source_fond_id" name="source_fond_id" required>
              <option value="">Sélectionner une source</option>
              <?php if ($sourceFonds): ?>
                <?php foreach ($sourceFonds as $source): ?>
                  <option value="<?= $source['id'] ?>"><?= htmlspecialchars($source['nom']) ?></option>
                <?php endforeach; ?>
              <?php endif; ?>
            </select>
          </div>

          <div class="d-grid gap-2 d-md-flex justify-content-md-end">
            <a href="<?= route('/fonds') ?>" class="btn btn-secondary">Annuler</a>
            <button type="submit" class="btn btn-primary">Créer</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>