@extends('admin.layouts.admin-master')

@section('header', 'Chi tiết đánh giá loại phòng')

@section('content')
<div class="container-fluid px-4">
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="{{ route('admin.room-type-reviews.index') }}">Quản lý đánh giá loại phòng</a></li>
        <li class="breadcrumb-item active">Chi tiết đánh giá</li>
    </ol>
    
    <div class="card mb-4">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <i class="fas fa-star me-1"></i>
                    Chi tiết đánh giá #{{ $review->id }}
                </div>
                <a href="{{ route('admin.room-type-reviews.index') }}" class="btn btn-secondary btn-sm">
                    <i class="fas fa-arrow-left"></i> Quay lại
                </a>
            </div>
        </div>
                
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <h5 class="text-info mb-3"><i class="fas fa-star me-2"></i>Thông Tin Đánh Giá</h5>
                            <table class="table table-borderless">
                                <tr>
                                    <td><strong>ID:</strong></td>
                                    <td>{{ $review->id }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Điểm đánh giá:</strong></td>
                                    <td>
                                        <div class="text-warning">
                                            @for ($i = 1; $i <= 5; $i++)
                                                <i class="fas fa-star{{ $i <= $review->rating ? '' : '-o' }}"></i>
                                            @endfor
                                            <span class="ml-2">{{ $review->rating }}/5</span>
                                        </div>
                                    </td>
                                </tr>
                                @if($review->cleanliness_rating || $review->comfort_rating || $review->location_rating || $review->facilities_rating || $review->value_rating)
                                    <tr>
                                        <td><strong>Đánh giá chi tiết:</strong></td>
                                        <td>
                                            <div class="row">
                                                @if($review->cleanliness_rating)
                                                    <div class="col-md-6 mb-2">
                                                        <small><strong>Vệ sinh:</strong> {{ $review->cleanliness_rating }}/5</small>
                                                    </div>
                                                @endif
                                                @if($review->comfort_rating)
                                                    <div class="col-md-6 mb-2">
                                                        <small><strong>Tiện nghi:</strong> {{ $review->comfort_rating }}/5</small>
                                                    </div>
                                                @endif
                                                @if($review->location_rating)
                                                    <div class="col-md-6 mb-2">
                                                        <small><strong>Vị trí:</strong> {{ $review->location_rating }}/5</small>
                                                    </div>
                                                @endif
                                                @if($review->facilities_rating)
                                                    <div class="col-md-6 mb-2">
                                                        <small><strong>Cơ sở vật chất:</strong> {{ $review->facilities_rating }}/5</small>
                                                    </div>
                                                @endif
                                                @if($review->value_rating)
                                                    <div class="col-md-6 mb-2">
                                                        <small><strong>Giá trị:</strong> {{ $review->value_rating }}/5</small>
                                                    </div>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @endif
                                <tr>
                                    <td><strong>Trạng thái:</strong></td>
                                    <td>
                                        <span class="badge badge-{{ $review->status == 'approved' ? 'success' : ($review->status == 'rejected' ? 'danger' : 'warning') }}">
                                            {{ $review->status_text }}
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Ẩn danh:</strong></td>
                                    <td>{{ $review->is_anonymous ? 'Có' : 'Không' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Ngày tạo:</strong></td>
                                    <td>{{ $review->created_at->format('d/m/Y H:i') }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Cập nhật lần cuối:</strong></td>
                                    <td>{{ $review->updated_at->format('d/m/Y H:i') }}</td>
                                </tr>
                            </table>
                        </div>
                        
                <div class="col-md-6">
                    <h5 class="text-info mb-3"><i class="fas fa-user me-2"></i>Thông Tin Người Đánh Giá</h5>
                            <table class="table table-borderless">
                                <tr>
                                    <td><strong>Họ tên:</strong></td>
                                    <td>{{ $review->user->name }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Email:</strong></td>
                                    <td>{{ $review->user->email }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Số điện thoại:</strong></td>
                                    <td>{{ $review->user->phone ?? 'Chưa cung cấp' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Ngày tham gia:</strong></td>
                                    <td>{{ $review->user->created_at->format('d/m/Y') }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                    
            <div class="row mt-4">
                <div class="col-12">
                    <h5 class="text-info mb-3"><i class="fas fa-bed me-2"></i>Thông Tin Loại Phòng</h5>
                            <table class="table table-borderless">
                                <tr>
                                    <td><strong>Tên loại phòng:</strong></td>
                                    <td>{{ $review->roomType->name }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Mô tả:</strong></td>
                                    <td>{{ $review->roomType->description ?? 'Không có mô tả' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Giá:</strong></td>
                                    <td>{{ number_format($review->roomType->price) }}đ/đêm</td>
                                </tr>
                                <tr>
                                    <td><strong>Sức chứa:</strong></td>
                                    <td>{{ $review->roomType->capacity ?? 'Không xác định' }} người</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                    
            @if($review->comment)
                <div class="row mt-4">
                    <div class="col-12">
                        <h5 class="text-info mb-3"><i class="fas fa-comment me-2"></i>Nội Dung Đánh Giá</h5>
                                <div class="card">
                                    <div class="card-body">
                                        <p class="mb-0">{{ $review->comment }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                    
            <div class="row mt-4">
                <div class="col-12">
                    <h5 class="text-info mb-3"><i class="fas fa-cogs me-2"></i>Hành Động</h5>
                            <div class="btn-group" role="group">
                                @if($review->status == 'pending')
                                    <form action="{{ route('admin.room-type-reviews.approve', $review->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="btn btn-success">
                                            <i class="fas fa-check"></i> Duyệt đánh giá
                                        </button>
                                    </form>
                                    
                                    <form action="{{ route('admin.room-type-reviews.reject', $review->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="btn btn-warning">
                                            <i class="fas fa-times"></i> Từ chối đánh giá
                                        </button>
                                    </form>
                                @endif
                                
                                <a href="{{ route('admin.room-type-reviews.index') }}" class="btn btn-secondary">
                                    <i class="fas fa-arrow-left"></i> Quay lại danh sách
                                </a>
                                
                                <form action="{{ route('admin.room-type-reviews.destroy', $review->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Bạn có chắc chắn muốn xóa đánh giá này?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger">
                                        <i class="fas fa-trash"></i> Xóa đánh giá
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        // Các mã JavaScript tùy chỉnh ở đây
    });
</script>
@endsection 