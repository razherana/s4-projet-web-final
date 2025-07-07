<div class="row">
  <div class="col-md-8">
    <div class="card">
      <div class="card-header">
        <h5 class="card-title">Modifier le Type de Prêt</h5>
      </div>
      <div class="card-body">
        <form method="POST" action="<?= route('/type-prets/' . $typePret['id']) ?>">
          <input type="hidden" name="_method" value="PUT">

          <div class="mb-3">
            <label for="nom" class="form-label">Nom</label>
            <input type="text" class="form-control" id="nom" name="nom" value="<?= htmlspecialchars($typePret['nom']) ?>" required>
          </div>

          <div class="mb-3">
            <label for="taux_interet" class="form-label">Taux d'Intérêt (%)</label>
            <input type="number" step="0.01" class="form-control" id="taux_interet" name="taux_interet" value="<?= $typePret['taux_interet'] ?>" required>
          </div>

          <div class="mb-3">
            <label for="taux_assurance" class="form-label">Taux d'Assurance (%)</label>
            <input type="number" step="0.01" class="form-control" id="taux_assurance" name="taux_assurance" value="<?= $typePret['taux_assurance'] ?? 0 ?>">
          </div>

          <div class="mb-3">
            <label for="duree_min" class="form-label">Durée Minimale (mois)</label>
            <input type="number" class="form-control" id="duree_min" name="duree_min" value="<?= $typePret['duree_min'] ?>" required>
          </div>

          <div class="mb-3">
            <label for="duree_max" class="form-label">Durée Maximale (mois)</label>
            <input type="number" class="form-control" id="duree_max" name="duree_max" value="<?= $typePret['duree_max'] ?>" required>
          </div>

          <div class="d-grid gap-2 d-md-flex justify-content-md-end">
            <a href="<?= route('/type-prets') ?>" class="btn btn-secondary">Annuler</a>
            <button type="submit" class="btn btn-primary">Modifier</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>