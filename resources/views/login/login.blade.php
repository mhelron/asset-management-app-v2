<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      background-color: #f8f9fa;
      min-height: 100vh;
      color: #444;
    }
    .main-wrapper {
      display: flex;
      align-items: center;
      justify-content: center;
      min-height: calc(100vh - 56px); /* subtract navbar height */
    }
    .card {
      background-color: #fff;
      border: none;
      border-radius: 1rem;
      box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
    }
    .form-control {
      border-radius: 0.5rem;
      color: #444;
      border: 1px solid #ccc;
    }
    .form-control:focus {
      border-color: #444;
      box-shadow: none;
    }
    .btn-primary {
      background-color: #444;
      border: none;
      border-radius: 0.5rem;
    }
    .btn-primary:hover {
      background-color: #333;
    }
    a {
      color: #444;
      text-decoration: underline;
    }
    a:hover {
      color: #222;
    }
  </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
  <div class="container">
    <a class="navbar-brand" href="#">Asset Inventory Management</a>
  </div>
</nav>

<div class="main-wrapper">
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-md-5 col-lg-4">
        <div class="card p-4">
          <div class="card-body">
            <h3 class="card-title text-center mb-4">Login</h3>

            @if($error = session()->pull('error'))
                <div class="alert alert-danger">
                    {{ $error }}
                </div>
            @endif


            <form method="POST" action="{{ route('login') }}">
              @csrf

              <div class="mb-3">
                <label for="email" class="form-label">Email Address</label>
                <input type="email" name="email" id="email" class="form-control" value="{{ old('email') }}" required autofocus>
              </div>

              <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input type="password" name="password" id="password" class="form-control" required>
              </div>

              <div class="form-check mb-3">
                <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                <label class="form-check-label" for="remember">Remember Me</label>
              </div>

              <div class="d-grid mb-3">
                <button type="submit" class="btn btn-primary">Login</button>
              </div>
              <!--
              <div class="text-center">
                  <small>Forgot your password? 
                      <a href="#" onclick="alert('This feature is not working yet. Di pa sya nagana mag hintay ka muna'); return false;">Click Here</a>
                  </small>
              </div> -->
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

</body>
</html>
