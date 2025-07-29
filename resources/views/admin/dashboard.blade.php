@extends('admin.layouts.admin-master')

@section('title', 'Dashboard')

@section('header', 'Dashboard')

@section('content')
<div class="row">
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-primary h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-xs text-uppercase mb-1 text-muted">Đặt phòng hôm nay</div>
                        <div class="h3 mb-0 font-weight-bold">{{ $todayBookings ?? 0 }}</div>
                    </div>
                    <div class="icon-circle bg-primary">
                        <i class="fas fa-calendar-day text-white"></i>
                    </div>
                </div>
            </div>
            <div class="card-footer bg-transparent">
                <a href="{{ route('admin.bookings.index') }}" class="text-decoration-none">
                    <span class="small text-primary">Xem chi tiết</span>
                    <i class="fas fa-chevron-right ms-1 small text-primary"></i>
                </a>
            </div>
        </div>
    </div>
    
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-success h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-xs text-uppercase mb-1 text-muted">Doanh thu tháng</div>
                        <div class="h3 mb-0 font-weight-bold">{{ number_format($monthlyRevenue ?? 0, 0, ',', '.') }} VNĐ</div>
                    </div>
                    <div class="icon-circle bg-success">
                        <i class="fas fa-dollar-sign text-white"></i>
                    </div>
                </div>
            </div>
            <div class="card-footer bg-transparent">
                <a href="{{ route('admin.bookings.report') }}" class="text-decoration-none">
                    <span class="small text-success">Xem báo cáo</span>
                    <i class="fas fa-chevron-right ms-1 small text-success"></i>
                </a>
            </div>
        </div>
    </div>
    
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-info h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-xs text-uppercase mb-1 text-muted">Tỷ lệ đặt phòng</div>
                        <div class="h3 mb-0 font-weight-bold">{{ $bookingRate ?? 0 }}%</div>
                    </div>
                    <div class="icon-circle bg-info">
                        <i class="fas fa-percentage text-white"></i>
                    </div>
                </div>
            </div>
            <div class="card-footer bg-transparent">
                <div class="progress" style="height: 8px;">
                    <div class="progress-bar bg-info" role="progressbar" style="width: {{ $bookingRate ?? 0 }}%" aria-valuenow="{{ $bookingRate ?? 0 }}" aria-valuemin="0" aria-valuemax="100"></div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-warning h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-xs text-uppercase mb-1 text-muted">Đặt phòng chờ xác nhận</div>
                        <div class="h3 mb-0 font-weight-bold">{{ $pendingBookings ?? 0 }}</div>
                    </div>
                    <div class="icon-circle bg-warning">
                        <i class="fas fa-clock text-white"></i>
                    </div>
                </div>
            </div>
            <div class="card-footer bg-transparent">
                <a href="{{ route('admin.bookings.index', ['status' => 'pending']) }}" class="text-decoration-none">
                    <span class="small text-warning">Xử lý ngay</span>
                    <i class="fas fa-chevron-right ms-1 small text-warning"></i>
                </a>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-xl-8 col-lg-7">
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold">Đặt phòng gần đây</h6>
                <a href="{{ route('admin.bookings.index') }}" class="btn btn-sm btn-primary">
                    <i class="fas fa-list me-1"></i> Xem tất cả
                </a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Mã đặt phòng</th>
                                <th>Khách hàng</th>
                                <th>Phòng</th>
                                <th>Check-in</th>
                                <th>Trạng thái</th>
                                <th>Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentBookings ?? [] as $booking)
                            <tr>
                                <td>{{ $booking->booking_id }}</td>
                                <td>{{ $booking->user->name ?? 'N/A' }}</td>
                                <td>{{ $booking->room->name ?? 'N/A' }}</td>
                                <td>{{ $booking->check_in_date->format('d/m/Y') }}</td>
                                <td>
                                                                        <span class="badge bg-{{ 
                                        $booking->status == 'pending' ? 'warning' : 
                                        ($booking->status == 'confirmed' ? 'primary' : 
                                        ($booking->status == 'checked_in' ? 'info' :
                                        ($booking->status == 'checked_out' ? 'secondary' :
                                        ($booking->status == 'completed' ? 'success' : 
                                        ($booking->status == 'no_show' ? 'dark' : 'danger'))))) 
                                    }}">
                                        {{ $booking->status_text }}
                                    </span>
                                </td>
                                <td>
                                    <a href="{{ route('admin.bookings.show', $booking->id) }}" class="btn btn-sm btn-info">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center">Không có đặt phòng nào</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-xl-4 col-lg-5">
        <div class="card mb-4">
            <div class="card-header">
                <h6 class="m-0 font-weight-bold">Thống kê theo trạng thái</h6>
            </div>
            <div class="card-body">
                <div class="chart-pie mb-4">
                    <canvas id="bookingStatusChart"></canvas>
                </div>
                <div class="mt-4 text-center small">
                    <div class="d-flex flex-wrap justify-content-center">
                        <span class="me-3 mb-1">
                            <i class="fas fa-circle text-warning"></i> Chờ xác nhận
                        </span>
                        <span class="me-3 mb-1">
                            <i class="fas fa-circle text-primary"></i> Đã xác nhận
                        </span>
                        <span class="me-3 mb-1">
                            <i class="fas fa-circle text-success"></i> Hoàn thành
                        </span>
                        <span class="me-3 mb-1">
                            <i class="fas fa-circle text-danger"></i> Đã hủy
                        </span>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="card">
            <div class="card-header">
                <h6 class="m-0 font-weight-bold">Truy cập nhanh</h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-6 mb-3">
                        <a href="{{ route('admin.bookings.create') }}" class="btn btn-primary w-100 py-3">
                            <i class="fas fa-plus mb-2 d-block fs-4"></i>
                            Tạo đặt phòng
                        </a>
                    </div>
                    <div class="col-6 mb-3">
                        <a href="{{ route('admin.bookings.report') }}" class="btn btn-success w-100 py-3">
                            <i class="fas fa-chart-line mb-2 d-block fs-4"></i>
                            Xem báo cáo
                        </a>
                    </div>
                    <div class="col-6">
                        <a href="#" class="btn btn-info w-100 py-3">
                            <i class="fas fa-bed mb-2 d-block fs-4"></i>
                            Quản lý phòng
                        </a>
                    </div>
                    <div class="col-6">
                        <a href="#" class="btn btn-secondary w-100 py-3">
                            <i class="fas fa-users mb-2 d-block fs-4"></i>
                            Quản lý user
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('styles')
<style>
    .border-left-primary {
        border-left: 4px solid var(--primary-color);
    }
    
    .border-left-success {
        border-left: 4px solid var(--success-color);
    }
    
    .border-left-info {
        border-left: 4px solid var(--info-color);
    }
    
    .border-left-warning {
        border-left: 4px solid var(--warning-color);
    }
    
    .icon-circle {
        height: 60px;
        width: 60px;
        border-radius: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    .icon-circle i {
        font-size: 1.8rem;
    }
    
    .card-footer {
        border-top: 1px solid rgba(0,0,0,.05);
        padding: 0.75rem 1.25rem;
    }
    
    .chart-pie {
        position: relative;
        height: 15rem;
    }
</style>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Dữ liệu cho biểu đồ
    const statusData = {
        pending: {{ $statusCounts['pending'] ?? 0 }},
        confirmed: {{ $statusCounts['confirmed'] ?? 0 }},
        cancelled: {{ $statusCounts['cancelled'] ?? 0 }},
        completed: {{ $statusCounts['completed'] ?? 0 }}
    };
    
    // Tạo biểu đồ tròn
    const ctx = document.getElementById('bookingStatusChart').getContext('2d');
    new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: ['Chờ xác nhận', 'Đã xác nhận', 'Hoàn thành', 'Đã hủy'],
            datasets: [{
                data: [statusData.pending, statusData.confirmed, statusData.completed, statusData.cancelled],
                backgroundColor: ['#ffc107', '#0d6efd', '#198754', '#dc3545'],
                hoverBackgroundColor: ['#e0a800', '#0a58ca', '#146c43', '#bb2d3b'],
                hoverBorderColor: "rgba(234, 236, 244, 1)",
            }],
        },
        options: {
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                }
            },
            cutout: '70%',
        },
    });
</script>
@endsection 