@extends('admin.layouts.admin-master')

@section('title', 'Báo cáo đặt phòng')

@section('header', 'Báo cáo đặt phòng')

@section('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
@endsection

@section('content')
<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex justify-content-between align-items-center">
        <h6 class="m-0 font-weight-bold text-primary">Báo cáo đặt phòng</h6>
        <a href="{{ route('admin.bookings.index') }}" class="btn btn-sm btn-secondary">
            <i class="fas fa-arrow-left"></i> Quay lại
        </a>
    </div>
    <div class="card-body">
        <div class="mb-4">
            <form action="{{ route('admin.bookings.report') }}" method="GET" class="row g-3">
                <div class="col-md-3">
                    <label for="from_date" class="form-label">Từ ngày</label>
                    <input type="date" name="from_date" id="from_date" class="form-control" value="{{ $fromDate ?? '' }}">
                </div>
                <div class="col-md-3">
                    <label for="to_date" class="form-label">Đến ngày</label>
                    <input type="date" name="to_date" id="to_date" class="form-control" value="{{ $toDate ?? '' }}">
                </div>
                <div class="col-md-3">
                    <label for="status" class="form-label">Trạng thái</label>
                    <select name="status" id="status" class="form-select">
                        <option value="all">Tất cả</option>
                        <option value="pending" {{ $status == 'pending' ? 'selected' : '' }}>Chờ xác nhận</option>
                        <option value="confirmed" {{ $status == 'confirmed' ? 'selected' : '' }}>Đã xác nhận</option>
                        <option value="checked_in" {{ $status == 'checked_in' ? 'selected' : '' }}>Đã nhận phòng</option>
                        <option value="checked_out" {{ $status == 'checked_out' ? 'selected' : '' }}>Đã trả phòng</option>
                        <option value="completed" {{ $status == 'completed' ? 'selected' : '' }}>Hoàn thành</option>
                        <option value="cancelled" {{ $status == 'cancelled' ? 'selected' : '' }}>Đã hủy</option>
                        <option value="no_show" {{ $status == 'no_show' ? 'selected' : '' }}>Không đến</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">&nbsp;</label>
                    <div>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-search"></i> Lọc
                        </button>
                        <a href="{{ route('admin.bookings.report') }}" class="btn btn-secondary">
                            <i class="fas fa-sync"></i> Đặt lại
                        </a>
                    </div>
                </div>
            </form>
        </div>
        
        <div class="row mb-4">
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-primary shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                    Tổng số đặt phòng</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $bookings->count() }}</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-calendar fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-success shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                    Tổng doanh thu</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($totalRevenue, 0, ',', '.') }} VNĐ</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-dollar-sign fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-info shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                    Đặt phòng hoàn thành</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $statusStats['completed']['count'] ?? 0 }}</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-clipboard-check fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-warning shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                    Đặt phòng bị hủy</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $statusStats['cancelled']['count'] ?? 0 }}</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-ban fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="row">
            <div class="col-lg-8">
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Danh sách đặt phòng</h6>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered" width="100%" cellspacing="0">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Khách hàng</th>
                                        <th>Phòng</th>
                                        <th>Ngày nhận</th>
                                        <th>Ngày trả</th>
                                        <th>Tổng tiền</th>
                                        <th>Trạng thái</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($bookings as $booking)
                                    <tr>
                                        <td>{{ $booking->booking_id }}</td>
                                        <td>{{ $booking->user->name ?? 'N/A' }}</td>
                                        <td>{{ $booking->room->name ?? 'N/A' }}</td>
                                        <td>{{ $booking->check_in_date->format('d/m/Y') }}</td>
                                        <td>{{ $booking->check_out_date->format('d/m/Y') }}</td>
                                        <td>{{ number_format($booking->price, 0, ',', '.') }} VNĐ</td>
                                        <td>
                                            @switch($booking->status)
                                                @case('pending')
                                                    <span class="badge bg-warning">Chờ xác nhận</span>
                                                    @break
                                                @case('confirmed')
                                                    <span class="badge bg-success">Đã xác nhận</span>
                                                    @break
                                                @case('checked_in')
                                                    <span class="badge bg-info">Đã nhận phòng</span>
                                                    @break
                                                @case('checked_out')
                                                    <span class="badge bg-secondary">Đã trả phòng</span>
                                                    @break
                                                @case('cancelled')
                                                    <span class="badge bg-danger">Đã hủy</span>
                                                    @break
                                                @case('completed')
                                                    <span class="badge bg-info">Hoàn thành</span>
                                                    @break
                                                @case('no_show')
                                                    <span class="badge bg-secondary">Không đến</span>
                                                    @break
                                                @default
                                                    <span class="badge bg-secondary">{{ $booking->status }}</span>
                                            @endswitch
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="7" class="text-center">Không có dữ liệu</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Thống kê theo trạng thái</h6>
                    </div>
                    <div class="card-body">
                        <div class="chart-pie pt-4">
                            <canvas id="statusPieChart"></canvas>
                        </div>
                        <div class="mt-4 text-center small">
                            <span class="mr-2">
                                <i class="fas fa-circle text-warning"></i> Chờ xác nhận
                            </span>
                            <span class="mr-2">
                                <i class="fas fa-circle text-success"></i> Đã xác nhận
                            </span>
                            <span class="mr-2">
                                <i class="fas fa-circle text-info"></i> Đã nhận phòng
                            </span>
                            <span class="mr-2">
                                <i class="fas fa-circle text-secondary"></i> Đã trả phòng
                            </span>
                            <span class="mr-2">
                                <i class="fas fa-circle text-primary"></i> Hoàn thành
                            </span>
                            <span class="mr-2">
                                <i class="fas fa-circle text-danger"></i> Đã hủy
                            </span>
                            <span class="mr-2">
                                <i class="fas fa-circle text-dark"></i> Không đến
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script>
    // Dữ liệu cho biểu đồ
    const statusData = {
        pending: {{ $statusStats['pending']['count'] ?? 0 }},
        confirmed: {{ $statusStats['confirmed']['count'] ?? 0 }},
        checked_in: {{ $statusStats['checked_in']['count'] ?? 0 }},
        checked_out: {{ $statusStats['checked_out']['count'] ?? 0 }},
        completed: {{ $statusStats['completed']['count'] ?? 0 }},
        cancelled: {{ $statusStats['cancelled']['count'] ?? 0 }},
        noShow: {{ $statusStats['no_show']['count'] ?? 0 }}
    };
    
    // Tạo biểu đồ tròn
    const ctx = document.getElementById('statusPieChart').getContext('2d');
    new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: ['Chờ xác nhận', 'Đã xác nhận', 'Đã nhận phòng', 'Đã trả phòng', 'Hoàn thành', 'Đã hủy', 'Không đến'],
            datasets: [{
                data: [statusData.pending, statusData.confirmed, statusData.checked_in, statusData.checked_out, statusData.completed, statusData.cancelled, statusData.noShow],
                backgroundColor: ['#f6c23e', '#1cc88a', '#36b9cc', '#858796', '#28a745', '#e74a3b', '#6c757d'],
                hoverBackgroundColor: ['#daa520', '#169b6b', '#2596be', '#6e707e', '#1e7e34', '#c0392b', '#545b62'],
                hoverBorderColor: "rgba(234, 236, 244, 1)",
            }],
        },
        options: {
            maintainAspectRatio: false,
            tooltips: {
                backgroundColor: "rgb(255,255,255)",
                bodyFontColor: "#858796",
                borderColor: '#dddfeb',
                borderWidth: 1,
                xPadding: 15,
                yPadding: 15,
                displayColors: false,
                caretPadding: 10,
            },
            legend: {
                display: false
            },
            cutoutPercentage: 80,
        },
    });
    
    // Khởi tạo date picker
    flatpickr("#from_date", {
        dateFormat: "Y-m-d",
    });
    
    flatpickr("#to_date", {
        dateFormat: "Y-m-d",
    });
</script>
@endsection 