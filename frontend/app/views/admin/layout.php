<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= $title ?? 'Admin Dashboard' ?></title>
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
      --sidebar-width: 280px;
      --header-height: 80px;
    }

    * {
      box-sizing: border-box;
    }

    body {
      font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
      background: var(--light-bg);
      margin: 0;
      overflow-x: hidden;
    }

    /* Layout Grid */
    .admin-container {
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
    .admin-sidebar {
      grid-area: sidebar;
      background: linear-gradient(135deg, var(--sidebar-bg) 0%, #111827 100%);
      color: white;
      overflow-y: auto;
      box-shadow: 4px 0 15px rgba(0, 0, 0, 0.1);
      z-index: 1000;
    }

    .sidebar-header {
      padding: 1.5rem;
      border-bottom: 1px solid rgba(255, 255, 255, 0.1);
      display: flex;
      align-items: center;
      gap: 0.75rem;
    }

    .sidebar-logo {
      width: 40px;
      height: 40px;
      background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
      border-radius: 12px;
      display: flex;
      align-items: center;
      justify-content: center;
      color: white;
      font-size: 1.2rem;
    }

    .sidebar-title {
      font-size: 1.25rem;
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
      margin-bottom: 2rem;
    }

    .nav-section-title {
      padding: 0 1.5rem 0.5rem;
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
      border-radius: 12px;
      font-weight: 500;
      transition: all 0.2s ease;
      position: relative;
    }

    .nav-link:hover {
      background: var(--sidebar-hover);
      color: white;
      transform: translateX(4px);
    }

    .nav-link.active {
      background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
      color: white;
      box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
    }

    .nav-link i {
      width: 20px;
      text-align: center;
      font-size: 1rem;
    }

    /* Header */
    .admin-header {
      grid-area: header;
      background: white;
      border-bottom: 1px solid var(--border-color);
      display: flex;
      align-items: center;
      justify-content: space-between;
      padding: 0 2rem;
      box-shadow: var(--card-shadow);
      z-index: 999;
    }

    .header-title {
      display: flex;
      flex-direction: column;
      gap: 0.25rem;
    }

    .header-title h1 {
      font-size: 1.75rem;
      font-weight: 700;
      color: var(--text-primary);
      margin: 0;
    }

    .header-subtitle {
      font-size: 0.875rem;
      color: var(--text-secondary);
      margin: 0;
    }

    .header-actions {
      display: flex;
      align-items: center;
      gap: 1rem;
    }

    .header-stats {
      display: flex;
      align-items: center;
      gap: 1.5rem;
    }

    .stat-item {
      text-align: right;
    }

    .stat-label {
      font-size: 0.75rem;
      color: var(--text-secondary);
      margin: 0;
    }

    .stat-value {
      font-size: 0.875rem;
      font-weight: 600;
      color: var(--text-primary);
      margin: 0;
    }

    .header-btn {
      width: 40px;
      height: 40px;
      border-radius: 12px;
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
    .admin-main {
      grid-area: main;
      padding: 2rem;
      overflow-y: auto;
      height: calc(100vh - var(--header-height));
    }

    /* Cards */
    .card {
      background: white;
      border: 1px solid var(--border-color);
      border-radius: 16px;
      box-shadow: var(--card-shadow);
      transition: all 0.3s ease;
      overflow: hidden;
    }

    .card:hover {
      box-shadow: var(--card-shadow-hover);
      transform: translateY(-2px);
    }

    .card-header {
      background: transparent;
      border-bottom: 1px solid var(--border-color);
      padding: 1.5rem;
      font-weight: 600;
      color: var(--text-primary);
    }

    .card-body {
      padding: 1.5rem;
    }

    /* Stats Cards */
    .stats-card {
      background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark) 100%);
      color: white;
      border: none;
      position: relative;
      overflow: hidden;
    }

    .stats-card::before {
      content: '';
      position: absolute;
      top: 0;
      right: 0;
      width: 100px;
      height: 100px;
      background: rgba(255, 255, 255, 0.1);
      border-radius: 50%;
      transform: translate(30px, -30px);
    }

    .stats-card.warning {
      background: linear-gradient(135deg, #F59E0B 0%, #D97706 100%);
    }

    .stats-card.success {
      background: linear-gradient(135deg, var(--accent-color) 0%, #059669 100%);
    }

    .stats-card.info {
      background: linear-gradient(135deg, var(--secondary-color) 0%, #4F46E5 100%);
    }

    .metric-value {
      font-size: 2.5rem;
      font-weight: 800;
      margin-bottom: 0.5rem;
      position: relative;
      z-index: 2;
    }

    .metric-label {
      font-size: 0.875rem;
      opacity: 0.9;
      margin-bottom: 0.75rem;
      position: relative;
      z-index: 2;
    }

    .metric-change {
      font-size: 0.75rem;
      padding: 0.25rem 0.5rem;
      border-radius: 20px;
      background: rgba(255, 255, 255, 0.2);
      display: inline-flex;
      align-items: center;
      gap: 0.25rem;
      position: relative;
      z-index: 2;
    }

    /* Form Elements */
    .form-control, .form-select {
      border-radius: 12px;
      border: 2px solid var(--border-color);
      padding: 0.75rem 1rem;
      font-size: 0.875rem;
      transition: all 0.2s ease;
    }

    .form-control:focus, .form-select:focus {
      border-color: var(--primary-color);
      box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
    }

    .btn-primary {
      background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark) 100%);
      border: none;
      border-radius: 12px;
      padding: 0.75rem 1.5rem;
      font-weight: 600;
      font-size: 0.875rem;
      transition: all 0.2s ease;
    }

    .btn-primary:hover {
      transform: translateY(-2px);
      box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
    }

    .btn-outline-primary {
      border: 2px solid var(--primary-color);
      color: var(--primary-color);
      background: transparent;
      border-radius: 12px;
      padding: 0.5rem 1rem;
      font-weight: 600;
      transition: all 0.2s ease;
    }

    .btn-outline-primary:hover {
      background: var(--primary-color);
      color: white;
      transform: translateY(-1px);
    }

    /* Chart Container */
    .chart-container {
      position: relative;
      padding: 1rem;
      border-radius: 12px;
      background: #FAFBFC;
    }

    /* Responsive Design */
    @media (max-width: 1024px) {
      :root {
        --sidebar-width: 240px;
      }
    }

    @media (max-width: 768px) {
      .admin-container {
        grid-template-columns: 1fr;
        grid-template-rows: var(--header-height) 1fr;
        grid-template-areas: 
          "header"
          "main";
      }

      .admin-sidebar {
        position: fixed;
        left: -100%;
        top: var(--header-height);
        height: calc(100vh - var(--header-height));
        width: var(--sidebar-width);
        transition: left 0.3s ease;
        z-index: 1001;
      }

      .admin-sidebar.show {
        left: 0;
      }

      .admin-main {
        padding: 1rem;
      }

      .header-stats {
        display: none;
      }

      .metric-value {
        font-size: 2rem;
      }
    }

    @media (max-width: 480px) {
      .admin-main {
        padding: 0.75rem;
      }

      .card-body {
        padding: 1rem;
      }

      .metric-value {
        font-size: 1.75rem;
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

    /* Loading states */
    .loading {
      opacity: 0.6;
      pointer-events: none;
    }

    .spinner {
      width: 20px;
      height: 20px;
      border: 2px solid rgba(255, 255, 255, 0.3);
      border-radius: 50%;
      border-top-color: white;
      animation: spin 1s ease-in-out infinite;
    }

    @keyframes spin {
      to { transform: rotate(360deg); }
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
  
  <div class="admin-container">
    <!-- Sidebar -->
    <aside class="admin-sidebar" id="sidebar">
      <div class="sidebar-header">
        <div class="sidebar-logo">
          <i class="fas fa-chart-line"></i>
        </div>
        <div>
          <h1 class="sidebar-title">FinanceAdmin</h1>
          <p class="sidebar-subtitle">Connecté: <?= htmlspecialchars($currentUser['nom']) ?></p>
        </div>
      </div>
      
      <nav class="sidebar-nav">
        <div class="nav-section">
          <div class="nav-section-title">Analyse</div>
          <div class="nav-item">
            <a class="nav-link <?= strpos($_SERVER['REQUEST_URI'], '/admin/interets') !== false ? 'active' : '' ?>" href="<?= route('/admin/interets') ?>">
              <i class="fas fa-chart-line"></i>
              <span>Intérêts</span>
            </a>
          </div>
          <div class="nav-item">
            <a class="nav-link <?= strpos($_SERVER['REQUEST_URI'], '/admin/dashboard') !== false ? 'active' : '' ?>" href="<?= route('/admin/dashboard') ?>">
              <i class="fas fa-tachometer-alt"></i>
              <span>Dashboard</span>
            </a>
          </div>
        </div>

        <div class="nav-section">
          <div class="nav-section-title">Gestion</div>
          <div class="nav-item">
            <a class="nav-link <?= strpos($_SERVER['REQUEST_URI'], '/admin/prets') !== false ? 'active' : '' ?>" href="<?= route('/admin/prets') ?>">
              <i class="fas fa-handshake"></i>
              <span>Prêts</span>
            </a>
          </div>
          <div class="nav-item">
            <a class="nav-link <?= strpos($_SERVER['REQUEST_URI'], '/admin/clients') !== false ? 'active' : '' ?>" href="<?= route('/admin/clients') ?>">
              <i class="fas fa-users"></i>
              <span>Clients</span>
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
    <header class="admin-header">
      <div class="header-title">
        <h1><?= $title ?? 'Dashboard' ?></h1>
        <p class="header-subtitle"><?= $subtitle ?? 'Bienvenue dans votre espace d\'administration' ?></p>
      </div>
      
      <div class="header-actions">
        <div class="header-stats">
          <div class="stat-item">
            <p class="stat-label">Connecté en tant que</p>
            <p class="stat-value"><?= htmlspecialchars($currentUser['role']) ?></p>
          </div>
          <div class="stat-item">
            <p class="stat-label">Dernière connexion</p>
            <p class="stat-value"><?= date('H:i') ?></p>
          </div>
        </div>
        
        <button class="header-btn d-md-none" onclick="toggleSidebar()">
          <i class="fas fa-bars"></i>
        </button>
        
        <button class="header-btn" onclick="refreshData()">
          <i class="fas fa-sync-alt"></i>
        </button>
        
        <button class="header-btn">
          <i class="fas fa-bell"></i>
        </button>
      </div>
    </header>

    <!-- Main Content -->
    <main class="admin-main fade-in">
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

    // Refresh data functionality
    function refreshData() {
      const btn = event.target.closest('.header-btn');
      const icon = btn.querySelector('i');
      
      icon.classList.add('fa-spin');
      
      setTimeout(() => {
        icon.classList.remove('fa-spin');
        // Add your data refresh logic here
        if (typeof submitForm === 'function') {
          submitForm();
        }
      }, 1000);
    }

    // Auto-hide sidebar on window resize
    window.addEventListener('resize', function() {
      const sidebar = document.getElementById('sidebar');
      if (window.innerWidth > 768) {
        sidebar.classList.remove('show');
      }
    });

    // Smooth scroll for better UX
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
      anchor.addEventListener('click', function (e) {
        e.preventDefault();
        document.querySelector(this.getAttribute('href')).scrollIntoView({
          behavior: 'smooth'
        });
      });
    });
  </script>
</body>
</html>
