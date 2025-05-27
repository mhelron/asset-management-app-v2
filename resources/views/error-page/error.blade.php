<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Access Denied</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <style>
    body {
      background-color: #f8f9fa;
      height: 100vh;
      display: flex;
      justify-content: center;
      align-items: center;
    }
    .access-denied-card {
      text-align: center;
      padding: 2rem;
      border-radius: 1rem;
      box-shadow: 0 0.5rem 1rem rgba(0,0,0,0.1);
      background-color: #ffffff;
      color: #212529;
    }
    .lock-icon {
      font-size: 4rem;
      color: #212529;
    }
  </style>
  <script>
    function goBack() {
      window.history.back();
    }
  </script>
</head>
<body>
  <div class="access-denied-card">
    <div class="lock-icon mb-3">
      <i class="fas fa-lock"></i>
    </div>
    <h2>Access Denied</h2>
    <p>You do not have permission to view this page.</p>
    <button onclick="goBack()" class="btn btn-dark mt-3">← Go Back</button>
  </div>
</body>
</html>