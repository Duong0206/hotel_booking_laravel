@extends('client.layouts.master')

@section('title', 'Quản Lý Tài Khoản')

@section('content')
<style>
    .profile-sidebar {
        background: linear-gradient(135deg, #fff 60%, #f7f3ec 100%);
        border-radius: 16px;
        box-shadow: 0 4px 24px rgba(141,112,59,0.08);
        padding: 32px 0 32px 0;
        min-height: 500px;
        transition: box-shadow 0.3s;
    }
    .profile-sidebar .nav-link {
        color: #8d703b;
        font-weight: 600;
        border-radius: 0 30px 30px 0;
        margin-bottom: 12px;
        font-size: 18px;
        padding: 12px 28px 12px 32px;
        display: flex;
        align-items: center;
        transition: background 0.2s, color 0.2s, transform 0.2s;
    }
    .profile-sidebar .nav-link i {
        margin-right: 12px;
        font-size: 20px;
        transition: color 0.2s;
    }
    .profile-sidebar .nav-link.active, .profile-sidebar .nav-link:hover {
        background: #8d703b;
        color: #fff;
        transform: translateX(6px) scale(1.04);
        box-shadow: 0 2px 8px rgba(141,112,59,0.10);
    }
    .profile-sidebar .nav-link.active i, .profile-sidebar .nav-link:hover i {
        color: #78d5ef;
    }
    .profile-avatar {
        width: 90px;
        height: 90px;
        border-radius: 50%;
        object-fit: cover;
        border: 4px solid #8d703b;
        box-shadow: 0 2px 12px rgba(141,112,59,0.12);
        margin-bottom: 12px;
        transition: transform 0.2s, box-shadow 0.2s;
    }
    .profile-avatar:hover {
        transform: scale(1.07);
        box-shadow: 0 6px 24px rgba(141,112,59,0.18);
    }
    .profile-sidebar .user-name {
        font-size: 20px;
        font-weight: 700;
        color: #8d703b;
        margin-bottom: 2px;
    }
    .profile-sidebar .user-email {
        font-size: 15px;
        color: #b3b3b3;
        margin-bottom: 18px;
    }
    .profile-content {
        background: #fff;
        border-radius: 16px;
        box-shadow: 0 4px 24px rgba(120,213,239,0.07);
        padding: 36px 32px 32px 32px;
        min-height: 500px;
        animation: fadeIn 0.5s;
    }
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(24px); }
        to { opacity: 1; transform: none; }
    }
    .profile-content h3 {
        color: #8d703b;
        font-weight: 700;
        margin-bottom: 24px;
    }
    .profile-content .btn-primary {
        background: #8d703b;
        border: none;
        color: #fff;
        font-weight: 600;
        border-radius: 8px;
        padding: 10px 28px;
        transition: background 0.2s, box-shadow 0.2s, transform 0.2s;
        box-shadow: 0 2px 8px rgba(141,112,59,0.10);
    }
    .profile-content .btn-primary:hover {
        background: #78d5ef;
        color: #fff;
        transform: translateY(-2px) scale(1.04);
        box-shadow: 0 6px 24px rgba(120,213,239,0.18);
    }
    @media (max-width: 991.98px) {
        .profile-sidebar, .profile-content {
            border-radius: 12px;
            padding: 18px 10px;
        }
        .profile-content {
            padding: 18px 10px;
        }
    }
    .dashboard-card {
        background: #fff;
        border-radius: 12px;
        box-shadow: 0 2px 12px rgba(141,112,59,0.08);
        padding: 24px 20px;
        margin-bottom: 24px;
        display: flex;
        align-items: center;
        gap: 24px;
    }
    .dashboard-avatar {
        width: 80px;
        height: 80px;
        border-radius: 50%;
        object-fit: cover;
        border: 3px solid #8d703b;
        box-shadow: 0 2px 8px rgba(141,112,59,0.12);
    }
    .dashboard-info {
        flex: 1;
    }
    .dashboard-info h4 {
        margin-bottom: 4px;
        color: #8d703b;
        font-weight: 700;
    }
    .dashboard-stats {
        display: flex;
        gap: 32px;
        flex-wrap: wrap;
        margin-top: 12px;
    }
    .dashboard-stat {
        min-width: 120px;
        background: linear-gradient(135deg, #f7f3ec 60%, #fff 100%);
        border-radius: 10px;
        padding: 12px 18px;
        text-align: center;
        box-shadow: 0 1px 4px rgba(141,112,59,0.06);
    }
    .dashboard-stat .stat-label {
        color: #8d703b;
        font-size: 15px;
        font-weight: 500;
    }
    .dashboard-stat .stat-value {
        font-size: 22px;
        font-weight: 700;
        color: #333;
        margin-top: 2px;
    }
    .dashboard-chart {
        background: #fff;
        border-radius: 12px;
        box-shadow: 0 2px 12px rgba(141,112,59,0.08);
        padding: 20px 20px 10px 20px;
        margin-bottom: 32px;
    }
    @media (max-width: 768px) {
        .dashboard-card { flex-direction: column; align-items: flex-start; }
        .dashboard-stats { gap: 12px; }
    }
    
    /* Modal styles */
    .modal-xl {
        max-width: 90% !important;
        width: 90% !important;
    }
    
    .modal-content {
        border-radius: 16px;
        box-shadow: 0 10px 40px rgba(0,0,0,0.2);
    }
    
    .modal-header {
        border-radius: 16px 16px 0 0;
        border-bottom: none;
        padding: 20px 30px;
    }
    
    .modal-body {
        padding: 30px;
        max-height: 70vh;
        overflow-y: auto;
    }
    
    .modal-footer {
        border-top: none;
        padding: 20px 30px;
        border-radius: 0 0 16px 16px;
    }
    
    .modal-title {
        font-size: 24px;
        font-weight: 700;
    }
    
    .modal .close {
        font-size: 28px;
        opacity: 0.8;
        transition: opacity 0.2s;
    }
    
    .modal .close:hover {
        opacity: 1;
    }
    
    @media (max-width: 768px) {
        .modal-xl {
            max-width: 95% !important;
            width: 95% !important;
            margin: 10px auto;
        }
        
        .modal-body {
            padding: 20px;
            max-height: 60vh;
        }
        
        .modal-header, .modal-footer {
            padding: 15px 20px;
        }
        
        .modal-title {
            font-size: 20px;
        }
    }
    
    /* Stat cards styles chỉ áp dụng cho profile */
    .profile-stat-section .stat-card {
        border-radius: 1.25rem;
        box-shadow: 0 4px 16px rgba(0,0,0,0.07);
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        padding: 2rem 0.5rem 1.2rem 0.5rem;
        text-align: center;
        transition: box-shadow 0.2s, transform 0.2s;
        background: #fff;
    }
    .profile-stat-section .stat-card:hover {
        box-shadow: 0 8px 32px rgba(0,0,0,0.13);
        transform: translateY(-4px) scale(1.03);
    }
    .profile-stat-section .stat-icon {
        width: 48px; height: 48px;
        border-radius: 50%;
        background: rgba(255,255,255,0.18);
        display: flex; align-items: center; justify-content: center;
        font-size: 1.7rem;
        color: #fff;
        margin-bottom: 12px;
    }
    .profile-stat-section .stat-number {
        font-size: 2rem;
        font-weight: bold;
        margin-bottom: 4px;
        color: #fff;
        word-break: break-all;
        max-width: 100%;
        overflow-wrap: break-word;
        text-align: center;
        min-width: 0;
    }
    .profile-stat-section .stat-label {
        font-size: 1rem;
        opacity: 0.93;
        font-weight: 500;
        color: #fff;
        white-space: nowrap;
    }
    .smooth-fade {
        transition: opacity 0.3s;
    }
    .smooth-fade.loading {
        opacity: 0.5;
        pointer-events: none;
    }
    #toast-notification .alert {
        box-shadow: 0 2px 16px rgba(0,0,0,0.15);
        font-size: 1.1rem;
        padding: 1rem 2rem;
        border-radius: 8px;
        text-align: center;
    }
