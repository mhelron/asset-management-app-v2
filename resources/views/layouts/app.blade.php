<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">

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
    
    /* Notification Dropdown Styles */
    .dropdown-menu {
        border: none;
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
    }
    
    .notifications-container {
        max-height: 300px;
        overflow-y: auto;
    }
    
    .notifications-container::-webkit-scrollbar {
        width: 5px;
    }
    
    .notifications-container::-webkit-scrollbar-thumb {
        background: #ccc;
        border-radius: 5px;
    }
    
    .dropdown-item.unread {
        background-color: rgba(13, 110, 253, 0.05);
    }
    
    .dropdown-item:active {
        background-color: #212529 !important;
        color: white !important;
    }
    
    .notification-icon {
        width: 36px;
        height: 36px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    /* Notification animation */
    @keyframes notification-pulse {
        0% { transform: scale(1); }
        50% { transform: scale(1.1); }
        100% { transform: scale(1); }
    }
    
    .notification-badge-new {
        animation: notification-pulse 1s infinite;
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

    <!-- Notification Dropdown Script -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Update notifications function
            const updateNotifications = function() {
                fetch('/notifications/get-latest', {
                    method: 'GET',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    // Get elements
                    const bell = document.querySelector('#notificationDropdown');
                    const notificationContainer = document.querySelector('.notifications-container');
                    const unreadCount = data.unreadCount;
                    let notificationBadge = document.querySelector('#notificationDropdown .badge');
                    
                    // Update the badge count
                    if (unreadCount > 0) {
                        if (!notificationBadge) {
                            // Create badge if it doesn't exist
                            const badge = document.createElement('span');
                            badge.className = 'position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger';
                            badge.innerHTML = unreadCount > 99 ? '99+' : unreadCount;
                            badge.innerHTML += '<span class="visually-hidden">unread notifications</span>';
                            bell.appendChild(badge);
                        } else {
                            // Update existing badge
                            notificationBadge.innerHTML = unreadCount > 99 ? '99+' : unreadCount;
                            notificationBadge.innerHTML += '<span class="visually-hidden">unread notifications</span>';
                        }
                        
                        // Also update sidebar badge if it exists
                        const sidebarBadge = document.querySelector('.sidebar-link .badge');
                        if (sidebarBadge) {
                            sidebarBadge.textContent = unreadCount;
                        }
                    } else if (notificationBadge) {
                        // Remove badge if no unread notifications
                        notificationBadge.remove();
                    }
                    
                    // Update notification container if it exists
                    if (notificationContainer && data.notifications.length > 0) {
                        let notificationsHtml = '';
                        
                        data.notifications.forEach(notification => {
                            notificationsHtml += `
                                <a href="${notification.route}" 
                                   class="dropdown-item d-flex border-bottom py-2 ${notification.read ? '' : 'unread'}"
                                   data-notification-id="${notification.id}">
                                    <div class="me-3">
                                        <div class="${notification.bgClass} text-white rounded-circle p-2 d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                                            <i class="bi ${notification.iconClass}"></i>
                                        </div>
                                    </div>
                                    <div class="flex-grow-1">
                                        <p class="mb-0 ${notification.read ? 'text-muted' : 'fw-bold'} text-truncate" style="max-width: 220px;">
                                            ${notification.message}
                                        </p>
                                        <small class="text-muted">${notification.time}</small>
                                    </div>
                                </a>
                            `;
                        });
                        
                        notificationContainer.innerHTML = notificationsHtml;
                    } else if (notificationContainer) {
                        // Show "No notifications" message
                        notificationContainer.innerHTML = `
                            <div class="dropdown-item text-center py-3">
                                <span class="text-muted">No notifications</span>
                            </div>
                        `;
                    }
                })
                .catch(error => {
                    console.error('Error fetching notifications:', error);
                });
            };
            
            // Initialize dropdown
            var dropdownElementList = [].slice.call(document.querySelectorAll('.dropdown-toggle'));
            var dropdownList = dropdownElementList.map(function (dropdownToggleEl) {
                return new bootstrap.Dropdown(dropdownToggleEl);
            });
            
            // Handle notification clicks
            document.addEventListener('click', function(event) {
                const target = event.target.closest('.notifications-container .dropdown-item');
                if (target) {
                    const notificationId = target.getAttribute('data-notification-id');
                    if (notificationId) {
                        fetch(`/notifications/mark-read/${notificationId}`, {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                                'Content-Type': 'application/json',
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify({})
                        });
                    }
                }
            });
            
            // Initial update
            updateNotifications();
            
            // Set interval to refresh notifications every 15 seconds
            setInterval(updateNotifications, 15000);
        });
    </script>

    <!-- Navbar Script -->
    @vite('resources/js/navbar.js')
</html>