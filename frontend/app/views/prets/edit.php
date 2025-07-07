<div class="row">
  <div class="col-md-8">
    <div class="card">
      <div class="card-header">
        <h5 class="card-title">Modifier le Prêt</h5>
      </div>
      <div class="card-body">
        <form method="POST" action="<?= route('/prets/' . $pret['id']) ?>">
          <input type="hidden" name="_method" value="PUT">

          <div class="mb-3">
            <label for="client_id" class="form-label">Client</label>
            <select class="form-select" id="client_id" name="client_id" required>
              <option value="">Sélectionner un client</option>
              <?php if ($clients): ?>
                <?php foreach ($clients as $client): ?>
                  <option value="<?= $client['id'] ?>" <?= $client['id'] == $pret['client_id'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($client['nom'] . ' ' . $client['prenom']) ?>
                  </option>
                <?php endforeach; ?>
              <?php endif; ?>
            </select>
          </div>

          <div class="mb-3">
            <label for="type_pret_id" class="form-label">Type de Prêt</label>
            <select class="form-select" id="type_pret_id" name="type_pret_id" required>
              <option value="">Sélectionner un type</option>
              <?php if ($typePrets): ?>
                <?php foreach ($typePrets as $type): ?>
                  <option value="<?= $type['id'] ?>" <?= $type['id'] == $pret['type_pret_id'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($type['nom']) ?> (<?= $type['taux_interet'] ?>%)
                  </option>
                <?php endforeach; ?>
              <?php endif; ?>
            </select>
          </div>

          <div class="mb-3">
            <label for="montant" class="form-label">Montant</label>
            <input type="number" class="form-control" id="montant" name="montant" value="<?= $pret['montant'] ?>" required>
          </div>

          <div class="mb-3">
            <label for="duree" class="form-label">Durée (mois)</label>
            <input type="number" class="form-control" id="duree" name="duree" value="<?= $pret['duree'] ?>" required>
          </div>

          <div class="mb-3">
            <label for="date_acceptation" class="form-label">Date d'Acceptation</label>
            <input type="datetime-local" class="form-control" id="date_acceptation" name="date_acceptation"
              value="<?= $pret['date_acceptation'] ? date('Y-m-d\TH:i', strtotime($pret['date_acceptation'])) : '' ?>">
          </div>

          <div class="mb-3">
            <label for="date_refus" class="form-label">Date de Refus</label>
            <input type="datetime-local" class="form-control" id="date_refus" name="date_refus"
              value="<?= $pret['date_refus'] ? date('Y-m-d\TH:i', strtotime($pret['date_refus'])) : '' ?>">
          </div>

          <div class="mb-3">
            <label for="date_creation" class="form-label">Date de Création</label>
            <input type="datetime-local" class="form-control" id="date_creation" name="date_creation"
              value="<?= date('Y-m-d\TH:i', strtotime($pret['date_creation'])) ?>" required>
          </div>

          <div class="d-grid gap-2 d-md-flex justify-content-md-end">
            <a href="<?= route('/prets') ?>" class="btn btn-secondary">Annuler</a>
            <button type="submit" class="btn btn-primary">Modifier</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>