</style>

<div class="hero-wrap" style="background-image: url('/client/images/bg_1.jpg');">
    <div class="overlay"></div>
    <div class="container">
        <div class="row no-gutters slider-text d-flex align-itemd-end justify-content-center">
            <div class="col-md-9 ftco-animate text-center d-flex align-items-end justify-content-center">
                <div class="text">
                    <p class="breadcrumbs mb-2"><span class="mr-2"><a href="{{ route('index') }}">Trang chủ</a></span> <span>Quản Lý Tài Khoản</span></p>
                    <h1 class="mb-4 bread">Quản Lý Tài Khoản</h1>
                </div>
            </div>
        </div>
    </div>
</div>

<section class="ftco-section bg-light">
    <div class="container">
        <div class="row">
            <div class="col-md-4 mb-4 mb-md-0">
                <div class="profile-sidebar text-center">
                    <img src="https://ui-avatars.com/api/?name={{ urlencode($user->name) }}&background=8d703b&color=fff&size=128" class="profile-avatar" alt="Avatar">
                    <div class="user-name">{{ $user->name }}</div>
                    <div class="user-email">{{ $user->email }}</div>
                    <hr style="border-top:1px solid #f0e6d2; margin: 18px 0;">
                    <nav class="nav flex-column nav-pills" id="profile-tabs" role="tablist">
                        <a class="nav-link active" id="profile-info-tab" data-toggle="pill" href="#profile-info" role="tab"><i class="fas fa-user"></i> Thông tin cá nhân</a>
                        <a class="nav-link" id="profile-password-tab" data-toggle="pill" href="#profile-password" role="tab"><i class="fas fa-key"></i> Đổi mật khẩu</a>
                        <a class="nav-link" id="profile-bookings-tab" data-toggle="pill" href="#profile-bookings" role="tab"><i class="fas fa-calendar-check"></i> Lịch sử đặt phòng</a>
                        <a class="nav-link" id="profile-reviews-tab" data-toggle="pill" href="#profile-reviews" role="tab"><i class="fas fa-star"></i> Đánh giá của tôi</a>
                    </nav>
                </div>
            </div>
            <div class="col-md-8">
                <div class="profile-content tab-content" id="profile-tabContent">
                    <div class="tab-pane fade show active" id="profile-info" role="tabpanel">
                        <h3 class="mb-3"><i class="fas fa-user-edit mr-2"></i>Thông Tin Cá Nhân</h3>
                        @if (session('success') && session('tab') == 'info')
                            <div class="alert alert-success d-flex align-items-center justify-content-center mb-4" role="alert">
                                <i class="fas fa-check-circle me-2"></i>
                                <div>{{ session('success') }}</div>
                            </div>
                        @endif
                        @if (session('error') && session('tab') == 'info')
                            <div class="alert alert-danger d-flex align-items-center justify-content-center mb-4" role="alert">
                                <i class="fas fa-exclamation-circle me-2"></i>
                                <div>{{ session('error') }}</div>
                            </div>
                        @endif
                        @if ($errors->any() && session('tab') == 'info')
                            <div class="alert alert-danger d-flex align-items-center justify-content-center mb-4" role="alert">
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                        @include('client.profile.info', ['user' => $user])
                    </div>
                    <div class="tab-pane fade" id="profile-password" role="tabpanel">
                        <h3 class="mb-3"><i class="fas fa-key mr-2"></i>Đổi Mật Khẩu</h3>
                        @if (session('success') && session('tab') == 'password')
                            <div class="alert alert-success d-flex align-items-center justify-content-center mb-4" role="alert">
                                <i class="fas fa-check-circle me-2"></i>
                                <div>{{ session('success') }}</div>
                            </div>
                        @endif
                        @if (session('error') && session('tab') == 'password')
                            <div class="alert alert-danger d-flex align-items-center justify-content-center mb-4" role="alert">
                                <i class="fas fa-exclamation-circle me-2"></i>
                                <div>{{ session('error') }}</div>
                            </div>
                        @endif
                        @if ($errors->any() && session('tab') == 'password')
                            <div class="alert alert-danger d-flex align-items-center justify-content-center mb-4" role="alert">
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                        @include('client.profile.password')
                    </div>
                    <div class="tab-pane fade" id="profile-bookings" role="tabpanel">
                        <h3 class="mb-3"><i class="fas fa-calendar-check mr-2"></i>Thống Kê Đặt Phòng</h3>
                        
                        <!-- Thống kê tổng quan -->
                        <div class="row profile-stat-section mb-4">
                            <div class="col-12 col-sm-6 col-lg-3 mb-4">
                                <div class="stat-card" style="background:linear-gradient(135deg,#a07b3b,#c9a063);">
                                    <div class="stat-icon"><i class="fas fa-calendar-check"></i></div>
                                    <div class="stat-number" id="total-bookings">{{ $dashboardData['totalBookings'] ?? 0 }}</div>
                                    <div class="stat-label">Tổng đặt phòng</div>
                                </div>
                            </div>
                            <div class="col-12 col-sm-6 col-lg-3 mb-4">
                                <div class="stat-card" style="background:linear-gradient(135deg,#28a745,#5be584);">
                                    <div class="stat-icon"><i class="fas fa-check-circle"></i></div>
                                    <div class="stat-number" id="completed-bookings">{{ $dashboardData['completedBookings'] ?? 0 }}</div>
                                    <div class="stat-label">Hoàn thành</div>
                                </div>
                            </div>
                            <div class="col-12 col-sm-6 col-lg-3 mb-4">
                                <div class="stat-card" style="background:linear-gradient(135deg,#ffc107,#ffe082);">
                                    <div class="stat-icon"><i class="fas fa-clock"></i></div>
                                    <div class="stat-number" id="pending-bookings">{{ $dashboardData['pendingBookings'] ?? 0 }}</div>
                                    <div class="stat-label">Đang chờ</div>
                                </div>
                            </div>
                            <div class="col-12 col-sm-6 col-lg-3 mb-4">
                                <div class="stat-card" style="background:linear-gradient(135deg,#17a2b8,#6fe7f7);">
                                    <div class="stat-icon"><i class="fas fa-dollar-sign"></i></div>
                                    @php
                                        $spent = $dashboardData['totalSpent'] ?? 0;
                                        $spentText = $spent >= 1000000 ? number_format($spent/1000000, 1) . ' triệu' : number_format($spent) . 'đ';
                                    @endphp
                                    <div class="stat-number" id="total-spent">{{ $spentText }}</div>
                                    <div class="stat-label">Tổng chi tiêu</div>
                                </div>
                            </div>
                        </div>

                        <!-- Biểu đồ đặt phòng theo tháng -->
                        <div class="chart-container mb-4">
                            <h5 class="mb-3"><i class="fas fa-chart-bar mr-2"></i>Biểu Đồ Đặt Phòng Theo Tháng</h5>
                            <canvas id="bookingsChart" height="100"></canvas>
                        </div>

                        <!-- Nút xem chi tiết -->
                        <div class="text-center">
                            <button type="button" class="btn btn-primary btn-lg" id="viewBookingsBtn">
                                <i class="fas fa-list"></i> Xem Chi Tiết Lịch Sử Đặt Phòng
                            </button>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="profile-reviews" role="tabpanel">
                        <h3 class="mb-3"><i class="fas fa-star mr-2"></i>Thống Kê Đánh Giá</h3>
                        
                        <!-- Thống kê tổng quan -->
                        <div class="row profile-stat-section mb-4">
                            <div class="col-12 col-sm-6 col-lg-3 mb-4">
                                <div class="stat-card" style="background:linear-gradient(135deg,#ffc107,#ffe082);">
                                    <div class="stat-icon"><i class="fas fa-star"></i></div>
                                    <div class="stat-number" id="total-reviews">{{ $dashboardData['totalReviews'] ?? 0 }}</div>
                                    <div class="stat-label">Tổng đánh giá</div>
                                </div>
                            </div>
                            <div class="col-12 col-sm-6 col-lg-3 mb-4">
                                <div class="stat-card" style="background:linear-gradient(135deg,#28a745,#5be584);">
                                    <div class="stat-icon"><i class="fas fa-thumbs-up"></i></div>
                                    <div class="stat-number" id="approved-reviews">{{ $dashboardData['approvedReviews'] ?? 0 }}</div>
                                    <div class="stat-label">Đã duyệt</div>
                                </div>
                            </div>
                            <div class="col-12 col-sm-6 col-lg-3 mb-4">
                                <div class="stat-card" style="background:linear-gradient(135deg,#dc3545,#ffb3b3);">
                                    <div class="stat-icon"><i class="fas fa-thumbs-down"></i></div>
                                    <div class="stat-number" id="rejected-reviews">{{ $dashboardData['rejectedReviews'] ?? 0 }}</div>
                                    <div class="stat-label">Từ chối</div>
                                </div>
                            </div>
                            <div class="col-12 col-sm-6 col-lg-3 mb-4">
                                <div class="stat-card" style="background:linear-gradient(135deg,#17a2b8,#6fe7f7);">
                                    <div class="stat-icon"><i class="fas fa-star-half-alt"></i></div>
                                    <div class="stat-number" id="avg-rating">{{ number_format($dashboardData['averageRating'] ?? 0, 1) }}/5</div>
                                    <div class="stat-label">Điểm trung bình</div>
                                </div>
                            </div>
                        </div>

                        <!-- Biểu đồ đánh giá theo sao -->
                        <div class="chart-container mb-4">
                            <h5 class="mb-3"><i class="fas fa-chart-pie mr-2"></i>Phân Bố Đánh Giá Theo Sao</h5>
                            <canvas id="reviewsChart" height="100"></canvas>
                        </div>

                        <!-- Nút xem chi tiết -->
                        <div class="text-center">
                            <button type="button" class="btn btn-primary btn-lg" id="viewReviewsBtn">
                                <i class="fas fa-list"></i> Xem Chi Tiết Đánh Giá
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Modal cho Bookings -->
<div class="modal fade" id="bookingsModal" tabindex="-1" role="dialog" aria-labelledby="bookingsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="bookingsModalLabel">
                    <i class="fas fa-calendar-check mr-2"></i>Lịch Sử Đặt Phòng
                </h5>
                <button type="button" class="btn-close text-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="bookings-content" class="smooth-fade">
                    <div class="text-center py-4">
                        <div class="spinner-border text-primary" role="status">
                            <span class="sr-only">Đang tải...</span>
                        </div>
                        <p class="mt-2 text-muted">Đang tải dữ liệu đặt phòng...</p>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal cho Reviews -->
