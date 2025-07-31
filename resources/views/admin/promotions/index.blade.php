@extends('admin.layouts.admin-master')

@section('title', 'Quản Lý Khuyến Mại')

@section('header', 'Quản lý khuyến mại')

@section('content')
<div class="container-fluid px-4">
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item active">Quản lý khuyến mại</li>
    </ol>


{{-- Stats Cards --}}
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card">
            <div class="card-body text-center">
                <i class="fas fa-tags fa-2x text-primary mb-2"></i>
                <h3 class="text-primary">{{ $stats['total'] }}</h3>
                <p class="text-muted mb-0">Tổng khuyến mại</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card">
            <div class="card-body text-center">
                <i class="fas fa-check-circle fa-2x text-success mb-2"></i>
                <h3 class="text-success">{{ $stats['active'] }}</h3>
                <p class="text-muted mb-0">Đang hoạt động</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card">
            <div class="card-body text-center">
                <i class="fas fa-star fa-2x text-warning mb-2"></i>
                <h3 class="text-warning">{{ $stats['featured'] }}</h3>
                <p class="text-muted mb-0">Nổi bật</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card">
            <div class="card-body text-center">
                <i class="fas fa-clock fa-2x text-danger mb-2"></i>
                <h3 class="text-danger">{{ $stats['expired'] }}</h3>
                <p class="text-muted mb-0">Đã hết hạn</p>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h6 class="mb-0">Danh sách khuyến mại</h6>
        <a href="{{ route('admin.promotions.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Thêm mới
        </a>
    </div>
    <div class="card-body">
        {{-- Filters --}}
        <form method="GET" action="{{ route('admin.promotions.index') }}" class="mb-4">
            <div class="row g-3">
                <div class="col-md-3">
                    <select name="status" class="form-control">
                        <option value="">Tất cả trạng thái</option>
                        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Hoạt động</option>
                        <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Không hoạt động</option>
                        <option value="expired" {{ request('status') == 'expired' ? 'selected' : '' }}>Hết hạn</option>
                        <option value="valid" {{ request('status') == 'valid' ? 'selected' : '' }}>Còn hiệu lực</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <select name="discount_type" class="form-control">
                        <option value="">Tất cả loại</option>
                        <option value="percentage" {{ request('discount_type') == 'percentage' ? 'selected' : '' }}>Phần trăm</option>
                        <option value="fixed" {{ request('discount_type') == 'fixed' ? 'selected' : '' }}>Cố định</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <input type="text" name="search" class="form-control" placeholder="Tìm kiếm..." value="{{ request('search') }}">
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-outline-primary w-100">
                        <i class="fas fa-search"></i> Tìm
                    </button>
                </div>
            </div>
        </form>

        {{-- Table --}}
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Hình ảnh</th>
                        <th>Thông tin</th>
                        <th>Giảm giá</th>
                        <th>Hạn sử dụng</th>
                        <th>Loại phòng</th>
                        <th>Trạng thái HĐ</th>
                        <th>Thống kê</th>
                        <th>Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($promotions as $promotion)
                        <tr>
                            <td>
                                @if($promotion->image)
                                    <img src="{{ asset('storage/' . $promotion->image) }}" alt="{{ $promotion->title }}" class="img-thumbnail" style="width: 60px; height: 60px; object-fit: cover;">
                                @else
                                    <div class="bg-primary text-white d-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                                        <i class="fas fa-percentage"></i>
                                    </div>
                                @endif
                            </td>
                            <td>
                                <div>
                                    <strong>{{ $promotion->title }}</strong>
                                    @if($promotion->is_featured)
                                        <span class="badge bg-warning text-dark ms-1">⭐ Nổi bật</span>
                                    @endif
                                </div>
                                <small class="text-muted">{{ Str::limit($promotion->description, 50) }}</small><br>
                                <small><strong>Mã:</strong> {{ $promotion->code }}</small>
                            </td>
                            <td>
                                <span class="badge bg-success text-white">{{ $promotion->discount_text }}</span><br>
                                @if($promotion->minimum_amount > 0)
                                    <small class="text-muted">Tối thiểu: {{ number_format($promotion->minimum_amount, 0, ',', '.') }}đ</small>
                                @endif
                            </td>
                            <td>
                                @if($promotion->valid_from)
                                    <small class="text-muted">Từ: {{ $promotion->valid_from->format('d/m/Y') }}</small><br>
                                @endif
                                <span class="{{ $promotion->expired_at < now() ? 'text-danger' : 'text-success' }}">
                                    Đến: {{ $promotion->expired_at->format('d/m/Y') }}
                                </span><br>
                                <small class="text-muted">
                                    @if($promotion->valid_from && $promotion->valid_from > now())
                                        {{ $promotion->valid_from->diffForHumans() }}
                                    @else
                                        {{ $promotion->expired_at->diffForHumans() }}
                                    @endif
                                </small>
                            </td>
                            <td>
                                @if($promotion->roomTypes && $promotion->roomTypes->count() > 0)
                                    <small class="text-dark">
                                        <strong>{{ $promotion->roomTypes->count() }} loại phòng</strong>
                                        @if($promotion->roomTypes->count() == 1)
                                            <br><span class="text-secondary">{{ $promotion->roomTypes->first()->name }}</span>
                                        @endif
                                    </small>
                                @else
                                    <span class="badge bg-primary text-white">
                                        <i class="fas fa-globe"></i> Tất cả phòng
                                    </span>
                                @endif
                                
                                @if($promotion->can_combine)
                                    <br><span class="badge bg-info text-white mt-1">
                                        <i class="fas fa-layer-group"></i> Có thể gộp
                                    </span>
                                @endif
                            </td>
                            <td>
                                @php
                                    $isExpired = $promotion->expired_at < now();
                                    $isActive = $promotion->is_active;
                                    $isUpcoming = $promotion->valid_from && $promotion->valid_from > now();
                                    
                                    if ($isExpired) {
                                        $currentStatus = 'expired';
                                    } elseif (!$isActive) {
                                        $currentStatus = 'inactive';
                                    } elseif ($isUpcoming) {
                                        $currentStatus = 'upcoming';
                                    } else {
                                        $currentStatus = 'active';
                                    }
                                @endphp
                                
                                <div class="d-flex flex-column gap-1">
                                    <span class="badge badge-sm {{ $currentStatus === 'inactive' ? 'bg-secondary text-white' : 'bg-light text-dark' }}">
                                        Tạm dừng
                                    </span>
                                    <span class="badge badge-sm {{ $currentStatus === 'upcoming' ? 'bg-warning text-dark' : 'bg-light text-dark' }}">
                                        Sắp diễn ra
                                    </span>
                                    <span class="badge badge-sm {{ $currentStatus === 'active' ? 'bg-success text-white' : 'bg-light text-dark' }}">
                                        Đang hoạt động  
                                    </span>
                                    <span class="badge badge-sm {{ $currentStatus === 'expired' ? 'bg-danger text-white' : 'bg-light text-dark' }}">
                                        Kết thúc
                                    </span>
                                </div>
                            </td>
                            <td>
                                <small>
                                    <strong>Đã dùng:</strong> {{ $promotion->used_count }}<br>
                                    @if($promotion->usage_limit)
                                        <strong>Còn lại:</strong> {{ $promotion->usage_limit - $promotion->used_count }}
                                    @else
                                        <strong>Không giới hạn</strong>
                                    @endif
                                </small>
                            </td>
                            <td>
                                @php
                                    $isExpired = $promotion->expired_at < now();
                                    $isActive = $promotion->is_active;
                                    $isUpcoming = $promotion->valid_from && $promotion->valid_from > now();
                                    $canDelete = !$isExpired && (!$isActive || $isUpcoming);
                                @endphp
                                
                                <div class="btn-group" role="group">
                                    <a href="{{ route('admin.promotions.show', $promotion->id) }}" class="btn btn-sm btn-outline-info">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.promotions.edit', $promotion->id) }}" class="btn btn-sm btn-outline-warning">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    @if($canDelete)
                                        <form method="POST" action="{{ route('admin.promotions.destroy', $promotion->id) }}" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Bạn có chắc muốn xóa khuyến mại này?')">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    @else
                                        <button class="btn btn-sm btn-outline-secondary" disabled 
                                                title="{{ $isExpired ? 'Không thể xóa khuyến mại đã kết thúc' : 'Không thể xóa khuyến mại đang hoạt động' }}">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-4">
                                <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                                <p class="text-muted">Không có khuyến mại nào</p>
                                <a href="{{ route('admin.promotions.create') }}" class="btn btn-primary">
                                    <i class="fas fa-plus"></i> Tạo khuyến mại đầu tiên
                                </a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        <div class="d-flex justify-content-center">
            {{ $promotions->links() }}
        </div>
    </div>
</div>

@push('scripts')
<script>
$(document).ready(function() {
    // Show alert notification
    function showAlert(type, message) {
        const alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
        const iconClass = type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle';
        
        const alert = $(`
            <div class="alert ${alertClass} alert-dismissible fade show position-fixed" 
                 style="top: 80px; right: 20px; z-index: 9999; min-width: 300px; max-width: 500px;">
                <i class="fas ${iconClass} me-2"></i>${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        `);
        
        $('body').append(alert);
        
        // Auto remove after 4 seconds
        setTimeout(function() {
            alert.alert('close');
        }, 4000);
    }
});
</script>
@endpush
@endsection 