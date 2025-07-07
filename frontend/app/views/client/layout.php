<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= $title ?? 'Mon Espace Client' ?></title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <style>
    :root {
      --primary-color: #3B82F6;
      --primary-dark: #1E40AF;
      --secondary-color: #6366F1;
      --accent-color: #10B981;
      --sidebar-bg: #1F2937;
      --sidebar-hover: #374151;
      --light-bg: #F8FAFC;
      --card-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
      --card-shadow-hover: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
      --border-color: #E5E7EB;
      --text-primary: #111827;
      --text-secondary: #6B7280;
      --sidebar-width: 260px;
      --header-height: 70px;
    }

    body {
      font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
      background: var(--light-bg);
      margin: 0;
      overflow-x: hidden;
    }

    /* Layout Grid */
    .client-container {
      display: grid;
      grid-template-columns: var(--sidebar-width) 1fr;
      grid-template-rows: var(--header-height) 1fr;
      grid-template-areas: 
        "sidebar header"
        "sidebar main";
      min-height: 100vh;
      position: fixed;
      width: 100%;
      top: 0;
      left: 0;
    }

    /* Sidebar */
    .client-sidebar {
      grid-area: sidebar;
      background: linear-gradient(135deg, var(--sidebar-bg) 0%, #111827 100%);
      color: white;
      overflow-y: auto;
      box-shadow: 4px 0 15px rgba(0, 0, 0, 0.1);
      z-index: 1000;
    }

    .sidebar-header {
      padding: 1.25rem;
      border-bottom: 1px solid rgba(255, 255, 255, 0.1);
      display: flex;
      align-items: center;
      gap: 0.75rem;
    }

    .sidebar-logo {
      width: 36px;
      height: 36px;
      background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
      border-radius: 10px;
      display: flex;
      align-items: center;
      justify-content: center;
      color: white;
      font-size: 1rem;
    }

    .sidebar-title {
      font-size: 1.125rem;
      font-weight: 700;
      margin: 0;
    }

    .sidebar-subtitle {
      font-size: 0.75rem;
      color: rgba(255, 255, 255, 0.6);
      margin: 0;
    }

    .sidebar-nav {
      padding: 1rem 0;
    }

    .nav-section {
      margin-bottom: 1.5rem;
    }

    .nav-section-title {
      padding: 0 1.25rem 0.5rem;
      font-size: 0.75rem;
      font-weight: 600;
      text-transform: uppercase;
      color: rgba(255, 255, 255, 0.5);
      letter-spacing: 0.05em;
    }

    .nav-item {
      margin: 0.25rem 1rem;
    }

    .nav-link {
      display: flex;
      align-items: center;
      gap: 0.75rem;
      padding: 0.75rem 1rem;
      color: rgba(255, 255, 255, 0.8);
      text-decoration: none;
      border-radius: 10px;
      font-weight: 500;
      transition: all 0.2s ease;
      position: relative;
    }

    .nav-link:hover {
      background: var(--sidebar-hover);
      color: white;
      transform: translateX(2px);
    }

    .nav-link.active {
      background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
      color: white;
      box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
    }

    .nav-link i {
      width: 18px;
      text-align: center;
      font-size: 0.9rem;
    }

    /* Header */
    .client-header {
      grid-area: header;
      background: white;
      border-bottom: 1px solid var(--border-color);
      display: flex;
      align-items: center;
      justify-content: space-between;
      padding: 0 1.5rem;
      box-shadow: var(--card-shadow);
      z-index: 999;
    }

    .header-title {
      display: flex;
      flex-direction: column;
      gap: 0.125rem;
    }

    .header-title h1 {
      font-size: 1.5rem;
      font-weight: 700;
      color: var(--text-primary);
      margin: 0;
    }

    .header-subtitle {
      font-size: 0.8rem;
      color: var(--text-secondary);
      margin: 0;
    }

    .header-actions {
      display: flex;
      align-items: center;
      gap: 1rem;
    }

    .user-info {
      display: flex;
      align-items: center;
      gap: 0.75rem;
    }

    .user-avatar {
      width: 40px;
      height: 40px;
      border-radius: 50%;
      background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
      display: flex;
      align-items: center;
      justify-content: center;
      color: white;
      font-weight: 600;
      font-size: 0.875rem;
    }

    .user-details {
      text-align: right;
    }

    .user-name {
      font-size: 0.875rem;
      font-weight: 600;
      color: var(--text-primary);
      margin: 0;
    }

    .user-role {
      font-size: 0.75rem;
      color: var(--text-secondary);
      margin: 0;
    }

    .header-btn {
      width: 36px;
      height: 36px;
      border-radius: 10px;
      border: 1px solid var(--border-color);
      background: white;
      color: var(--text-secondary);
      display: flex;
      align-items: center;
      justify-content: center;
      cursor: pointer;
      transition: all 0.2s ease;
    }

    .header-btn:hover {
      background: var(--primary-color);
      color: white;
      border-color: var(--primary-color);
      transform: translateY(-1px);
    }

    /* Main Content */
    .client-main {
      grid-area: main;
      padding: 1.5rem;
      overflow-y: auto;
      height: calc(100vh - var(--header-height));
    }

    /* Cards */
    .card {
      background: white;
      border: 1px solid var(--border-color);
      border-radius: 12px;
      box-shadow: var(--card-shadow);
      transition: all 0.3s ease;
      overflow: hidden;
    }

    .card:hover {
      box-shadow: var(--card-shadow-hover);
      transform: translateY(-2px);
    }

    /* Responsive Design */
    @media (max-width: 768px) {
      .client-container {
        grid-template-columns: 1fr;
        grid-template-rows: var(--header-height) 1fr;
        grid-template-areas: 
          "header"
          "main";
      }

      .client-sidebar {
        position: fixed;
        left: -100%;
        top: var(--header-height);
        height: calc(100vh - var(--header-height));
        width: var(--sidebar-width);
        transition: left 0.3s ease;
        z-index: 1001;
      }

      .client-sidebar.show {
        left: 0;
      }

      .client-main {
        padding: 1rem;
      }

      .user-details {
        display: none;
      }
    }

    /* Animation utilities */
    .fade-in {
      animation: fadeIn 0.5s ease-in;
    }

    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(20px); }
      to { opacity: 1; transform: translateY(0); }
    }
  </style>