<div class="modal fade" id="reviewsModal" tabindex="-1" role="dialog" aria-labelledby="reviewsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="reviewsModalLabel">
                    <i class="fas fa-star mr-2"></i>Đánh Giá Của Tôi
                </h5>
                <button type="button" class="btn-close text-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="reviews-content" class="smooth-fade">
                    <div class="text-center py-4">
                        <div class="spinner-border text-primary" role="status">
                            <span class="sr-only">Đang tải...</span>
                        </div>
                        <p class="mt-2 text-muted">Đang tải dữ liệu đánh giá...</p>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal cho Chi tiết Booking -->
<div class="modal fade" id="bookingDetailModal" tabindex="-1" role="dialog" aria-labelledby="bookingDetailModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title" id="bookingDetailModalLabel">
                    <i class="fas fa-calendar-check mr-2"></i>Chi Tiết Đặt Phòng
                </h5>
                <button type="button" class="btn-close text-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="booking-detail-content">
                    <div class="text-center py-4">
                        <div class="spinner-border text-info" role="status">
                            <span class="sr-only">Đang tải...</span>
                        </div>
                        <p class="mt-2 text-muted">Đang tải chi tiết đặt phòng...</p>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal cho Chi tiết Review -->
<div class="modal fade" id="reviewDetailModal" tabindex="-1" role="dialog" aria-labelledby="reviewDetailModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-warning text-dark">
                <h5 class="modal-title" id="reviewDetailModalLabel">
                    <i class="fas fa-star mr-2"></i>Chi Tiết Đánh Giá
                </h5>
                <button type="button" class="btn-close text-dark" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="review-detail-content">
                    <div class="text-center py-4">
                        <div class="spinner-border text-warning" role="status">
                            <span class="sr-only">Đang tải...</span>
                        </div>
                        <p class="mt-2 text-muted">Đang tải chi tiết đánh giá...</p>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal cho Form Đánh Giá -->
