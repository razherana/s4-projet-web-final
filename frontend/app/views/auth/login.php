<div class="auth-card">
  <div class="auth-header">
    <div class="auth-logo">
      <i class="fas fa-chart-line"></i>
    </div>
    <h1 class="auth-title">FinanceAdmin</h1>
    <p class="auth-subtitle">Connectez-vous à votre espace d'administration</p>
  </div>
  
  <div class="auth-body">
    <!-- Error/Success Messages -->
    <?php if (isset($_GET['error'])): ?>
      <div class="alert alert-danger">
        <i class="fas fa-exclamation-triangle"></i>
        <?php
          switch ($_GET['error']) {
            case 'missing_credentials':
              echo 'Veuillez saisir votre email et mot de passe.';
              break;
            case 'invalid_credentials':
              echo 'Email ou mot de passe incorrect.';
              break;
            case 'server_error':
              echo 'Erreur serveur. Veuillez réessayer.';
              break;
            default:
              echo 'Une erreur est survenue.';
          }
        ?>
      </div>
    <?php endif; ?>

    <?php if (isset($_GET['success']) && $_GET['success'] === 'logged_out'): ?>
      <div class="alert alert-success">
        <i class="fas fa-check-circle"></i>
        Vous avez été déconnecté avec succès.
      </div>
    <?php endif; ?>

    <form id="loginForm" method="POST" action="<?= route('/login') ?>">
      <div class="form-group">
        <label for="email" class="form-label">
          <i class="fas fa-envelope"></i>
          Adresse email
        </label>
        <input 
          type="email" 
          class="form-control" 
          id="email" 
          name="email" 
          placeholder="admin@admin.com"
          required 
          autocomplete="email"
        >
      </div>
      
      <div class="form-group">
        <label for="password" class="form-label">
          <i class="fas fa-lock"></i>
          Mot de passe
        </label>
        <input 
          type="password" 
          class="form-control" 
          id="password" 
          name="password" 
          placeholder="••••••••"
          required 
          autocomplete="current-password"
        >
      </div>
      
      <button type="submit" class="btn-login" id="loginBtn">
        <span class="btn-text">Se connecter</span>
        <div class="spinner d-none"></div>
      </button>
    </form>
  </div>
  
  <div class="auth-footer">
    <p class="footer-text">
      <i class="fas fa-shield-alt"></i>
      Connexion sécurisée - FinanceAdmin v1.0
    </p>
  </div>
</div>

<script>
document.getElementById('loginForm').addEventListener('submit', function(e) {
  const btn = document.getElementById('loginBtn');
  const btnText = btn.querySelector('.btn-text');
  const spinner = btn.querySelector('.spinner');
  
  // Show loading state
  btn.disabled = true;
  btnText.textContent = 'Connexion...';
  spinner.classList.remove('d-none');
  
  // Form will submit normally, loading state will be visible until page redirects
});

// Auto-hide alerts after 5 seconds
document.addEventListener('DOMContentLoaded', function() {
  const alerts = document.querySelectorAll('.alert');
  alerts.forEach(alert => {
    setTimeout(() => {
      alert.style.opacity = '0';
      alert.style.transform = 'translateY(-10px)';
      setTimeout(() => alert.remove(), 300);
    }, 5000);
  });
});

// Demo credentials helper
document.addEventListener('DOMContentLoaded', function() {
  const emailInput = document.getElementById('email');
  const passwordInput = document.getElementById('password');
  
  // Pre-fill demo credentials
  emailInput.value = 'admin@admin.com';
  passwordInput.value = 'admin123';
});
</script>
