<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <title>Asset Management System</title>

    <!-- Custom Toast Styles -->
    <style>
    .custom-toast-size {
        width: 350px;
        max-width: 100%;
        padding: 15px;
        font-size: 16px;
        border-radius: 8px;
    }

    .toast-body {
        font-size: 16px;
    }

    .toast-container {
        z-index: 1055;
    }

    .btn-close {
        font-size: 20px;
    }
    
    /* Responsive fixes */
    @media (max-width: 767px) {
        .container-fluid {
            padding-left: 10px;
            padding-right: 10px;
        }
        .navbar-brand {
            font-size: 0.9rem;
            max-width: 60%;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .main {
            padding: 10px !important;
        }
        .content-wrapper {
            padding: 0;
        }
        /* Global header and breadcrumb styles */
        .content-header .row {
            text-align: left;
        }
        .breadcrumb {
            justify-content: flex-start;
            float: none !important;
            padding-left: 0;
            margin-top: 0.5rem;
        }
        /* Action buttons layout */
        .action-buttons, 
        .d-flex.justify-content-between.align-items-center.mb-3 {
            display: flex !important;
            flex-direction: row !important;
            justify-content: space-between !important;
            align-items: center !important;
            width: 100%;
        }
    }
    
    /* Fix for user info in navbar */
    @media (max-width: 480px) {
        .navbar .text-light {
            display: none;
        }
    }
    </style>

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <!-- Boxicons Icons -->
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>

    <!-- Global CSS -->
    @vite(['resources/css/global.css', 'resources/css/navbar.css'])

</head>
    <body class="hold-transition sidebar-mini">

        <div class="wrapper">

        <!-- Navbar -->
        @include('layouts.inc.navbar')

        <div class="main p-3">
            <div class="content-wrapper">
                <!-- Pages -->
                 @yield('content')
            </div>
        </div>
    </body>

    <!-- Bootstrap 5 -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>

    <!-- Custom Toast -->
    @if (session('status'))
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                var toastEl = document.querySelector('.toast');
                var toast = new bootstrap.Toast(toastEl);
                toast.show();
            });
        </script>
    @endif

    <!-- Navbar Script -->
    @vite('resources/js/navbar.js')
</html>