<div class="modal fade" id="reviewFormModal" tabindex="-1" role="dialog" aria-labelledby="reviewFormModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title" id="reviewFormModalLabel">
                    <i class="fas fa-star mr-2"></i>Viết Đánh Giá
                </h5>
                <button type="button" class="btn-close text-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="review-form-content">
                    <div class="text-center py-4">
                        <div class="spinner-border text-success" role="status">
                            <span class="sr-only">Đang tải...</span>
                        </div>
                        <p class="mt-2 text-muted">Đang tải form đánh giá...</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>



<!-- Toast Notification -->
<div id="toast-notification" style="display:none; position: fixed; top: 20px; left: 50%; transform: translateX(-50%); z-index: 9999; min-width: 300px;">
    <div id="toast-content" class="alert mb-0"></div>
</div>

@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Đảm bảo jQuery và Bootstrap đã load
document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM loaded');
    console.log('jQuery version:', typeof $ !== 'undefined' ? $.fn.jquery : 'jQuery not loaded');
    console.log('Bootstrap version:', typeof bootstrap !== 'undefined' ? 'Bootstrap loaded' : 'Bootstrap not loaded');
    
    // Kiểm tra CSRF token
    const csrfToken = document.querySelector('meta[name="csrf-token"]');
    if (!csrfToken) {
        console.error('CSRF token not found!');
        return;
    }
    console.log('CSRF token found');
    
    // Khởi tạo tab system
    initTabs();
    
    // Khởi tạo modal system
    initModals();
    
    // Khởi tạo charts
    initCharts();
});

