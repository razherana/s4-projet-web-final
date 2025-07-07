<div class="row">
  <div class="col-md-8">
    <div class="card">
      <div class="card-header">
        <h5 class="card-title">Modifier la Source de Fonds</h5>
      </div>
      <div class="card-body">
        <form method="POST" action="<?= route('/source-fonds/' . $sourceFond['id']) ?>">
          <input type="hidden" name="_method" value="PUT">

          <div class="mb-3">
            <label for="nom" class="form-label">Nom</label>
            <input type="text" class="form-control" id="nom" name="nom" value="<?= htmlspecialchars($sourceFond['nom']) ?>" required>
          </div>

          <div class="d-grid gap-2 d-md-flex justify-content-md-end">
            <a href="<?= route('/source-fonds') ?>" class="btn btn-secondary">Annuler</a>
            <button type="submit" class="btn btn-primary">Modifier</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>