@extends('admin.layouts.admin-master')

@section('title', 'Chi Tiết Khuyến Mại')

@section('header', 'Chi Tiết Khuyến Mại')

@section('content')
    <div class="container-fluid px-4">
        <ol class="breadcrumb mb-4">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.promotions.index') }}">Quản lý khuyến mại</a></li>
            <li class="breadcrumb-item active">Chỉnh sửa</li>
        </ol>
        <div>
            <a href="{{ route('admin.promotions.edit', $promotion->id) }}" class="btn btn-warning">
                <i class="fas fa-edit"></i> Chỉnh sửa
            </a>
            <a href="{{ route('admin.promotions.index') }}" class="btn btn-secondary ms-2">
                <i class="fas fa-arrow-left"></i> Quay lại
            </a>
        </div>
        <div class="row">
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header">
                        <h6 class="mb-0">{{ $promotion->title }}</h6>
                    </div>
                    <div class="card-body">
                        @if($promotion->image)
                            <div class="text-center mb-4">
                                <img src="{{ asset('storage/' . $promotion->image) }}" alt="{{ $promotion->title }}"
                                    class="img-fluid rounded" style="max-height: 300px;">
                            </div>
                        @endif

                        <div class="row">
                            <div class="col-md-6">
                                <h6>Thông tin cơ bản</h6>
                                <table class="table table-borderless">
                                    <tr>
                                        <td><strong>Mã khuyến mại:</strong></td>
                                        <td>
                                            <span class="badge bg-primary">{{ $promotion->code }}</span>
                                            <button class="btn btn-sm btn-outline-secondary ms-2"
                                                onclick="copyToClipboard('{{ $promotion->code }}')">
                                                <i class="fas fa-copy"></i>
                                            </button>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><strong>Loại giảm giá:</strong></td>
                                        <td>
                                            {{ $promotion->discount_type == 'percentage' ? 'Phần trăm' : 'Cố định' }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><strong>Giá trị giảm:</strong></td>
                                        <td>
                                            <div class="text-center">
                                                <span
                                                    class="badge bg-success text-white">{{ $promotion->discount_text }}</span>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><strong>Đơn tối thiểu:</strong></td>
                                        <td>
                                            @if($promotion->minimum_amount > 0)
                                                {{ number_format($promotion->minimum_amount, 0, ',', '.') }}đ
                                            @else
                                                <span class="text-muted">Không yêu cầu</span>
                                            @endif
                                        </td>
                                    </tr>
                                </table>
                            </div>
                            <div class="col-md-6">
                                <h6>Thời gian & giới hạn</h6>
                                <table class="table table-borderless">
                                    <tr>
                                        <td><strong>Ngày tạo:</strong></td>
                                        <td>{{ $promotion->created_at->format('d/m/Y H:i') }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Ngày bắt đầu:</strong></td>
                                        <td>
                                            @if($promotion->valid_from)
                                                <span
                                                    class="{{ $promotion->valid_from > now() ? 'text-warning' : 'text-success' }}">
                                                    {{ $promotion->valid_from->format('d/m/Y') }}
                                                </span>
                                                <br>
                                                <small class="text-muted">{{ $promotion->valid_from->diffForHumans() }}</small>
                                            @else
                                                <span class="text-muted">Có hiệu lực ngay</span>
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><strong>Ngày hết hạn:</strong></td>
                                        <td>
                                            <span
                                                class="{{ $promotion->expired_at < now() ? 'text-danger' : 'text-success' }}">
                                                {{ $promotion->expired_at->format('d/m/Y') }}
                                            </span>
                                            <br>
                                            <small class="text-muted">{{ $promotion->expired_at->diffForHumans() }}</small>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><strong>Giới hạn sử dụng:</strong></td>
                                        <td>
                                            @if($promotion->usage_limit)
                                                {{ $promotion->usage_limit }} lần
                                            @else
                                                <span class="text-muted">Không giới hạn</span>
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><strong>Trạng thái:</strong></td>
                                        <td>
                                            <span class="badge bg-{{ $promotion->is_active ? 'success' : 'secondary' }}">
                                                {{ $promotion->status }}
                                            </span>
                                            @if($promotion->is_featured)
                                                <span class="badge bg-warning ms-1">Nổi bật</span>
                                            @endif
                                            @if($promotion->can_combine)
                                                <span class="badge bg-info text-white ms-1">
                                                    <i class="fas fa-layer-group"></i> Có thể dùng gộp
                                                </span>
                                            @endif
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </div>

                        <hr>

                        <div class="mb-4">
                            <h6>Mô tả</h6>
                            <p class="text-muted">{{ $promotion->description }}</p>
                        </div>

                        <!-- Phạm vi áp dụng -->
                        <div class="mb-4">
                            <h6>Phạm vi áp dụng khuyến mại</h6>

                            @if($promotion->roomTypes && $promotion->roomTypes->count() > 0)
                                {{-- Hiển thị theo loại phòng --}}
                                <div class="alert alert-info">
                                    <i class="fas fa-layer-group"></i>
                                    Áp dụng cho <strong>{{ $promotion->roomTypes->count() }} loại phòng</strong>
                                </div>

                                <div class="row">
                                    @foreach($promotion->roomTypes as $roomType)
                                        <div class="col-md-6 mb-2">
                                            <div class="card bg-light border-info">
                                                <div class="card-body py-2">
                                                    <h6 class="card-title mb-1 text-info">
                                                        <i class="fas fa-layer-group"></i> {{ $roomType->name }}
                                                    </h6>
                                                    <small class="text-muted">
                                                        {{ number_format($roomType->price, 0, ',', '.') }}đ/đêm
                                                        • {{ $roomType->rooms->count() }} phòng
                                                    </small>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>

                            @else
                                {{-- Áp dụng cho tất cả --}}
                                <div class="alert alert-primary">
                                    <i class="fas fa-globe"></i>
                                    Khuyến mại này áp dụng cho <strong>tất cả phòng</strong> trong khách sạn
                                </div>
                            @endif
                        </div>

                        @if($promotion->minimum_amount > 0)
                            <div class="alert alert-warning">
                                <i class="fas fa-info-circle"></i>
                                <strong>Lưu ý:</strong> Khuyến mại này chỉ áp dụng khi đơn hàng có tổng giá trị từ
                                {{ number_format($promotion->minimum_amount, 0, ',', '.') }}đ trở lên.
                            </div>
                        @endif

                        @if($promotion->terms_conditions)
                            <div class="mb-4">
                                <h6>Điều khoản và điều kiện</h6>
                                <div class="alert alert-warning">
                                    <i class="fas fa-exclamation-triangle"></i>
                                    <strong>Lưu ý quan trọng:</strong>
                                </div>
                                <div class="border-start border-3 border-warning ps-3">
                                    {!! nl2br(e($promotion->terms_conditions)) !!}
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="col-lg-4">
                <div class="card">
                    <div class="card-header">
                        <h6 class="mb-0">Thống kê sử dụng</h6>
                    </div>
                    <div class="card-body">
                        <div class="row text-center">
                            <div class="col-6">
                                <div class="border-end">
                                    <h3 class="text-primary">{{ $promotion->used_count }}</h3>
                                    <small class="text-muted">Đã sử dụng</small>
                                </div>
                            </div>
                            <div class="col-6">
                                <h3 class="text-success">
                                    @if($promotion->usage_limit)
                                        {{ $promotion->usage_limit - $promotion->used_count }}
                                    @else
                                        ∞
                                    @endif
                                </h3>
                                <small class="text-muted">Còn lại</small>
                            </div>
                        </div>

                        @if($promotion->usage_limit)
                            <div class="mt-3">
                                <div class="d-flex justify-content-between mb-1">
                                    <span class="small">Tiến độ sử dụng</span>
                                    <span
                                        class="small">{{ number_format(($promotion->used_count / $promotion->usage_limit) * 100, 1) }}%</span>
                                </div>
                                <div class="progress">
                                    <div class="progress-bar" role="progressbar"
                                        style="width: {{ ($promotion->used_count / $promotion->usage_limit) * 100 }}%">
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>



                <div class="card mt-3">
                    <div class="card-header">
                        <h6 class="mb-0">Xem trước trang khách hàng</h6>
                    </div>
                    <div class="card-body">
                        <div class="promotion-preview">
                            <div class="text-center mb-3">
                                @if($promotion->image)
                                    <img src="{{ asset('storage/' . $promotion->image) }}" alt="{{ $promotion->title }}"
                                        style="width: 80px; height: 80px; object-fit: cover; border-radius: 50%;">
                                @else
                                    <div class="bg-primary text-white d-inline-flex align-items-center justify-content-center"
                                        style="width: 80px; height: 80px; border-radius: 50%;">
                                        <i class="fas fa-percentage fa-2x"></i>
                                    </div>
                                @endif
                            </div>
                            <h6 class="text-center">{{ $promotion->title }}</h6>
                            <p class="text-center text-muted small">{{ Str::limit($promotion->description, 60) }}</p>
                            <div class="text-center">
                                <span class="badge bg-success text-white">{{ $promotion->discount_text }}</span>
                            </div>
                            <div class="mt-2 text-center">
                                <small class="text-muted">Mã: <strong>{{ $promotion->code }}</strong></small>
                            </div>
                            <div class="mt-3 text-center">
                                <a href="{{ route('promotions.show', $promotion->id) }}" target="_blank"
                                    class="btn btn-outline-primary btn-sm">
                                    <i class="fas fa-external-link-alt"></i> Xem trên trang khách
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @push('scripts')
            <script>
                function copyToClipboard(text) {
                    if (navigator.clipboard) {
                        navigator.clipboard.writeText(text);
                    }
                }

                $(document).ready(function () {
                    // JavaScript for show page (if needed)
                });
            </script>
        @endpush
@endsection