function initTabs() {
    // Kích hoạt tab đúng theo session (nếu có)
    @if(session('tab'))
        $('#profile-tabs a[href="#profile-{{ session('tab') }}"]').tab('show');
        localStorage.removeItem('profileActiveTab');
    @elseif(session('active_tab'))
        $('#profile-tabs a[href="#profile-{{ session('active_tab') }}"]').tab('show');
        localStorage.removeItem('profileActiveTab');
    @else
        // Khi truy cập trực tiếp vào profile (không có session), đảm bảo hiển thị tab đầu tiên
        $('#profile-tabs a[href="#profile-info"]').tab('show');
        localStorage.removeItem('profileActiveTab');
    @endif

    // Khi click tab, lưu vào localStorage để giữ trạng thái khi reload
    document.querySelectorAll('#profile-tabs a').forEach(function(tab) {
        tab.addEventListener('click', function(e) {
            // Kích hoạt Bootstrap 4 tab
            $(this).tab('show');
            localStorage.setItem('profileActiveTab', this.getAttribute('href'));
            
            // Tạo biểu đồ khi tab được kích hoạt
            var targetTab = this.getAttribute('href');
            if (targetTab === '#profile-bookings') {
                createBookingsChart();
            } else if (targetTab === '#profile-reviews') {
                createReviewsChart();
            }
        });
    });
}

function initModals() {
    // Nút xem bookings
    const viewBookingsBtn = document.getElementById('viewBookingsBtn');
    if (viewBookingsBtn) {
        viewBookingsBtn.addEventListener('click', function() {
            $('#bookingsModal').modal('show');
        });
    }
    
    // Nút xem reviews
    const viewReviewsBtn = document.getElementById('viewReviewsBtn');
    if (viewReviewsBtn) {
        viewReviewsBtn.addEventListener('click', function() {
            $('#reviewsModal').modal('show');
        });
    }
    
    // Xử lý modal bookings
    $('#bookingsModal').on('show.bs.modal', function () {
        console.log('Loading bookings data...');
        loadBookingsData();
    });
    
    // Xử lý modal reviews
    $('#reviewsModal').on('show.bs.modal', function () {
        console.log('Loading reviews data...');
        loadReviewsData();
    });
}

function enableBookingsPaginationAjax() {
    $('#bookings-content').on('click', '.pagination a', function(e) {
        e.preventDefault();
        var url = $(this).attr('href');
        if (!url) return;
        var $content = $('#bookings-content');
        $content.addClass('loading');
        fetch(url, {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            credentials: 'same-origin'
        })
        .then(res => res.text())
        .then(html => {
            $content.fadeOut(150, function() {
                $content.html(html);
                $content.removeClass('loading');
                $content.fadeIn(150);
                enableBookingsPaginationAjax();
                enableDetailModals();
            });
        })
        .catch(err => {
            $content.html('<div class="alert alert-danger">Không thể tải dữ liệu đặt phòng!</div>');
            $content.removeClass('loading');
        });
    });
}

function enableReviewsPaginationAjax() {
    $('#reviews-content').on('click', '.pagination a', function(e) {
        e.preventDefault();
        var url = $(this).attr('href');
        if (!url) return;
        var $content = $('#reviews-content');
        $content.addClass('loading');
        fetch(url, {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            credentials: 'same-origin'
        })
        .then(res => res.text())
        .then(html => {
            $content.fadeOut(150, function() {
                $content.html(html);
                $content.removeClass('loading');
                $content.fadeIn(150);
                enableReviewsPaginationAjax();
                enableDetailModals();
            });
        })
        .catch(err => {
            $content.html('<div class="alert alert-danger">Không thể tải dữ liệu đánh giá!</div>');
            $content.removeClass('loading');
        });
    });
}

