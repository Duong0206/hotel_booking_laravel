@extends('admin.layouts.admin-master')

@section('title', 'Chỉnh Sửa Khuyến Mại')

@section('header', 'Chỉnh sửa khuyến mại')

@section('content')
<div class="container-fluid px-4">
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="{{ route('admin.promotions.index') }}">Quản lý khuyến mại</a></li>
        <li class="breadcrumb-item active">Chỉnh sửa</li>
    </ol>

<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <h6 class="mb-0">Thông tin khuyến mại</h6>
                    <a href="{{ route('admin.promotions.index') }}" class="btn btn-outline-secondary btn-sm">
                        <i class="fas fa-list"></i> Danh sách
                    </a>
                </div>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('admin.promotions.update', $promotion->id) }}" enctype="multipart/form-data" id="promotion-form">
                    @csrf
                    @method('PUT')
                    
                    <div class="row">
                        <div class="col-md-8">
                            <div class="mb-3">
                                <label for="title" class="form-label">Tiêu đề <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('title') is-invalid @enderror" 
                                       id="title" name="title" value="{{ old('title', $promotion->title) }}" required>
                                @error('title')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="code" class="form-label">Mã khuyến mại <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('code') is-invalid @enderror" 
                                       id="code" name="code" value="{{ old('code', $promotion->code) }}" 
                                       placeholder="VD: SUMMER2024" required>
                                @error('code')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label">Mô tả <span class="text-danger">*</span></label>
                        <textarea class="form-control @error('description') is-invalid @enderror" 
                                  id="description" name="description" rows="3" required>{{ old('description', $promotion->description) }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="discount_type" class="form-label">Loại giảm giá <span class="text-danger">*</span></label>
                                <select class="form-control @error('discount_type') is-invalid @enderror" 
                                        id="discount_type" name="discount_type" required>
                                    <option value="">Chọn loại giảm giá</option>
                                    <option value="percentage" {{ old('discount_type', $promotion->discount_type) == 'percentage' ? 'selected' : '' }}>Phần trăm (%)</option>
                                    <option value="fixed" {{ old('discount_type', $promotion->discount_type) == 'fixed' ? 'selected' : '' }}>Số tiền cố định (VNĐ)</option>
                                </select>
                                @error('discount_type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="discount_value" class="form-label">Giá trị giảm <span class="text-danger">*</span></label>
                                <input type="number" class="form-control @error('discount_value') is-invalid @enderror" 
                                       id="discount_value" name="discount_value" value="{{ old('discount_value', $promotion->discount_value) }}" 
                                       step="0.01" min="0" required>
                                <small class="form-text text-muted" id="discount_help">
                                    Nhập giá trị từ 0-100 cho phần trăm, hoặc số tiền cho giảm cố định
                                </small>
                                @error('discount_value')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="minimum_amount" class="form-label">Đơn hàng tối thiểu (VNĐ)</label>
                                <input type="number" class="form-control @error('minimum_amount') is-invalid @enderror" 
                                       id="minimum_amount" name="minimum_amount" value="{{ old('minimum_amount', $promotion->minimum_amount) }}" 
                                       step="1000" min="0">
                                <small class="form-text text-muted">Để trống nếu không yêu cầu giá trị tối thiểu</small>
                                @error('minimum_amount')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label for="usage_limit" class="form-label">Giới hạn số lần sử dụng</label>
                                <input type="number" class="form-control @error('usage_limit') is-invalid @enderror" 
                                       id="usage_limit" name="usage_limit" value="{{ old('usage_limit', $promotion->usage_limit) }}" 
                                       min="1">
                                <small class="form-text text-muted">Để trống nếu không giới hạn</small>
                                @error('usage_limit')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label for="valid_from" class="form-label">Ngày bắt đầu</label>
                                <input type="date" class="form-control @error('valid_from') is-invalid @enderror" 
                                       id="valid_from" name="valid_from" value="{{ old('valid_from', $promotion->valid_from?->format('Y-m-d')) }}">
                                <small class="form-text text-muted">Để trống nếu có hiệu lực ngay</small>
                                @error('valid_from')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label for="expired_at" class="form-label">Ngày hết hạn <span class="text-danger">*</span></label>
                                <input type="date" class="form-control @error('expired_at') is-invalid @enderror" 
                                       id="expired_at" name="expired_at" value="{{ old('expired_at', $promotion->expired_at->format('Y-m-d')) }}" 
                                       required>
                                @error('expired_at')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label for="image" class="form-label">Hình ảnh mới</label>
                                <input type="file" class="form-control @error('image') is-invalid @enderror" 
                                       id="image" name="image" accept="image/*">
                                <small class="form-text text-muted">Tối đa 2MB, định dạng: JPG, PNG, GIF</small>
                                @error('image')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="terms_conditions" class="form-label">Điều khoản và điều kiện</label>
                        <textarea class="form-control @error('terms_conditions') is-invalid @enderror" 
                                  id="terms_conditions" name="terms_conditions" rows="4">{{ old('terms_conditions', $promotion->terms_conditions) }}</textarea>
                        @error('terms_conditions')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Phạm vi áp dụng -->
                    <div class="mb-4">
                        <label class="form-label">
                            <i class="fas fa-bullseye text-primary"></i> 
                            Phạm vi áp dụng khuyến mại <span class="text-danger">*</span>
                        </label>
                        <small class="form-text text-muted d-block mb-3">Chọn một trong các tùy chọn dưới đây:</small>

                        @php
                            $selectedRoomTypes = old('room_type_ids', $promotion->roomTypes ? $promotion->roomTypes->pluck('id')->toArray() : []);
                            $currentScope = old('apply_scope', $promotion->apply_scope ?? 'all');
                            if (!in_array($currentScope, ['all', 'room_types'])) {
                                $currentScope = 'all';
                            }
                        @endphp

                        <div class="card">
                            <div class="card-body">
                                <!-- Navigation Tabs -->
                                <ul class="nav nav-tabs" id="applyScopeTabs" role="tablist">
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link {{ $currentScope === 'all' ? 'active' : '' }}" 
                                                id="all-rooms-tab" 
                                                data-bs-toggle="tab" 
                                                data-bs-target="#all-rooms" 
                                                type="button" 
                                                role="tab"
                                                data-scope="all">
                                            <i class="fas fa-globe"></i> Tất cả phòng
                                        </button>
                                    </li>
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link {{ $currentScope === 'room_types' ? 'active' : '' }}" 
                                                id="room-types-tab" 
                                                data-bs-toggle="tab" 
                                                data-bs-target="#room-types" 
                                                type="button" 
                                                role="tab"
                                                data-scope="room_types">
                                            <i class="fas fa-layer-group"></i> Theo loại phòng
                                        </button>
                                    </li>
                                </ul>

                                <!-- Hidden input for form submission -->
                                <input type="hidden" name="apply_scope" id="apply_scope_input" value="{{ $currentScope }}">

                                <!-- Tab Content -->
                                <div class="tab-content mt-3" id="applyScopeTabContent">

                                    <!-- Tab: Tất cả phòng -->
                                    <div class="tab-pane fade {{ $currentScope === 'all' ? 'show active' : '' }}" id="all-rooms" role="tabpanel">
                                        <div class="alert alert-info">
                                            <i class="fas fa-info-circle"></i>
                                            <strong>Khuyến mại này sẽ áp dụng cho tất cả phòng trong khách sạn.</strong>
                                            <br>
                                            <small class="text-muted">Không cần chọn loại phòng cụ thể.</small>
                                        </div>
                                    </div>

                                    <!-- Tab: Theo loại phòng -->
                                    <div class="tab-pane fade {{ $currentScope === 'room_types' ? 'show active' : '' }}" id="room-types" role="tabpanel">
                                        <div class="mb-3">
                                            <h6 class="mb-0">Chọn loại phòng áp dụng khuyến mại</h6>
                                        </div>
                                        <div class="row g-3">
                                            @foreach(\App\Models\RoomType::withCount('rooms')->get() as $roomType)
                                                <div class="col-sm-6 col-md-4">
                                                    <div class="form-check border rounded-3 p-3 text-center room-type-card">
                                                        <input type="checkbox" 
                                                               class="form-check-input room-type-checkbox" 
                                                               name="room_type_ids[]" 
                                                               value="{{ $roomType->id }}"
                                                               id="room_type_{{ $roomType->id }}"
                                                               {{ in_array($roomType->id, $selectedRoomTypes) ? 'checked' : '' }}>
                                                        <label for="room_type_{{ $roomType->id }}" class="form-check-label w-100">
                                                            <div class="mb-2">
                                                                <i class="fas fa-bed fa-lg text-success"></i>
                                                            </div>
                                                            <div class="fw-bold">{{ $roomType->name }}</div>
                                                            <small class="text-muted">{{ $roomType->rooms_count }} phòng</small>
                                                        </label>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Checkboxes -->
                    <div class="row mb-4">
                        <div class="col-md-4">
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" id="is_active" name="is_active" value="1" 
                                       {{ old('is_active', $promotion->is_active) ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_active">
                                    <i class="fas fa-power-off"></i> Kích hoạt
                                </label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" id="is_featured" name="is_featured" value="1" 
                                       {{ old('is_featured', $promotion->is_featured) ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_featured">
                                    <i class="fas fa-star"></i> Đặt nổi bật
                                </label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" id="can_combine" name="can_combine" value="1" 
                                       {{ old('can_combine', $promotion->can_combine) ? 'checked' : '' }}>
                                <label class="form-check-label" for="can_combine">
                                    <i class="fas fa-layer-group"></i> Có thể dùng gộp
                                </label>
                            </div>
                        </div>
                    </div>

                    <!-- Submit buttons -->
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Lưu thay đổi
                        </button>
                        <a href="{{ route('admin.promotions.index') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-times"></i> Hủy
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <!-- Sidebar -->
    <div class="col-lg-4">
        @if($promotion->image)
            <div class="card mb-3">
                <div class="card-header">
                    <h6 class="mb-0">Hình ảnh hiện tại</h6>
                </div>
                <div class="card-body text-center">
                    <img src="{{ asset('storage/' . $promotion->image) }}" alt="{{ $promotion->title }}" 
                         class="img-fluid rounded" style="max-height: 200px;">
                </div>
            </div>
        @endif

        <div class="card">
            <div class="card-header">
                <h6 class="mb-0">Thống kê</h6>
            </div>
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-6">
                        <h4 class="text-primary">{{ $promotion->used_count }}</h4>
                        <small class="text-muted">Đã sử dụng</small>
                    </div>
                    <div class="col-6">
                        <h4 class="text-success">
                            @if($promotion->usage_limit)
                                {{ $promotion->usage_limit - $promotion->used_count }}
                            @else
                                ∞
                            @endif
                        </h4>
                        <small class="text-muted">Còn lại</small>
                    </div>
                </div>
                
                @if($promotion->usage_limit)
                    <div class="mt-3">
                        <div class="progress">
                            <div class="progress-bar" style="width: {{ ($promotion->used_count / $promotion->usage_limit) * 100 }}%"></div>
                        </div>
                        <small class="text-muted">{{ number_format(($promotion->used_count / $promotion->usage_limit) * 100, 1) }}% đã sử dụng</small>
                    </div>
                @endif
            </div>
        </div>

        <div class="card mt-3">
            <div class="card-header">
                <h6 class="mb-0">Hướng dẫn</h6>
            </div>
            <div class="card-body">
                <h6><i class="fas fa-info-circle text-info"></i> Lưu ý:</h6>
                <ul class="list-unstyled">
                    <li>• Cẩn thận khi thay đổi mã khuyến mại</li>
                    <li>• Kiểm tra ngày hết hạn</li>
                    <li>• Đảm bảo phạm vi áp dụng đúng</li>
                    <li>• Xem lại điều khoản sử dụng</li>
                </ul>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<style>
.room-type-card {
    transition: all 0.2s ease;
}

.room-type-card:hover {
    transform: translateY(-1px);
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}
</style>

<script>
$(document).ready(function() {
    // Function to update apply_scope based on current selections
    function updateApplyScope() {
        const checkedRoomTypes = $('.room-type-checkbox:checked').length;
        
        if (checkedRoomTypes > 0) {
            $('#apply_scope_input').val('room_types');
        } else {
            $('#apply_scope_input').val('all');
        }
    }

    // Tab switching logic
    $('#applyScopeTabs button[data-bs-toggle="tab"]').on('shown.bs.tab', function (e) {
        const scope = $(e.target).data('scope');
        
        if (scope === 'all') {
            // Clear all checkboxes when switching to "all rooms"
            $('.room-type-checkbox').prop('checked', false);
            updateApplyScope();
        }
    });

    // Individual checkbox change events
    function setupCheckboxEvents() {
        $('.room-type-checkbox').on('change', function() {
            updateApplyScope();
        });
    }

    // Form validation
    function setupFormValidation() {
        $('#promotion-form').on('submit', function(e) {
            const scope = $('#apply_scope_input').val();
            
            if (scope === 'room_types') {
                const checkedRoomTypes = $('.room-type-checkbox:checked').length;
                if (checkedRoomTypes === 0) {
                    e.preventDefault();
                    alert('Vui lòng chọn ít nhất một loại phòng hoặc chuyển sang tab "Tất cả phòng"');
                    return false;
                }
            }
            
            // Validate discount value
            const discountType = $('#discount_type').val();
            const discountValue = parseFloat($('#discount_value').val());
            
            if (discountType === 'percentage' && discountValue > 80) {
                e.preventDefault();
                alert('Giảm giá theo phần trăm không được vượt quá 80%');
                return false;
            }
            
            return true;
        });
    }

    // Update discount help text and validation
    $('#discount_type').change(function() {
        const type = $(this).val();
        const $discountValue = $('#discount_value');
        const $helpText = $('#discount_help');
        
        if (type === 'percentage') {
            $discountValue.attr({
                'max': 80,
                'step': 0.01
            });
            $helpText.text('Nhập giá trị từ 0-80 cho phần trăm (tối đa 80%)');
        } else if (type === 'fixed') {
            $discountValue.attr({
                'max': null,
                'step': 1000
            });
            $helpText.text('Nhập số tiền giảm (VNĐ)');
        }
    });

    // Validate discount value on input
    $('#discount_value').on('input', function() {
        const type = $('#discount_type').val();
        const value = parseFloat($(this).val());
        
        if (type === 'percentage' && value > 80) {
            $(this).val(80);
            alert('Giảm giá theo phần trăm không được vượt quá 80%');
        }
    });

    // Initialize on page load
    function initializeForm() {
        setupCheckboxEvents();
        setupFormValidation();
        
        // Set initial apply_scope based on current selections
        updateApplyScope();
    }

    // Initialize
    initializeForm();
});
</script>
@endpush
@endsection 