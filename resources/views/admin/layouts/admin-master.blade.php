<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin Dashboard') - Marron Hotel Management</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700&family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <!-- Admin Main CSS -->
    <link rel="stylesheet" href="{{ asset('admin/css/admin-main.css') }}">
    <!-- Admin Responsive CSS -->
    <link rel="stylesheet" href="{{ asset('admin/css/admin-responsive.css') }}">
    <!-- Admin Components CSS -->
    <link rel="stylesheet" href="{{ asset('admin/css/admin-components.css') }}">
    @yield('styles')
    <style>
        .page-header h1 {
            font-size: 1.7rem;
            font-weight: 700;
            margin-bottom: 0.1rem;
        }
    </style>
</head>

<body>
    <!-- Sidebar Toggle Button -->
    <button class="sidebar-toggle" id="sidebarToggle" title="Toggle Sidebar">
        <i class="fas fa-bars"></i>
    </button>

    <!-- Top Navbar -->
    <nav class="topbar">

        <div class="topbar-search">
            <i class="fas fa-search"></i>
            <input type="text" class="form-control" placeholder="Tìm kiếm...">
        </div>

        <div class="topbar-menu">
            <div class="topbar-divider"></div>

            <!-- Notifications Dropdown -->
            <div class="topbar-item dropdown">
                <a class="topbar-icon" href="#" id="notificationsDropdown" role="button"
                    data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="fas fa-bell"></i>
                    <span class="topbar-badge" id="notificationBadge">{{ $unreadCount > 0 ? $unreadCount : 0 }}</span>
                </a>
                <div class="dropdown-menu dropdown-menu-end animate slideIn" aria-labelledby="notificationsDropdown">
                    <div class="dropdown-header d-flex justify-content-between align-items-center py-2">
                        <h6 class="m-0">
                            <i class="fas fa-bell me-2"></i>Thông báo
                        </h6>
                    </div>
                    <div id="notificationsList">
                        @if($unreadCount > 0)
                            @foreach($unreadNotifications as $notification)
                                <div class="dropdown-item notification-item d-flex align-items-start justify-content-between gap-2">
                                    <a href="{{ route('admin.notifications.show', $notification->id) }}" class="flex-grow-1 text-decoration-none text-dark">
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="icon-circle bg-{{ $notification->color }}">
                                                <i class="{{ $notification->display_icon }} text-white"></i>
                                            </div>
                                            <div>
                                                <div class="fw-bold small text-truncate" title="{{ $notification->title }}">{{ $notification->title }}</div>
                                                <div class="small text-muted text-truncate mb-1" title="{{ $notification->message }}">{{ $notification->message }}</div>
                                                <div class="small text-gray-500"><i class="fas fa-clock me-1"></i>{{ $notification->time_ago }}</div>
                                            </div>
                                        </div>
                                    </a>
                                    <form method="POST" action="{{ route('admin.notifications.delete') }}" style="margin:0;">
                                        @csrf
                                        @method('DELETE')
                                        <input type="hidden" name="notification_id" value="{{ $notification->id }}">
                                        <button type="submit" class="btn btn-link btn-sm text-danger p-0 ms-2" title="Xóa" style="font-size:1rem;"><i class="fas fa-trash"></i></button>
                                    </form>
                                </div>
                            @endforeach
                        @else
                            <div class="dropdown-item text-center small text-gray-500 py-3">
                                <i class="fas fa-check-circle text-success me-2"></i> Không có thông báo mới
                            </div>
                        @endif
                    </div>
                    <div class="dropdown-divider"></div>
                    <a class="dropdown-item text-center small text-gray-500 py-2" href="{{ route('admin.notifications.index') }}">
                        <i class="fas fa-list me-1"></i>Xem tất cả thông báo
                    </a>
                </div>
            </div>

            <!-- Messages Dropdown -->
            <div class="topbar-item dropdown">
                <a class="topbar-icon" href="#" id="messagesDropdown" role="button" data-bs-toggle="dropdown"
                    aria-expanded="false">
                    <i class="fas fa-envelope"></i>
                    <span class="topbar-badge">
                        {{ \App\Models\SupportTicket::with('messages')->whereHas('messages')->count() }}
                    </span>
                </a>
                <div class="dropdown-menu dropdown-menu-end animate slideIn" aria-labelledby="messagesDropdown">
                    <h6 class="dropdown-header">Tin nhắn hỗ trợ</h6>
                    @php
                        $tickets = \App\Models\SupportTicket::with([
                            'user',
                            'messages' => function ($q) {
                                $q->latest();
                            },
                        ])
                            ->whereHas('messages')
                            ->latest('updated_at')
                            ->take(5)
                            ->get();
                    @endphp
                    @forelse($tickets as $ticket)
                        <a class="dropdown-item" href="{{ route('admin.support.showTicket', $ticket->id) }}">
                            <div class="dropdown-item-message">
                                <img src="https://ui-avatars.com/api/?name={{ urlencode($ticket->user->name ?? 'Khach') }}&background=random"
                                    alt="{{ $ticket->user->name ?? 'Khách' }}">
                                <div class="dropdown-item-message-content">
                                    <div class="dropdown-item-message-title">{{ $ticket->user->name ?? 'Khách' }}</div>
                                    <p class="dropdown-item-message-text">
                                        {{ optional($ticket->messages->first())->message ?? '...' }}</p>
                                    <div class="dropdown-item-message-time">
                                        {{ optional($ticket->messages->first())->created_at ? optional($ticket->messages->first())->created_at->diffForHumans() : '' }}
                                    </div>
                                </div>
                            </div>
                        </a>
                    @empty
                        <div class="dropdown-item text-center small text-gray-500">Không có tin nhắn mới</div>
                    @endforelse
                    <a class="dropdown-item text-center small text-gray-500"
                        href="{{ route('admin.support.index') }}">Xem tất cả tin nhắn</a>
                </div>
            </div>

            <div class="topbar-divider"></div>

            <!-- User Information Dropdown -->
            <div class="topbar-item dropdown">
                <a class="user-profile" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown"
                    aria-expanded="false">
                    <img src="https://ui-avatars.com/api/?name=Admin&background=random" alt="Admin">
                    <div class="user-profile-info d-none d-md-block">
                        <div class="user-profile-name">Admin</div>
                        <div class="user-profile-role">Quản trị viên</div>
                    </div>
                </a>
                <div class="dropdown-menu dropdown-menu-end animate slideIn" aria-labelledby="userDropdown">
                    <h6 class="dropdown-header">Tài khoản</h6>
                    <a class="dropdown-item" href="#">
                        <i class="fas fa-user"></i> Hồ sơ
                    </a>
                    <a class="dropdown-item" href="#">
                        <i class="fas fa-cogs"></i> Cài đặt
                    </a>
                    <a class="dropdown-item" href="#">
                        <i class="fas fa-list"></i> Hoạt động
                    </a>
                    <div class="dropdown-divider"></div>
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="dropdown-item">
                            <i class="fas fa-sign-out-alt"></i> Đăng xuất
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </nav>

    <!-- Sidebar -->
    <div class="col-md-3 col-lg-2 d-md-block sidebar collapse">
        <div class="position-sticky pt-3">
            <div class="navbar-brand text-center py-4">
                <i class="fas fa-hotel me-2"></i> Marron Hotel
            </div>
            <hr class="mx-3 opacity-25">
            <ul class="nav flex-column px-3 mt-4">
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}"
                        href="{{ route('admin.dashboard') }}">
                        <i class="fas fa-tachometer-alt"></i> Dashboard
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.bookings.*') ? 'active' : '' }}"
                        href="{{ route('admin.bookings.index') }}">
                        <i class="fas fa-calendar-check"></i> Đặt phòng
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link d-flex justify-content-between align-items-center {{ request()->routeIs('admin.rooms.*') || request()->routeIs('admin.room-type-services.*') || request()->routeIs('admin.service-categories.*') ? '' : 'collapsed' }}"
                        data-bs-toggle="collapse" href="#submenuRoom" role="button"
                        aria-expanded="{{ request()->routeIs('admin.rooms.*') || request()->routeIs('admin.room-type-services.*') || request()->routeIs('admin.service-categories.*') ? 'true' : 'false' }}"
                        aria-controls="submenuRoom">
                        <span><i class="fas fa-bed"></i> Phòng</span>
                        <i
                            class="fas fa-chevron-down small {{ request()->routeIs('admin.rooms.*') || request()->routeIs('admin.room-type-services.*') || request()->routeIs('admin.service-categories.*') ? 'rotate-180' : '' }}"></i>
                    </a>
                    <div class="collapse {{ request()->routeIs('admin.rooms.*') || request()->routeIs('admin.room-type-services.*') || request()->routeIs('admin.service-categories.*') ? 'show' : '' }}"
                        id="submenuRoom">
                        <ul class="nav flex-column ms-3">
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('admin.rooms.index') ? 'active' : '' }}"
                                    href="{{ route('admin.rooms.index') }}">
                                    Danh sách phòng
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('admin.room-type-services.index') ? 'active' : '' }}"
                                    href="{{ route('admin.room-type-services.index') }}">
                                    Dịch vụ loại phòng
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.promotions.*') ? 'active' : '' }}" href="{{ route('admin.promotions.index') }}">
                        <i class="fas fa-percentage"></i> Khuyến mại
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}"
                        href="{{ route('admin.users.index') }}">
                        <i class="fas fa-users"></i> Người dùng
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.room-type-reviews.*') ? 'active' : '' }}"
                        href="{{ route('admin.room-type-reviews.index') }}">
                        <i class="fas fa-star"></i> Đánh giá
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.bookings.report') ? 'active' : '' }}"
                        href="{{ route('admin.bookings.report') }}">
                        <i class="fas fa-chart-bar"></i> Báo cáo
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.notifications.*') ? 'active' : '' }}" href="{{ route('admin.notifications.index') }}">
                        <i class="fas fa-bell"></i> Thông báo
                        <span class="badge bg-danger ms-2" id="sidebarNotificationBadge">0</span>
                    </a>
                </li>
                <li class="nav-item mt-5">
                    <a class="nav-link" href="{{ route('index') }}" target="_blank">
                        <i class="fas fa-external-link-alt"></i> Xem trang chủ
                    </a>
                </li>
            </ul>
        </div>
    </div>

    <!-- Main content -->
    <main class="main-content">
                <div class="page-header fade-in">
                    <h1>@yield('header', 'Dashboard')</h1>
                </div>

                <!-- Hiển thị thông báo -->
                <div class="alert-container">
                    @if (session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"
                                aria-label="Close"></button>
                        </div>
                    @endif

                    @if (session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="fas fa-exclamation-circle me-2"></i> {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"
                                aria-label="Close"></button>
                        </div>
                    @endif

                    @if (session('warning'))
                        <div class="alert alert-warning alert-dismissible fade show" role="alert">
                            <i class="fas fa-exclamation-triangle me-2"></i> {{ session('warning') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"
                                aria-label="Close"></button>
                        </div>
                    @endif

                    @if (session('info'))
                        <div class="alert alert-info alert-dismissible fade show" role="alert">
                            <i class="fas fa-info-circle me-2"></i> {{ session('info') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"
                                aria-label="Close"></button>
                        </div>
                    @endif
                </div>

                <div class="content-wrapper fade-in">
                    @yield('content')
                </div>
            </main>

    <!-- Toast Container -->
    <div class="toast-container position-fixed bottom-0 end-0 p-3" style="z-index: 1050;">
        <!-- Toast notifications will be inserted here -->
    </div>

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Admin Main JavaScript -->
    <script src="{{ asset('admin/js/admin-main.js') }}"></script>
    
    <!-- Debug script -->
    <script>
        console.log('Layout loaded');
        $(document).ready(function() {
            console.log('Document ready in layout');
            console.log('Notification elements:', {
                badge: $('#notificationBadge').length,
                list: $('#notificationsList').length,
                markAllBtn: $('#markAllReadBtn').length,
                sidebarBadge: $('#sidebarNotificationBadge').length,
            });
            
            // Test API call
            $.ajax({
                url: '/admin/api/notifications/unread',
                method: 'GET',
                dataType: 'json',
                success: function(response) {
                    console.log('API test successful:', response);
                    if (response.success) {
                        console.log('Found', response.count, 'unread notifications');
                    }
                },
                error: function(xhr, status, error) {
                    console.error('API test failed:', {xhr, status, error});
                }
            });
            
            // Test notification manager
            setTimeout(function() {
                if (window.notificationManager) {
                    console.log('NotificationManager is available');
                } else {
                    console.error('NotificationManager not found!');
                }
            }, 1000);
        });
    </script>
    
    @yield('scripts')
</body>

</html>