function enableDetailModals() {
    // Xử lý nút xem chi tiết booking
    $('#bookings-content').on('click', '.btn-view-booking', function(e) {
        e.preventDefault();
        var bookingId = $(this).data('booking-id');
        if (bookingId) {
            loadBookingDetail(bookingId);
        }
    });
    
    // Xử lý nút xem chi tiết review
    $('#reviews-content').on('click', '.btn-view-review', function(e) {
        e.preventDefault();
        var reviewId = $(this).data('review-id');
        if (reviewId) {
            loadReviewDetail(reviewId);
        }
    });
}

function loadBookingDetail(bookingId) {
    $('#bookingDetailModal').modal('show');
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    
    fetch('/user/bookings/' + bookingId + '/detail', {
        method: 'GET',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': csrfToken,
            'Accept': 'text/html, application/xhtml+xml, application/xml;q=0.9, image/webp, */*;q=0.8'
        },
        credentials: 'same-origin'
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Network response was not ok: ' + response.status);
        }
        return response.text();
    })
    .then(html => {
        document.getElementById('booking-detail-content').innerHTML = html;
    })
    .catch(error => {
        document.getElementById('booking-detail-content').innerHTML = 
            '<div class="alert alert-danger">Không thể tải chi tiết đặt phòng: ' + error.message + '</div>';
    });
}

function loadReviewDetail(reviewId) {
    $('#reviewDetailModal').modal('show');
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    
    fetch('/user/reviews/' + reviewId + '/detail', {
        method: 'GET',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': csrfToken,
            'Accept': 'text/html, application/xhtml+xml, application/xml;q=0.9, image/webp, */*;q=0.8'
        },
        credentials: 'same-origin'
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Network response was not ok: ' + response.status);
        }
        return response.text();
    })
    .then(html => {
        document.getElementById('review-detail-content').innerHTML = html;
    })
    .catch(error => {
        document.getElementById('review-detail-content').innerHTML = 
            '<div class="alert alert-danger">Không thể tải chi tiết đánh giá: ' + error.message + '</div>';
    });
}

function loadBookingsData() {
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    
    fetch('/user/bookings/partial', {
        method: 'GET',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': csrfToken,
            'Accept': 'text/html, application/xhtml+xml, application/xml;q=0.9, image/webp, */*;q=0.8'
        },
        credentials: 'same-origin'
    })
    .then(response => {
        console.log('Bookings response status:', response.status);
        if (!response.ok) {
            throw new Error('Network response was not ok: ' + response.status);
        }
        return response.text();
    })
    .then(html => {
        console.log('Bookings HTML received, length:', html.length);
        document.getElementById('bookings-content').innerHTML = html;
        enableBookingsPaginationAjax();
        enableDetailModals();
    })
    .catch(error => {
        console.error('Error loading bookings:', error);
        document.getElementById('bookings-content').innerHTML = 
            '<div class="alert alert-danger">Không thể tải dữ liệu đặt phòng: ' + error.message + '</div>';
    });
}

function loadReviewsData() {
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    
    fetch('/user/reviews/partial', {
        method: 'GET',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': csrfToken,
            'Accept': 'text/html, application/xhtml+xml, application/xml;q=0.9, image/webp, */*;q=0.8'
        },
        credentials: 'same-origin'
    })
    .then(response => {
        console.log('Reviews response status:', response.status);
        if (!response.ok) {
            throw new Error('Network response was not ok: ' + response.status);
        }
        return response.text();
    })
    .then(html => {
        console.log('Reviews HTML received, length:', html.length);
        document.getElementById('reviews-content').innerHTML = html;
        enableReviewsPaginationAjax();
        enableDetailModals();
    })
    .catch(error => {
        console.error('Error loading reviews:', error);
        document.getElementById('reviews-content').innerHTML = 
            '<div class="alert alert-danger">Không thể tải dữ liệu đánh giá: ' + error.message + '</div>';
    });
}

function initCharts() {
    // Tạo biểu đồ booking chính
    const ctx = document.getElementById('bookingChart');
    if (ctx) {
        const bookingChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: [
                    'Tháng 1', 'Tháng 2', 'Tháng 3', 'Tháng 4', 'Tháng 5', 'Tháng 6',
                    'Tháng 7', 'Tháng 8', 'Tháng 9', 'Tháng 10', 'Tháng 11', 'Tháng 12'
                ],
                datasets: [{
                    label: 'Số booking',
                    data: {!! json_encode(array_values($dashboardData['monthlyBookings'] ?? array_fill(1,12,0))) !!},
                    backgroundColor: '#8d703b',
                    borderRadius: 6,
                    maxBarThickness: 32
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { display: false },
                    tooltip: { enabled: true }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: { stepSize: 1 }
                    }
                }
            }
        });
    }
}

