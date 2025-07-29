@extends('admin.layouts.admin-master')

@section('header', 'Quản lý đánh giá loại phòng')

@section('content')
<div class="container-fluid px-4">
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item active">Quản lý đánh giá loại phòng</li>
    </ol>
    
    <div class="card mb-4">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <i class="fas fa-star me-1"></i>
                    Danh sách đánh giá loại phòng
                </div>
                <div>
                    <a href="{{ route('admin.room-type-reviews.create') }}" class="btn btn-primary btn-sm">
                        <i class="fas fa-plus"></i> Tạo đánh giá mới
                    </a>
                    <a href="{{ route('admin.room-type-reviews.statistics') }}" class="btn btn-info btn-sm">
                        <i class="fas fa-chart-bar"></i> Thống kê
                    </a>
                </div>
            </div>
        </div>
                
        <div class="card-body">
            <div class="mb-3">
                <form action="{{ route('admin.room-type-reviews.index') }}" method="GET" class="row g-3">
                    <div class="col-auto">
                        <label for="status" class="col-form-label">Lọc theo trạng thái:</label>
                    </div>
                    <div class="col-auto">
                        <select name="status" id="status" class="form-select" onchange="this.form.submit()">
                            <option value="">Tất cả</option>
                            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Chờ duyệt</option>
                            <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Đã duyệt</option>
                            <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Đã từ chối</option>
                        </select>
                    </div>
                    <div class="col-auto">
                        <label for="room_type" class="col-form-label">Loại phòng:</label>
                    </div>
                    <div class="col-auto">
                        <select name="room_type" id="room_type" class="form-select" onchange="this.form.submit()">
                            <option value="">Tất cả</option>
                            @foreach($roomTypes as $roomType)
                                <option value="{{ $roomType->id }}" {{ request('room_type') == $roomType->id ? 'selected' : '' }}>
                                    {{ $roomType->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-auto">
                        <label for="rating" class="col-form-label">Điểm đánh giá:</label>
                    </div>
                    <div class="col-auto">
                        <select name="rating" id="rating" class="form-select" onchange="this.form.submit()">
                            <option value="">Tất cả</option>
                            @for($i = 1; $i <= 5; $i++)
                                <option value="{{ $i }}" {{ request('rating') == $i ? 'selected' : '' }}>{{ $i }} sao</option>
                            @endfor
                        </select>
                    </div>
                </form>
            </div>

            <div class="table-responsive">
                <table class="table table-bordered table-striped" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>STT</th>
                            <th>Người đánh giá</th>
                            <th>Loại phòng</th>
                            <th>Điểm</th>
                            <th>Bình luận</th>
                            <th>Trạng thái</th>
                            <th>Ngày tạo</th>
                            <th>Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($reviews as $review)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                                            <td>
                                                <div>
                                                    <strong>{{ $review->user->name }}</strong>
                                                    <br>
                                                    <small class="text-muted">{{ $review->user->email }}</small>
                                                    @if($review->is_anonymous)
                                                        <br>
                                                        <span class="badge badge-secondary">Ẩn danh</span>
                                                    @endif
                                                </div>
                                            </td>
                                            <td>
                                                <strong>{{ $review->roomType->name }}</strong>
                                                <br>
                                                <small class="text-muted">{{ number_format($review->roomType->price) }}đ/đêm</small>
                                            </td>
                                            <td>
                                                <div class="text-warning">
                                                    @for ($i = 1; $i <= 5; $i++)
                                                        <i class="fas fa-star{{ $i <= $review->rating ? '' : '-o' }}"></i>
                                                    @endfor
                                                </div>
                                                <small class="text-muted">{{ $review->rating }}/5</small>
                                                @if($review->cleanliness_rating || $review->comfort_rating || $review->location_rating || $review->facilities_rating || $review->value_rating)
                                                    <div class="mt-1">
                                                        <small class="text-muted">
                                                            @if($review->cleanliness_rating)
                                                                <span class="mr-1">Vệ sinh: {{ $review->cleanliness_rating }}/5</span>
                                                            @endif
                                                            @if($review->comfort_rating)
                                                                <span class="mr-1">Tiện nghi: {{ $review->comfort_rating }}/5</span>
                                                            @endif
                                                            @if($review->location_rating)
                                                                <span class="mr-1">Vị trí: {{ $review->location_rating }}/5</span>
                                                            @endif
                                                            @if($review->facilities_rating)
                                                                <span class="mr-1">CSVC: {{ $review->facilities_rating }}/5</span>
                                                            @endif
                                                            @if($review->value_rating)
                                                                <span>Giá trị: {{ $review->value_rating }}/5</span>
                                                            @endif
                                                        </small>
                                                    </div>
                                                @endif
                                            </td>
                                            <td>
                                                @if($review->comment)
                                                    <div class="text-truncate" style="max-width: 200px;" title="{{ $review->comment }}">
                                                        {{ Str::limit($review->comment, 100) }}
                                                    </div>
                                                @else
                                                    <span class="text-muted"><em>Không có bình luận</em></span>
                                                @endif
                                            </td>
                            <td>
                                <span class="badge bg-{{ $review->status == 'approved' ? 'success' : ($review->status == 'rejected' ? 'danger' : 'warning') }}">
                                    {{ $review->status_text }}
                                </span>
                            </td>
                                            <td>{{ $review->created_at->format('d/m/Y H:i') }}</td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="{{ route('admin.room-type-reviews.show', $review->id) }}" class="btn btn-sm btn-info">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    
                                    @if($review->status == 'pending')
                                        <form action="{{ route('admin.room-type-reviews.approve', $review->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Bạn có chắc chắn muốn duyệt đánh giá này?')">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="btn btn-sm btn-success">
                                                <i class="fas fa-check"></i>
                                            </button>
                                        </form>
                                        
                                        <form action="{{ route('admin.room-type-reviews.reject', $review->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Bạn có chắc chắn muốn từ chối đánh giá này?')">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="btn btn-sm btn-warning">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        </form>
                                    @endif
                                    
                                    <form action="{{ route('admin.room-type-reviews.destroy', $review->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Bạn có chắc chắn muốn xóa đánh giá này?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                </tbody>
            </table>
        </div>

        <div class="d-flex justify-content-center mt-3">
            {{ $reviews->appends(request()->query())->links() }}
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
