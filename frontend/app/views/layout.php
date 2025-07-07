<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= $title ?? 'CRUD Application' ?></title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>

<body>
  <div class="container-fluid">
    <div class="row min-vh-100">
      <!-- Sidebar -->
      <nav id="sidebar" class="col-md-3 col-lg-2 d-md-block bg-light sidebar collapse">
        <div class="position-sticky pt-3">
          <h5 class="sidebar-heading px-3 mb-3 text-muted">
            <i class="fas fa-database"></i> CRUD Management
          </h5>
          <ul class="nav flex-column">
            <li class="nav-item">
              <a class="nav-link" href="<?= route('/fonds') ?>">
                <i class="fas fa-money-bill-wave"></i> Fonds
              </a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="<?= route('/source-fonds') ?>">
                <i class="fa-solid fa-gears"></i> Source Fonds
              </a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="<?= route('/type-prets') ?>">
                <i class="fas fa-percentage"></i> Type Prêts
              </a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="<?= route('/clients') ?>">
                <i class="fas fa-users"></i> Clients
              </a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="<?= route('/prets') ?>">
                <i class="fas fa-handshake"></i> Prêts
              </a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="<?= route('/pret-retour-historiques') ?>">
                <i class="fas fa-history"></i> Historiques Retours
              </a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="<?= route('/users') ?>">
                <i class="fas fa-user"></i> Users
              </a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="<?= route('/fond-historiques') ?>">
                <i class="fas fa-chart-line"></i> Historiques Fonds
              </a>
            </li>
          </ul>
        </div>
      </nav>

      <!-- Main content -->
      <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
        <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
          <h1 class="h2"><?= $title ?? 'Dashboard' ?></h1>
        </div>

        <?= $content ?>
      </main>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    // Delete confirmation
    function confirmDelete(url) {
      if (confirm('Êtes-vous sûr de vouloir supprimer cet élément ?')) {
        fetch(url, {
            method: 'DELETE'
          })
          .then(response => response.json())
          .then(data => {
            alert(data.message || 'Élément supprimé');
            location.reload();
          })
          .catch(error => {
            console.error('Error:', error);
            alert('Erreur lors de la suppression');
          });
      }
    }
  </script>
</body>

</html>