</head>
<body>
  <?php
  
  if (!isset($_SESSION['user'])) {
    header('Location: /login');
    exit;
  }
  $currentUser = $_SESSION['user'];
  ?>
  
  <div class="client-container">
    <!-- Sidebar -->
    <aside class="client-sidebar" id="sidebar">
      <div class="sidebar-header">
        <div class="sidebar-logo">
          <i class="fas fa-user"></i>
        </div>
        <div>
          <h1 class="sidebar-title">Mon Espace</h1>
          <p class="sidebar-subtitle"><?= htmlspecialchars($currentUser['nom']) ?></p>
        </div>
      </div>
      
      <nav class="sidebar-nav">
        <div class="nav-section">
          <div class="nav-section-title">Navigation</div>
          <div class="nav-item">
            <a class="nav-link <?= strpos($_SERVER['REQUEST_URI'], '/client/dashboard') !== false ? 'active' : '' ?>" href="<?= route('/client/dashboard') ?>">
              <i class="fas fa-tachometer-alt"></i>
              <span>Tableau de Bord</span>
            </a>
          </div>
          <div class="nav-item">
            <a class="nav-link <?= strpos($_SERVER['REQUEST_URI'], '/client/loans') !== false ? 'active' : '' ?>" href="<?= route('/client/loans') ?>">
              <i class="fas fa-handshake"></i>
              <span>Mes Prêts</span>
            </a>
          </div>
          <div class="nav-item">
            <a class="nav-link <?= strpos($_SERVER['REQUEST_URI'], '/client/simulate') !== false ? 'active' : '' ?>" href="<?= route('/client/simulate') ?>">
              <i class="fas fa-calculator"></i>
              <span>Simuler un Prêt</span>
            </a>
          </div>
        </div>

        <div class="nav-section">
          <div class="nav-section-title">Compte</div>
          <div class="nav-item">
            <a class="nav-link" href="<?= route('/logout') ?>">
              <i class="fas fa-sign-out-alt"></i>
              <span>Déconnexion</span>
            </a>
          </div>
        </div>
      </nav>
    </aside>

    <!-- Header -->
    <header class="client-header">
      <div class="header-title">
        <h1><?= $title ?? 'Mon Espace' ?></h1>
        <p class="header-subtitle"><?= $subtitle ?? 'Bienvenue dans votre espace client' ?></p>
      </div>
      
      <div class="header-actions">
        <button class="header-btn d-md-none" onclick="toggleSidebar()">
          <i class="fas fa-bars"></i>
        </button>
        
        <div class="user-info">
          <div class="user-details">
            <p class="user-name"><?= htmlspecialchars($currentUser['nom'] . ' ' . $currentUser['prenom']) ?></p>
            <p class="user-role">Client</p>
          </div>
          <div class="user-avatar">
            <?= strtoupper(substr($currentUser['nom'], 0, 1) . substr($currentUser['prenom'], 0, 1)) ?>
          </div>
        </div>
      </div>
    </header>

    <!-- Main Content -->
    <main class="client-main fade-in">
      <?= $content ?>
    </main>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    // Sidebar toggle for mobile
    function toggleSidebar() {
      const sidebar = document.getElementById('sidebar');
      sidebar.classList.toggle('show');
    }

    // Close sidebar when clicking outside on mobile
    document.addEventListener('click', function(e) {
      const sidebar = document.getElementById('sidebar');
      const toggleBtn = document.querySelector('.header-btn');
      
      if (window.innerWidth <= 768 && 
          !sidebar.contains(e.target) && 
          !toggleBtn.contains(e.target)) {
        sidebar.classList.remove('show');
      }
    });

    // Auto-hide sidebar on window resize
    window.addEventListener('resize', function() {
      const sidebar = document.getElementById('sidebar');
      if (window.innerWidth > 768) {
        sidebar.classList.remove('show');
      }
    });
  </script>
</body>
</html>