// Hàm tạo biểu đồ đặt phòng
function createBookingsChart() {
    const ctx = document.getElementById('bookingsChart');
    if (!ctx) return;
    
    // Xóa biểu đồ cũ nếu có
    if (window.bookingsChart) {
        window.bookingsChart.destroy();
    }
    
    window.bookingsChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: [
                'Tháng 1', 'Tháng 2', 'Tháng 3', 'Tháng 4', 'Tháng 5', 'Tháng 6',
                'Tháng 7', 'Tháng 8', 'Tháng 9', 'Tháng 10', 'Tháng 11', 'Tháng 12'
            ],
            datasets: [{
                label: 'Số đặt phòng',
                data: {!! json_encode(array_values($dashboardData['monthlyBookings'] ?? array_fill(1,12,0))) !!},
                backgroundColor: '#8d703b',
                borderRadius: 6,
                maxBarThickness: 32
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: { 
                    enabled: true,
                    backgroundColor: 'rgba(0,0,0,0.8)',
                    titleColor: '#fff',
                    bodyColor: '#fff'
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: { 
                        stepSize: 1,
                        color: '#666'
                    },
                    grid: {
                        color: 'rgba(0,0,0,0.1)'
                    }
                },
                x: {
                    ticks: {
                        color: '#666'
                    },
                    grid: {
                        display: false
                    }
                }
            }
        }
    });
}

// Hàm tạo biểu đồ đánh giá
function createReviewsChart() {
    const ctx = document.getElementById('reviewsChart');
    if (!ctx) return;
    
    // Xóa biểu đồ cũ nếu có
    if (window.reviewsChart) {
        window.reviewsChart.destroy();
    }
    
    // Dữ liệu mẫu cho biểu đồ đánh giá (có thể thay bằng dữ liệu thật từ controller)
    const reviewData = {
        '5 sao': {{ $dashboardData['ratingCounts'][5] ?? 0 }},
        '4 sao': {{ $dashboardData['ratingCounts'][4] ?? 0 }},
        '3 sao': {{ $dashboardData['ratingCounts'][3] ?? 0 }},
        '2 sao': {{ $dashboardData['ratingCounts'][2] ?? 0 }},
        '1 sao': {{ $dashboardData['ratingCounts'][1] ?? 0 }}
    };
    
    window.reviewsChart = new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: Object.keys(reviewData),
            datasets: [{
                data: Object.values(reviewData),
                backgroundColor: [
                    '#28a745', // 5 sao - xanh lá
                    '#17a2b8', // 4 sao - xanh dương
                    '#ffc107', // 3 sao - vàng
                    '#fd7e14', // 2 sao - cam
                    '#dc3545'  // 1 sao - đỏ
                ],
                borderWidth: 2,
                borderColor: '#fff'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        padding: 20,
                        usePointStyle: true,
                        font: {
                            size: 12
                        }
                    }
                },
                tooltip: {
                    backgroundColor: 'rgba(0,0,0,0.8)',
                    titleColor: '#fff',
                    bodyColor: '#fff',
                    callbacks: {
                        label: function(context) {
                            const total = context.dataset.data.reduce((a, b) => a + b, 0);
                            const percentage = ((context.parsed / total) * 100).toFixed(1);
                            return context.label + ': ' + context.parsed + ' (' + percentage + '%)';
                        }
                    }
                }
            }
        }
    });
}

$(document).on('click', '.btn-view-booking', function() {
    var bookingId = $(this).data('booking-id');
    $('#booking-detail-content').html('<div class="text-center py-4"><span class="spinner-border"></span> Đang tải...</div>');
    $('#bookingDetailModal').modal('show');
    $.get('/user/bookings/' + bookingId + '/detail', function(html) {
        $('#booking-detail-content').html(html);
    }).fail(function() {
        $('#booking-detail-content').html('<div class="alert alert-danger">Không thể tải chi tiết booking.</div>');
    });
});

$(document).on('click', '.btn-view-review', function() {
    var reviewId = $(this).data('review-id');
    $('#review-detail-content').html('<div class="text-center py-4"><span class="spinner-border"></span> Đang tải...</div>');
    $('#reviewDetailModal').modal('show');
    $.get('/user/reviews/' + reviewId + '/detail', function(html) {
        $('#review-detail-content').html(html);
    }).fail(function() {
        $('#review-detail-content').html('<div class="alert alert-danger">Không thể tải chi tiết đánh giá.</div>');
    });
});

$(document).on('click', '.create-review-btn', function() {
    var roomTypeId = $(this).data('room-type-id');
    var bookingId = $(this).data('booking-id');
    console.log('Creating review for room type:', roomTypeId, 'booking:', bookingId);
    $('#review-form-content').html('<div class="text-center py-4"><span class="spinner-border"></span> Đang tải...</div>');
    $('#reviewFormModalLabel').html('<i class="fas fa-star mr-2"></i>Viết Đánh Giá');
    $('#reviewFormModal').modal('show');
    $.get('/room-type-reviews/' + roomTypeId + '/form?booking_id=' + bookingId, function(html) {
        console.log('Form loaded successfully');
        $('#review-form-content').html(html);
    }).fail(function(xhr, status, error) {
        console.error('Failed to load form:', status, error);
        console.error('Response:', xhr.responseText);
        $('#review-form-content').html('<div class="alert alert-danger">Không thể tải form đánh giá. Lỗi: ' + error + '</div>');
    });
});

