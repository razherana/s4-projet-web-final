<div class="row">
  <div class="col-md-8">
    <div class="card">
      <div class="card-header">
        <h5 class="card-title">Créer une Source de Fonds</h5>
      </div>
      <div class="card-body">
        <form method="POST" action="<?= route('/source-fonds') ?>">
          <div class="mb-3">
            <label for="nom" class="form-label">Nom</label>
            <input type="text" class="form-control" id="nom" name="nom" required>
          </div>

          <div class="d-grid gap-2 d-md-flex justify-content-md-end">
            <a href="<?= route('/source-fonds') ?>" class="btn btn-secondary">Annuler</a>
            <button type="submit" class="btn btn-primary">Créer</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>