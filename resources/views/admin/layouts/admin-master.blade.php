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
    <!-- Custom CSS -->
    <style>
        :root {
            --primary-color: #8b7d6b;
            --secondary-color: #2c3e50;
            --accent-color: #c19b76;
            --light-color: #f8f9fa;
            --dark-color: #212529;
            --success-color: #28a745;
            --info-color: #17a2b8;
            --warning-color: #ffc107;
            --danger-color: #dc3545;
        }
        
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f5f5f5;
        }
        
        h1, h2, h3, h4, h5, h6, .navbar-brand {
            font-family: 'Montserrat', sans-serif;
            font-weight: 600;
        }
        
        .sidebar {
            min-height: 100vh;
            background: linear-gradient(to bottom, var(--secondary-color), #1a252f);
            color: #fff;
            box-shadow: 2px 0 10px rgba(0, 0, 0, 0.1);
            position: fixed;
            top: 0;
            bottom: 0;
            left: 0;
            z-index: 100;
            padding-top: 0;
        }
        
        .sidebar .navbar-brand {
            color: var(--accent-color);
            font-size: 1.8rem;
            font-weight: 700;
            letter-spacing: 1px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            padding-top: 1rem;
            padding-bottom: 1rem;
        }
        
        .sidebar .nav-link {
            color: rgba(255, 255, 255, 0.7);
            padding: 12px 15px;
            margin: 4px 0;
            border-radius: 5px;
            transition: all 0.3s ease;
        }
        
        .sidebar .nav-link:hover {
            color: #fff;
            background-color: rgba(255, 255, 255, 0.1);
            transform: translateX(5px);
        }
        
        .sidebar .nav-link.active {
            color: #fff;
            background: linear-gradient(to right, var(--accent-color), rgba(155, 104, 52, 0.7));
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
        }
        
        .sidebar .nav-link i {
            width: 24px;
            text-align: center;
            margin-right: 8px;
        }
        
        .main-content {
            padding: 25px;
            transition: all 0.3s;
            margin-left: 16.66667%;
            margin-top: 60px;
        }
        
        .page-header {
            background: linear-gradient(to right, var(--secondary-color), var(--primary-color));
            color: white;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 25px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            position: relative;
            overflow: hidden;
        }
        
        .page-header::after {
            content: '';
            position: absolute;
            top: 0;
            right: 0;
            width: 150px;
            height: 100%;
            background: linear-gradient(to right, transparent, rgba(255, 255, 255, 0.1));
            transform: skewX(-30deg);
        }
        
        .page-header h1 {
            font-weight: 700;
            margin: 0;
            font-size: 2rem;
        }
        
        .card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            overflow: hidden;
            margin-bottom: 25px;
        }
        
        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
        }
        
        .card-header {
            background: linear-gradient(to right, var(--accent-color), rgba(193, 155, 118, 0.7));
            color: white;
            font-weight: 600;
            border: none;
            padding: 15px 20px;
        }
        
        .card-header h6 {
            margin: 0;
            font-size: 1.1rem;
        }
        
        .card-body {
            padding: 20px;
        }
        
        .btn {
            border-radius: 5px;
            padding: 8px 16px;
            font-weight: 500;
            transition: all 0.3s ease;
        }
        
        .btn-primary {
            background-color: var(--accent-color);
            border-color: var(--accent-color);
        }
        
        .btn-primary:hover {
            background-color: #a67c5b;
            border-color: #a67c5b;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        
        .btn-secondary {
            background-color: #6c757d;
            border-color: #6c757d;
        }
        
        .btn-secondary:hover {
            background-color: #5a6268;
            border-color: #545b62;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        
        .table {
            border-collapse: separate;
            border-spacing: 0;
        }
        
        .table th {
            background-color: #f8f9fa;
            border-top: none;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.8rem;
            letter-spacing: 0.5px;
        }
        
        .table td, .table th {
            padding: 15px;
            vertical-align: middle;
        }
        
        .table-striped tbody tr:nth-of-type(odd) {
            background-color: rgba(0, 0, 0, 0.02);
        }
        
        .badge {
            padding: 7px 12px;
            font-weight: 500;
            border-radius: 30px;
        }
        
        .form-control, .form-select {
            border-radius: 5px;
            padding: 10px 15px;
            border: 1px solid #ced4da;
            transition: all 0.3s ease;
        }
        
        .form-control:focus, .form-select:focus {
            border-color: var(--accent-color);
            box-shadow: 0 0 0 0.25rem rgba(193, 155, 118, 0.25);
        }
        
        .alert {
            border-radius: 10px;
            border: none;
            padding: 15px 20px;
            margin-bottom: 20px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.05);
        }
        
        .alert-success {
            background-color: #d4edda;
            color: #155724;
        }
        
        .alert-danger {
            background-color: #f8d7da;
            color: #721c24;
        }
        
        .alert-warning {
            background-color: #fff3cd;
            color: #856404;
        }
        
        .alert-info {
            background-color: #d1ecf1;
            color: #0c5460;
        }
        
        /* Navbar styles */
        .topbar {
            position: fixed;
            top: 0;
            left: 16.66667%;
            right: 0;
            height: 60px;
            background-color: white;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            z-index: 1000;
            display: flex;
            align-items: center;
            padding: 0 20px;
        }
        
        .topbar-search {
            position: relative;
            width: 350px;
        }
        
        .topbar-search input {
            background-color: #f5f5f5;
            border: none;
            border-radius: 30px;
            padding: 8px 15px 8px 40px;
            width: 100%;
            transition: all 0.3s;
        }
        
        .topbar-search input:focus {
            background-color: #fff;
            box-shadow: 0 0 10px rgba(193, 155, 118, 0.2);
        }
        
        .topbar-search i {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #777;
        }
        
        .topbar-divider {
            width: 1px;
            height: 30px;
            background-color: #e3e6f0;
            margin: 0 15px;
        }
        
        .topbar-menu {
            display: flex;
            align-items: center;
            margin-left: auto;
        }
        
        .topbar-item {
            position: relative;
            margin-left: 15px;
        }
        
        .topbar-icon {
            font-size: 1.2rem;
            color: #6c757d;
            cursor: pointer;
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            transition: all 0.3s;
        }
        
        .topbar-icon:hover {
            background-color: #f8f9fa;
            color: var(--accent-color);
        }
        
        .topbar-badge {
            position: absolute;
            top: -5px;
            right: -5px;
            font-size: 0.65rem;
            width: 18px;
            height: 18px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: var(--danger-color);
            color: white;
        }
        
        .dropdown-menu {
            border: none;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            padding: 10px 0;
            min-width: 16rem;
        }
        
        .dropdown-item {
            padding: 10px 20px;
            color: #212529;
            font-size: 0.85rem;
            transition: all 0.2s;
        }
        
        .dropdown-item:hover {
            background-color: #f8f9fa;
            color: var(--accent-color);
        }
        
        .dropdown-item i {
            width: 20px;
            text-align: center;
            margin-right: 10px;
            color: #777;
        }
        
        .dropdown-header {
            color: var(--accent-color);
            font-weight: 600;
            font-size: 0.7rem;
            padding: 10px 20px 5px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        .dropdown-divider {
            margin: 5px 0;
            border-top: 1px solid #e9ecef;
        }
        
        .dropdown-item-message {
            display: flex;
            align-items: center;
            width: 300px;
        }
        
        .dropdown-item-message img {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            margin-right: 15px;
        }
        
        .dropdown-item-message-content {
            flex: 1;
        }
        
        .dropdown-item-message-title {
            font-weight: 600;
            font-size: 0.85rem;
            margin-bottom: 2px;
        }
        
        .dropdown-item-message-text {
            font-size: 0.8rem;
            color: #6c757d;
            margin-bottom: 0;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            max-width: 210px;
        }
        
        .dropdown-item-message-time {
            font-size: 0.7rem;
            color: #999;
        }
        
        .user-profile {
            display: flex;
            align-items: center;
            cursor: pointer;
            padding: 5px 10px;
            border-radius: 30px;
            transition: all 0.3s;
        }
        
        .user-profile:hover {
            background-color: #f8f9fa;
        }
        
        .user-profile img {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            object-fit: cover;
            margin-right: 10px;
        }
        
        .user-profile-info {
            line-height: 1.2;
        }
        
        .user-profile-name {
            font-weight: 600;
            font-size: 0.9rem;
            color: #333;
        }
        
        .user-profile-role {
            font-size: 0.7rem;
            color: #777;
        }
        
        /* Animations */
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .fade-in {
            animation: fadeIn 0.5s ease forwards;
        }
        
        /* Custom Scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
        }
        
        ::-webkit-scrollbar-track {
            background: #f1f1f1;
        }
        
        ::-webkit-scrollbar-thumb {
            background: var(--accent-color);
            border-radius: 10px;
        }
        
        ::-webkit-scrollbar-thumb:hover {
            background: #a67c5b;
        }

        /* Responsive adjustments */
        @media (max-width: 992px) {
            .sidebar {
                transform: translateX(-100%);
                transition: transform 0.3s ease;
            }
            
            .sidebar.show {
                transform: translateX(0);
            }
            
            .main-content {
                margin-left: 0;
                width: 100%;
            }
            
            .topbar {
                left: 0;
            }
            
            .topbar-toggle {
                display: block !important;
            }
        }
    </style>
    @yield('styles')
</head>
<body>
    <!-- Top Navbar -->
    <nav class="topbar">
        <button class="btn topbar-toggle d-lg-none mr-3" id="sidebarToggle" style="display: none;">
            <i class="fas fa-bars"></i>
        </button>
        
        <div class="topbar-search">
            <i class="fas fa-search"></i>
            <input type="text" class="form-control" placeholder="Tìm kiếm...">
        </div>
        
        <div class="topbar-menu">
            <div class="topbar-divider"></div>
            
            <!-- Notifications Dropdown -->
            <div class="topbar-item dropdown">
                <a class="topbar-icon" href="#" id="notificationsDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="fas fa-bell"></i>
                    <span class="topbar-badge">3</span>
                </a>
                <div class="dropdown-menu dropdown-menu-end animate slideIn" aria-labelledby="notificationsDropdown">
                    <h6 class="dropdown-header">Thông báo</h6>
                    <a class="dropdown-item" href="#">
                        <div class="d-flex align-items-center">
                            <div class="icon-circle bg-primary">
                                <i class="fas fa-file-alt text-white"></i>
                            </div>
                            <div class="ms-3">
                                <div class="small text-gray-500">15 Tháng 7, 2023</div>
                                <span>Có đặt phòng mới cần xác nhận!</span>
                            </div>
                        </div>
                    </a>
                    <a class="dropdown-item" href="#">
                        <div class="d-flex align-items-center">
                            <div class="icon-circle bg-success">
                                <i class="fas fa-check text-white"></i>
                            </div>
                            <div class="ms-3">
                                <div class="small text-gray-500">14 Tháng 7, 2023</div>
                                <span>Thanh toán đã được xác nhận</span>
                            </div>
                        </div>
                    </a>
                    <a class="dropdown-item" href="#">
                        <div class="d-flex align-items-center">
                            <div class="icon-circle bg-warning">
                                <i class="fas fa-exclamation-triangle text-white"></i>
                            </div>
                            <div class="ms-3">
                                <div class="small text-gray-500">13 Tháng 7, 2023</div>
                                <span>Cảnh báo: Khách hàng đã hủy đặt phòng</span>
                            </div>
                        </div>
                    </a>
                    <a class="dropdown-item text-center small text-gray-500" href="#">Xem tất cả thông báo</a>
                </div>
            </div>
            
            <!-- Messages Dropdown -->
            <div class="topbar-item dropdown">
                <a class="topbar-icon" href="#" id="messagesDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="fas fa-envelope"></i>
                    <span class="topbar-badge">2</span>
                </a>
                <div class="dropdown-menu dropdown-menu-end animate slideIn" aria-labelledby="messagesDropdown">
                    <h6 class="dropdown-header">Tin nhắn</h6>
                    <a class="dropdown-item" href="#">
                        <div class="dropdown-item-message">
                            <img src="https://ui-avatars.com/api/?name=T+H&background=random" alt="Trần Hương">
                            <div class="dropdown-item-message-content">
                                <div class="dropdown-item-message-title">Trần Hương</div>
                                <p class="dropdown-item-message-text">Tôi cần đổi phòng sang hướng biển...</p>
                                <div class="dropdown-item-message-time">15 phút trước</div>
                            </div>
                        </div>
                    </a>
                    <a class="dropdown-item" href="#">
                        <div class="dropdown-item-message">
                            <img src="https://ui-avatars.com/api/?name=N+T&background=random" alt="Nguyễn Tuấn">
                            <div class="dropdown-item-message-content">
                                <div class="dropdown-item-message-title">Nguyễn Tuấn</div>
                                <p class="dropdown-item-message-text">Xin hỏi thông tin về gói ưu đãi...</p>
                                <div class="dropdown-item-message-time">1 giờ trước</div>
                            </div>
                        </div>
                    </a>
                    <a class="dropdown-item text-center small text-gray-500" href="#">Xem tất cả tin nhắn</a>
                </div>
            </div>
            
            <div class="topbar-divider"></div>
            
            <!-- User Information Dropdown -->
            <div class="topbar-item dropdown">
                <a class="user-profile" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
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

    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-3 col-lg-2 d-md-block sidebar collapse">
                <div class="position-sticky pt-3">
                    <div class="navbar-brand text-center py-4">
                        <i class="fas fa-hotel me-2"></i> Marron Hotel
                    </div>
                    <hr class="mx-3 opacity-25">
                    <ul class="nav flex-column px-3 mt-4">
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}">
                                <i class="fas fa-tachometer-alt"></i> Dashboard
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.bookings.*') ? 'active' : '' }}" href="{{ route('admin.bookings.index') }}">
                                <i class="fas fa-calendar-check"></i> Đặt phòng
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.rooms.*') ? 'active' : '' }}" href="{{ route('admin.rooms.index')}}">
                                <i class="fas fa-bed"></i> Phòng
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.promotions.*') ? 'active' : '' }}" href="{{ route('admin.promotions.index') }}">
                                <i class="fas fa-percentage"></i> Khuyến mại
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#">
                                <i class="fas fa-users"></i> Người dùng
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.bookings.report') ? 'active' : '' }}" href="{{ route('admin.bookings.report') }}">
                                <i class="fas fa-chart-bar"></i> Báo cáo
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
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 main-content">
                <div class="page-header fade-in">
                    <h1>@yield('header', 'Dashboard')</h1>
                </div>

                <!-- Hiển thị thông báo -->
                <div class="alert-container">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="fas fa-exclamation-circle me-2"></i> {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    @if(session('warning'))
                        <div class="alert alert-warning alert-dismissible fade show" role="alert">
                            <i class="fas fa-exclamation-triangle me-2"></i> {{ session('warning') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    @if(session('info'))
                        <div class="alert alert-info alert-dismissible fade show" role="alert">
                            <i class="fas fa-info-circle me-2"></i> {{ session('info') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif
                </div>

                <div class="content-wrapper fade-in">
                    @yield('content')
                </div>
            </main>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        // Animation for cards
        document.addEventListener('DOMContentLoaded', function() {
            const cards = document.querySelectorAll('.card');
            cards.forEach((card, index) => {
                card.style.opacity = '0';
                card.style.transform = 'translateY(20px)';
                setTimeout(() => {
                    card.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
                    card.style.opacity = '1';
                    card.style.transform = 'translateY(0)';
                }, 100 * index);
            });
            
            // Hover effect for buttons
            const buttons = document.querySelectorAll('.btn');
            buttons.forEach(button => {
                button.addEventListener('mouseenter', function() {
                    this.style.transform = 'translateY(-2px)';
                    this.style.boxShadow = '0 4px 8px rgba(0, 0, 0, 0.1)';
                });
                
                button.addEventListener('mouseleave', function() {
                    this.style.transform = '';
                    this.style.boxShadow = '';
                });
            });
            
            // Toggle sidebar on mobile
            const sidebarToggle = document.getElementById('sidebarToggle');
            if (sidebarToggle) {
                sidebarToggle.addEventListener('click', function() {
                    document.querySelector('.sidebar').classList.toggle('show');
                });
            }
            
            // Close dropdown when clicking outside
            document.addEventListener('click', function(e) {
                const dropdowns = document.querySelectorAll('.dropdown-menu.show');
                dropdowns.forEach(dropdown => {
                    if (!dropdown.contains(e.target) && !dropdown.previousElementSibling.contains(e.target)) {
                        dropdown.classList.remove('show');
                    }
                });
            });
        });
    </script>
    @yield('scripts')
</body>
</html>