$(document).on('click', '.edit-review-btn', function() {
    var reviewId = $(this).data('review-id');
    $('#review-form-content').html('<div class="text-center py-4"><span class="spinner-border"></span> Đang tải...</div>');
    $('#reviewFormModalLabel').html('<i class="fas fa-edit mr-2"></i>Chỉnh Sửa Đánh Giá');
    $('#reviewFormModal').modal('show');
    $.get('/user/reviews/' + reviewId + '/data', function(data) {
        if (data.review) {
            var review = data.review;
            var formHtml = `
                <form id="editReviewForm" method="POST" action="/room-type-reviews/${review.id}">
                    <input type="hidden" name="_token" value="${$('meta[name="csrf-token"]').attr('content')}">
                    <input type="hidden" name="_method" value="PUT">
                    <input type="hidden" name="room_type_id" value="${review.room_type_id}">
                    ${(review.room_type_name ? `<div class='alert alert-info mb-3'><strong>Đánh giá cho loại phòng:</strong> ${review.room_type_name}${review.booking_id ? `<br><strong>Mã booking:</strong> ${review.booking_id}` : ''}</div>` : '')}
                    <div class="form-group">
                        <label for="rating">Đánh giá tổng thể (sao):</label>
                        <select name="rating" id="rating" class="form-control" required>
                            <option value="">Chọn số sao</option>
                            ${[5,4,3,2,1].map(i => `<option value="${i}" ${review.rating == i ? 'selected' : ''}>${i} sao</option>`).join('')}
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="comment">Nội dung đánh giá:</label>
                        <textarea name="comment" id="comment" class="form-control" rows="3" required>${review.comment || ''}</textarea>
                    </div>
                    <div class="form-group">
                        <label><input type="checkbox" name="is_anonymous" value="1" ${review.is_anonymous ? 'checked' : ''}> Ẩn danh</label>
                    </div>
                    <button type="submit" class="btn btn-primary">Cập nhật đánh giá</button>
                </form>
            `;
            $('#review-form-content').html(formHtml);
            
            // Xử lý submit form
            $('#editReviewForm').off('submit').on('submit', function(e) {
                e.preventDefault();
                var form = $(this);
                var btn = form.find('button[type=submit]');
                btn.prop('disabled', true).text('Đang cập nhật...');
                $.ajax({
                    url: form.attr('action'),
                    method: 'POST',
                    data: form.serialize(),
                    success: function(res) {
                        btn.prop('disabled', false).text('Cập nhật đánh giá');
                        $('#reviewFormModal').modal('hide');
                        if (typeof loadReviewsData === 'function') loadReviewsData();
                        if (typeof loadBookingsData === 'function') loadBookingsData();
                        showToast(res.message || 'Đánh giá đã được cập nhật!', 'success');
                    },
                    error: function(xhr) {
                        btn.prop('disabled', false).text('Cập nhật đánh giá');
                        var msg = 'Có lỗi xảy ra khi cập nhật đánh giá.';
                        if (xhr.responseJSON && xhr.responseJSON.message) msg = xhr.responseJSON.message;
                        showToast(msg, 'danger');
                    }
                });
            });
        } else {
            $('#review-form-content').html('<div class="alert alert-danger">Không tìm thấy dữ liệu đánh giá.</div>');
        }
    }).fail(function() {
        $('#review-form-content').html('<div class="alert alert-danger">Không thể tải form chỉnh sửa đánh giá.</div>');
    });
});

$(document).on('click', '.delete-review-btn', function() {
    var reviewId = $(this).data('review-id');
    if (!confirm('Bạn có chắc chắn muốn xóa đánh giá này?')) return;
    $.ajax({
        url: '/room-type-reviews/' + reviewId,
        method: 'DELETE',
        data: { _token: $('meta[name="csrf-token"]').attr('content') },
        success: function(res) {
            $('#reviewFormModal').modal('hide');
            if (typeof loadReviewsData === 'function') loadReviewsData();
            if (typeof loadBookingsData === 'function') loadBookingsData();
            showToast(res.message || 'Đã xóa đánh giá!', 'success');
        },
        error: function(xhr) {
            var msg = 'Có lỗi xảy ra khi xóa đánh giá.';
            if (xhr.responseJSON && xhr.responseJSON.message) msg = xhr.responseJSON.message;
            showToast(msg, 'danger');
        }
    });
});

$(document).on('click', '.cancel-booking-btn', function() {
    var bookingId = $(this).data('booking-id');
    if (!confirm('Bạn có chắc chắn muốn hủy đặt phòng này?')) return;
    $.post('/booking/cancel/' + bookingId, {_token: $('meta[name="csrf-token"]').attr('content')}, function(res) {
        if (typeof loadBookingsData === 'function') loadBookingsData();
        showToast(res.message || 'Đã hủy đặt phòng!', 'success');
    }).fail(function(xhr) {
        showToast('Có lỗi xảy ra khi hủy đặt phòng.', 'danger');
    });
});

$(document).on('shown.bs.tab', 'a[data-toggle="pill"][href="#profile-bookings"]', function (e) {
    if (typeof loadBookingsData === 'function') loadBookingsData();
});

function showToast(message, type = 'success') {
    var $toast = $('#toast-notification');
    var $content = $('#toast-content');
    $content.removeClass('alert-success alert-danger alert-info alert-warning')
        .addClass('alert-' + type)
        .html(message);
    $toast.fadeIn(200);
    setTimeout(function() {
        $toast.fadeOut(400);
    }, 2500);
}
</script>
@endsection 