@extends('admin.layouts.admin-master')

@section('header', 'Thống kê đánh giá loại phòng')

@section('content')
<div class="container-fluid px-4">
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="{{ route('admin.room-type-reviews.index') }}">Quản lý đánh giá loại phòng</a></li>
        <li class="breadcrumb-item active">Thống kê</li>
    </ol>
    
    <div class="card mb-4">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <i class="fas fa-chart-bar me-1"></i>
                    Thống kê đánh giá loại phòng
                </div>
                <a href="{{ route('admin.room-type-reviews.index') }}" class="btn btn-secondary btn-sm">
                    <i class="fas fa-arrow-left"></i> Quay lại
                </a>
            </div>
        </div>
                
                <div class="card-body">
            <!-- Tổng quan -->
            <div class="row mb-4">
                        <div class="col-md-3">
                            <div class="card bg-primary text-white">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <h4 class="mb-0">{{ $statistics['total_reviews'] }}</h4>
                                            <p class="mb-0">Tổng đánh giá</p>
                                        </div>
                                        <div class="align-self-center">
                                            <i class="fas fa-star fa-2x"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-3">
                            <div class="card bg-warning text-white">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <h4 class="mb-0">{{ $statistics['pending_reviews_count'] }}</h4>
                                            <p class="mb-0">Chờ duyệt</p>
                                        </div>
                                        <div class="align-self-center">
                                            <i class="fas fa-clock fa-2x"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-3">
                            <div class="card bg-success text-white">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <h4 class="mb-0">{{ $statistics['approved_reviews_count'] }}</h4>
                                            <p class="mb-0">Đã duyệt</p>
                                        </div>
                                        <div class="align-self-center">
                                            <i class="fas fa-check fa-2x"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-3">
                            <div class="card bg-danger text-white">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <h4 class="mb-0">{{ $statistics['rejected_reviews_count'] }}</h4>
                                            <p class="mb-0">Đã từ chối</p>
                                        </div>
                                        <div class="align-self-center">
                                            <i class="fas fa-times fa-2x"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Điểm đánh giá trung bình -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="mb-0"><i class="fas fa-star"></i> Điểm đánh giá trung bình</h5>
                                </div>
                                <div class="card-body text-center">
                                    <div class="display-4 text-warning mb-2">
                                        {{ number_format($statistics['average_rating'], 1) }}
                                    </div>
                                    <div class="text-warning mb-2">
                                        @for ($i = 1; $i <= 5; $i++)
                                            <i class="fas fa-star{{ $i <= round($statistics['average_rating']) ? '' : '-o' }}"></i>
                                        @endfor
                                    </div>
                                    <p class="text-muted">Dựa trên {{ $statistics['approved_reviews_count'] }} đánh giá đã duyệt</p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="mb-0"><i class="fas fa-chart-pie"></i> Phân bố điểm đánh giá</h5>
                                </div>
                                <div class="card-body">
                                    @for($i = 5; $i >= 1; $i--)
                                        <div class="d-flex align-items-center mb-2">
                                            <div class="text-warning mr-2">
                                                @for($j = 1; $j <= 5; $j++)
                                                    <i class="fas fa-star{{ $j <= $i ? '' : '-o' }}"></i>
                                                @endfor
                                            </div>
                                            <div class="progress flex-grow-1 mr-2" style="height: 20px;">
                                                @php
                                                    $percentage = $statistics['approved_reviews_count'] > 0 
                                                        ? ($statistics['rating_stats'][$i] / $statistics['approved_reviews_count']) * 100 
                                                        : 0;
                                                @endphp
                                                <div class="progress-bar bg-warning" style="width: {{ $percentage }}%"></div>
                                            </div>
                                            <span class="text-muted">{{ $statistics['rating_stats'][$i] }}</span>
                                        </div>
                                    @endfor
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Thống kê đánh giá chi tiết -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="mb-0"><i class="fas fa-chart-line"></i> Thống kê đánh giá chi tiết theo tiêu chí</h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <h6 class="text-center mb-3">Vệ sinh</h6>
                                            <div class="text-center">
                                                <div class="display-6 text-success mb-2">
                                                    {{ number_format($statistics['detailed_stats']['cleanliness'] ?? 0, 1) }}
                                                </div>
                                                <div class="text-warning">
                                                    @for ($i = 1; $i <= 5; $i++)
                                                        <i class="fas fa-star{{ $i <= round($statistics['detailed_stats']['cleanliness'] ?? 0) ? '' : '-o' }}"></i>
                                                    @endfor
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <h6 class="text-center mb-3">Tiện nghi</h6>
                                            <div class="text-center">
                                                <div class="display-6 text-success mb-2">
                                                    {{ number_format($statistics['detailed_stats']['comfort'] ?? 0, 1) }}
                                                </div>
                                                <div class="text-warning">
                                                    @for ($i = 1; $i <= 5; $i++)
                                                        <i class="fas fa-star{{ $i <= round($statistics['detailed_stats']['comfort'] ?? 0) ? '' : '-o' }}"></i>
                                                    @endfor
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <h6 class="text-center mb-3">Vị trí</h6>
                                            <div class="text-center">
                                                <div class="display-6 text-success mb-2">
                                                    {{ number_format($statistics['detailed_stats']['location'] ?? 0, 1) }}
                                                </div>
                                                <div class="text-warning">
                                                    @for ($i = 1; $i <= 5; $i++)
                                                        <i class="fas fa-star{{ $i <= round($statistics['detailed_stats']['location'] ?? 0) ? '' : '-o' }}"></i>
                                                    @endfor
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row mt-3">
                                        <div class="col-md-6">
                                            <h6 class="text-center mb-3">Cơ sở vật chất</h6>
                                            <div class="text-center">
                                                <div class="display-6 text-success mb-2">
                                                    {{ number_format($statistics['detailed_stats']['facilities'] ?? 0, 1) }}
                                                </div>
                                                <div class="text-warning">
                                                    @for ($i = 1; $i <= 5; $i++)
                                                        <i class="fas fa-star{{ $i <= round($statistics['detailed_stats']['facilities'] ?? 0) ? '' : '-o' }}"></i>
                                                    @endfor
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <h6 class="text-center mb-3">Giá trị</h6>
                                            <div class="text-center">
                                                <div class="display-6 text-success mb-2">
                                                    {{ number_format($statistics['detailed_stats']['value'] ?? 0, 1) }}
                                                </div>
                                                <div class="text-warning">
                                                    @for ($i = 1; $i <= 5; $i++)
                                                        <i class="fas fa-star{{ $i <= round($statistics['detailed_stats']['value'] ?? 0) ? '' : '-o' }}"></i>
                                                    @endfor
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Top loại phòng được đánh giá -->
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="mb-0"><i class="fas fa-trophy"></i> Top loại phòng được đánh giá nhiều nhất</h5>
                                </div>
                                <div class="card-body">
                                    @if(count($statistics['top_room_types']) > 0)
                                        <div class="table-responsive">
                                            <table class="table table-hover">
                                                <thead class="table-light">
                                                    <tr>
                                                        <th>#</th>
                                                        <th>Loại phòng</th>
                                                        <th>Số đánh giá</th>
                                                        <th>Điểm trung bình</th>
                                                        <th>Giá</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($statistics['top_room_types'] as $index => $roomType)
                                                        <tr>
                                                            <td>{{ $index + 1 }}</td>
                                                            <td>
                                                                <strong>{{ $roomType->name }}</strong>
                                                                @if($roomType->description)
                                                                    <br>
                                                                    <small class="text-muted">{{ Str::limit($roomType->description, 50) }}</small>
                                                                @endif
                                                            </td>
                                                            <td>
                                                                <span class="badge badge-primary">{{ $roomType->approved_reviews_count }}</span>
                                                            </td>
                                                            <td>
                                                                <div class="text-warning">
                                                                    @for ($i = 1; $i <= 5; $i++)
                                                                        <i class="fas fa-star{{ $i <= round($roomType->average_rating) ? '' : '-o' }}"></i>
                                                                    @endfor
                                                                </div>
                                                                <small class="text-muted">{{ number_format($roomType->average_rating, 1) }}/5</small>
                                                            </td>
                                                            <td>{{ number_format($roomType->price) }}đ/đêm</td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    @else
                                        <div class="text-center py-4">
                                            <i class="fas fa-info-circle fa-2x text-muted mb-3"></i>
                                            <p class="text-muted">Chưa có đánh giá nào cho loại phòng nào.</p>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Thống kê theo thời gian -->
                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="mb-0"><i class="fas fa-calendar"></i> Thống kê đánh giá theo tháng</h5>
                                </div>
                                <div class="card-body">
                                    <div class="alert alert-info">
                                        <i class="fas fa-info-circle"></i>
                                        Tính năng thống kê theo thời gian sẽ được phát triển trong phiên bản tiếp theo.
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.card {
    transition: transform 0.2s ease-in-out;
}

.card:hover {
    transform: translateY(-2px);
}

.progress {
    background-color: #f8f9fa;
}

.progress-bar {
    transition: width 0.6s ease;
}

.display-4 {
    font-size: 3rem;
    font-weight: 300;
    line-height: 1.2;
}

.badge {
    font-size: 0.875em;
}
</style>
@endsection 