<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= $title ?? 'FinanceAdmin' ?></title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
  <style>
    :root {
      --primary-color: #3B82F6;
      --primary-dark: #1E40AF;
      --secondary-color: #6366F1;
      --accent-color: #10B981;
      --light-bg: #F8FAFC;
      --card-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
      --border-color: #E5E7EB;
      --text-primary: #111827;
      --text-secondary: #6B7280;
    }

    body {
      font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
      background: linear-gradient(135deg, var(--light-bg) 0%, #E2E8F0 100%);
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      margin: 0;
    }

    .auth-container {
      width: 100%;
      max-width: 480px;
      padding: 2rem;
    }

    .auth-card {
      background: white;
      border-radius: 20px;
      box-shadow: var(--card-shadow);
      overflow: hidden;
      border: 1px solid var(--border-color);
    }

    .auth-header {
      background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
      color: white;
      padding: 2rem;
      text-align: center;
      position: relative;
    }

    .auth-header::before {
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

    .auth-logo {
      width: 60px;
      height: 60px;
      background: rgba(255, 255, 255, 0.2);
      border-radius: 16px;
      display: flex;
      align-items: center;
      justify-content: center;
      margin: 0 auto 1rem;
      font-size: 1.5rem;
      position: relative;
      z-index: 2;
    }

    .auth-title {
      font-size: 1.75rem;
      font-weight: 700;
      margin: 0 0 0.5rem 0;
      position: relative;
      z-index: 2;
    }

    .auth-subtitle {
      font-size: 0.875rem;
      opacity: 0.9;
      margin: 0;
      position: relative;
      z-index: 2;
    }

    .auth-body {
      padding: 2rem;
    }

    .form-group {
      margin-bottom: 1.5rem;
      position: relative;
    }

    .form-label {
      display: flex;
      align-items: center;
      gap: 0.5rem;
      font-weight: 600;
      color: var(--text-primary);
      margin-bottom: 0.75rem;
    }

    .form-control {
      border-radius: 12px;
      border: 2px solid var(--border-color);
      padding: 0.875rem 1rem;
      font-size: 0.875rem;
      transition: all 0.2s ease;
      background: #FAFBFC;
    }

    .form-control:focus {
      border-color: var(--primary-color);
      box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
      background: white;
    }

    .btn-login {
      background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark) 100%);
      border: none;
      border-radius: 12px;
      padding: 0.875rem 2rem;
      font-weight: 600;
      font-size: 0.875rem;
      color: white;
      width: 100%;
      transition: all 0.3s ease;
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 0.5rem;
    }

    .btn-login:hover {
      transform: translateY(-2px);
      box-shadow: 0 8px 25px rgba(59, 130, 246, 0.3);
    }

    .btn-login:disabled {
      opacity: 0.6;
      cursor: not-allowed;
      transform: none;
    }

    .alert {
      border-radius: 12px;
      border: none;
      padding: 1rem;
      margin-bottom: 1.5rem;
      display: flex;
      align-items: center;
      gap: 0.75rem;
    }

    .alert-danger {
      background: rgba(239, 68, 68, 0.1);
      color: #EF4444;
      border-left: 4px solid #EF4444;
    }

    .alert-success {
      background: rgba(16, 185, 129, 0.1);
      color: var(--accent-color);
      border-left: 4px solid var(--accent-color);
    }

    .auth-footer {
      text-align: center;
      padding: 1.5rem 2rem;
      border-top: 1px solid var(--border-color);
      background: #FAFBFC;
    }

    .footer-text {
      font-size: 0.75rem;
      color: var(--text-secondary);
      margin: 0;
    }

    .spinner {
      width: 18px;
      height: 18px;
      border: 2px solid rgba(255, 255, 255, 0.3);
      border-radius: 50%;
      border-top-color: white;
      animation: spin 1s ease-in-out infinite;
    }

    @keyframes spin {
      to { transform: rotate(360deg); }
    }

    /* Responsive */
    @media (max-width: 480px) {
      .auth-container {
        padding: 1rem;
      }
      
      .auth-header {
        padding: 1.5rem;
      }
      
      .auth-body {
        padding: 1.5rem;
      }
      
      .auth-title {
        font-size: 1.5rem;
      }
    }
  </style>
</head>
<body>
  <div class="auth-container">
    <?= $content ?>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
