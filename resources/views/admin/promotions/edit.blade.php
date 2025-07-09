@extends('admin.layouts.admin-master')

@section('title', 'Chỉnh Sửa Khuyến Mại')

@section('header', '')

@section('content')
<div class="page-header">
    <h1><i class="fas fa-edit me-2"></i>Chỉnh Sửa Khuyến Mại</h1>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0">Thông tin khuyến mại</h6>
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
                                       id="code" name="code" value="{{ old('code', $promotion->code) }}" required>
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
                                <small class="form-text text-muted">Để trống nếu có hiệu lực ngay. Có thể chọn ngày tương lai cho khuyến mại sắp diễn ra.</small>
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
                                <small class="form-text text-muted">Để trống nếu không thay đổi hình ảnh</small>
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

                    <div class="mb-3">
                        <label class="form-label">Phạm vi áp dụng khuyến mại</label>
                        <div class="card">
                            <div class="card-body">
                                @php
                                    $selectedRoomTypes = old('room_type_ids', $promotion->roomTypes->pluck('id')->toArray());
                                    $selectedRooms = old('room_ids', $promotion->rooms->pluck('id')->toArray());
                                    
                                    // Xác định scope hiện tại
                                    $currentScope = 'all';
                                    if (!empty($selectedRooms)) {
                                        $currentScope = 'specific_rooms';
                                    } elseif (!empty($selectedRoomTypes)) {
                                        $currentScope = 'room_types';
                                    }
                                @endphp
                                
                                <!-- Navigation Tabs -->
                                <ul class="nav nav-tabs" id="applyScopeTabs" role="tablist">
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link {{ $currentScope === 'all' ? 'active' : '' }}" id="all-rooms-tab" data-bs-toggle="tab" 
                                                data-bs-target="#all-rooms" type="button" role="tab" 
                                                data-scope="all">
                                            <i class="fas fa-globe"></i> Tất cả phòng
                                        </button>
                                    </li>
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link {{ $currentScope === 'room_types' ? 'active' : '' }}" id="room-types-tab" data-bs-toggle="tab" 
                                                data-bs-target="#room-types" type="button" role="tab"
                                                data-scope="room_types">
                                            <i class="fas fa-layer-group"></i> Theo loại phòng
                                        </button>
                                    </li>
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link {{ $currentScope === 'specific_rooms' ? 'active' : '' }}" id="specific-rooms-tab" data-bs-toggle="tab" 
                                                data-bs-target="#specific-rooms" type="button" role="tab"
                                                data-scope="specific_rooms">
                                            <i class="fas fa-bed"></i> Phòng cụ thể
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
                                            <strong>Áp dụng cho tất cả phòng</strong><br>
                                            Khuyến mại sẽ được áp dụng cho toàn bộ {{ \App\Models\Room::count() }} phòng trong hệ thống.
                                        </div>
                                    </div>

                                    <!-- Tab: Theo loại phòng -->
                                    <div class="tab-pane fade {{ $currentScope === 'room_types' ? 'show active' : '' }}" id="room-types" role="tabpanel">
                                        <h6 class="mb-3"><i class="fas fa-layer-group"></i> Chọn loại phòng áp dụng:</h6>
                                        <div class="table-responsive">
                                            <table class="table table-hover">
                                                <thead class="table-light">
                                                    <tr>
                                                        <th width="50">
                                                            <input type="checkbox" id="select-all-room-types" class="form-check-input">
                                                        </th>
                                                        <th>Loại phòng</th>
                                                        <th>Số phòng</th>
                                                        <th>Giá/đêm</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($roomTypes as $roomType)
                                                        <tr>
                                                            <td>
                                                                <input class="form-check-input room-type-checkbox" type="checkbox" 
                                                                       name="room_type_ids[]" value="{{ $roomType->id }}" 
                                                                       id="room_type_{{ $roomType->id }}"
                                                                       {{ in_array($roomType->id, $selectedRoomTypes) ? 'checked' : '' }}>
                                                            </td>
                                                            <td>
                                                                <strong>{{ $roomType->name }}</strong>
                                                            </td>
                                                            <td>
                                                                <span class="badge bg-primary">{{ $roomType->rooms->count() }} phòng</span>
                                                            </td>
                                                            <td>
                                                                <span class="text-success fw-bold">{{ number_format($roomType->price, 0, ',', '.') }}đ</span>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>

                                    <!-- Tab: Phòng cụ thể -->
                                    <div class="tab-pane fade {{ $currentScope === 'specific_rooms' ? 'show active' : '' }}" id="specific-rooms" role="tabpanel">
                                        <h6 class="mb-3"><i class="fas fa-bed"></i> Chọn phòng cụ thể:</h6>
                                        
                                        @foreach($roomTypes as $roomType)
                                            <div class="mb-4">
                                                <div class="d-flex justify-content-between align-items-center mb-2">
                                                    <h6 class="text-secondary mb-0">{{ $roomType->name }}</h6>
                                                    <button type="button" class="btn btn-sm btn-outline-secondary select-all-in-type" 
                                                            data-room-type-id="{{ $roomType->id }}">
                                                        <i class="fas fa-check-double"></i> Chọn tất cả
                                                    </button>
                                                </div>
                                                
                                                <div class="table-responsive">
                                                    <table class="table table-sm table-hover">
                                                        <thead class="table-light">
                                                            <tr>
                                                                <th width="50">Chọn</th>
                                                                <th>Số phòng</th>
                                                                <th>Giá/đêm</th>
                                                                <th>Trạng thái</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @foreach($roomType->rooms as $room)
                                                                <tr class="room-type-{{ $roomType->id }}-row">
                                                                    <td>
                                                                        <input class="form-check-input specific-room-checkbox room-type-{{ $roomType->id }}-checkbox" 
                                                                               type="checkbox" name="room_ids[]" value="{{ $room->id }}" 
                                                                               id="room_{{ $room->id }}"
                                                                               {{ in_array($room->id, $selectedRooms) ? 'checked' : '' }}>
                                                                    </td>
                                                                    <td>
                                                                        <strong>{{ $room->room_number }}</strong>
                                                                    </td>
                                                                    <td>
                                                                        <span class="text-success">{{ number_format($room->price, 0, ',', '.') }}đ</span>
                                                                    </td>
                                                                    <td>
                                                                        <span class="badge bg-success">Có sẵn</span>
                                                                    </td>
                                                                </tr>
                                                            @endforeach
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>

                                <div class="mt-3">
                                    <small class="text-muted">
                                        <i class="fas fa-info-circle"></i> 
                                        Chọn phạm vi áp dụng phù hợp cho khuyến mại của bạn
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-check form-switch mb-3">
                                <input class="form-check-input" type="checkbox" id="is_active" name="is_active" 
                                       {{ old('is_active', $promotion->is_active) ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_active">
                                    Kích hoạt
                                </label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-check form-switch mb-3">
                                <input class="form-check-input" type="checkbox" id="is_featured" name="is_featured" 
                                       {{ old('is_featured', $promotion->is_featured) ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_featured">
                                    Đánh dấu nổi bật
                                </label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-check form-switch mb-3">
                                <input class="form-check-input" type="checkbox" id="can_combine" name="can_combine" 
                                       {{ old('can_combine', $promotion->can_combine) ? 'checked' : '' }}>
                                <label class="form-check-label" for="can_combine">
                                    <i class="fas fa-layer-group"></i> Có thể dùng gộp
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="{{ route('admin.promotions.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Quay lại
                        </a>
                        <div>
                            <button type="button" class="btn btn-warning me-2" onclick="
                                const currentScope = document.getElementById('apply_scope_input').value;
                                const checkedRooms = document.querySelectorAll('.specific-room-checkbox:checked').length;
                                const checkedTypes = document.querySelectorAll('.room-type-checkbox:checked').length;
                                console.log('=== MANUAL SCOPE CHECK ===');
                                console.log('Current scope:', currentScope);
                                console.log('Checked rooms:', checkedRooms);
                                console.log('Checked types:', checkedTypes);
                                
                                let message = `Current scope: ${currentScope}\nChecked rooms: ${checkedRooms}\nChecked types: ${checkedTypes}`;
                                
                                if (checkedRooms > 0 && currentScope !== 'specific_rooms') {
                                    message += '\n\n❌ MISMATCH DETECTED!';
                                    document.getElementById('apply_scope_input').value = 'specific_rooms';
                                    message += '\n✅ Fixed scope to specific_rooms';
                                    if (window.manualInitForm) window.manualInitForm();
                                } else if (checkedTypes > 0 && currentScope !== 'room_types') {
                                    message += '\n\n❌ MISMATCH DETECTED!';
                                    document.getElementById('apply_scope_input').value = 'room_types';
                                    message += '\n✅ Fixed scope to room_types';
                                    if (window.manualInitForm) window.manualInitForm();
                                } else if (checkedRooms === 0 && checkedTypes === 0 && currentScope !== 'all') {
                                    message += '\n\n❌ MISMATCH DETECTED!';
                                    document.getElementById('apply_scope_input').value = 'all';
                                    message += '\n✅ Fixed scope to all';
                                    if (window.manualInitForm) window.manualInitForm();
                                } else {
                                    message += '\n\n✅ Scope is correct!';
                                }
                                
                                alert(message);
                            ">
                                <i class="fas fa-search"></i> Check & Fix Scope
                            </button>
                            <button type="button" id="debug-form" class="btn btn-info me-2" onclick="debugFormData()">
                                <i class="fas fa-bug"></i> Debug
                            </button>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Cập nhật khuyến mại
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0">Hình ảnh hiện tại</h6>
            </div>
            <div class="card-body text-center">
                @if($promotion->image)
                    <img src="{{ asset('storage/' . $promotion->image) }}" alt="{{ $promotion->title }}" 
                         class="img-fluid rounded" style="max-height: 200px;">
                @else
                    <div class="bg-light d-flex align-items-center justify-content-center" style="height: 200px;">
                        <div class="text-center text-muted">
                            <i class="fas fa-image fa-3x mb-2"></i>
                            <p>Không có hình ảnh</p>
                        </div>
                    </div>
                @endif
            </div>
        </div>

        <div class="card mt-3">
            <div class="card-header">
                <h6 class="mb-0">Thống kê sử dụng</h6>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <div class="d-flex justify-content-between">
                        <span>Đã sử dụng:</span>
                        <strong>{{ $promotion->used_count }} lần</strong>
                    </div>
                </div>
                @if($promotion->usage_limit)
                    <div class="mb-3">
                        <div class="d-flex justify-content-between">
                            <span>Còn lại:</span>
                            <strong>{{ $promotion->usage_limit - $promotion->used_count }} lần</strong>
                        </div>
                        <div class="progress mt-2">
                            <div class="progress-bar" role="progressbar" 
                                 style="width: {{ ($promotion->used_count / $promotion->usage_limit) * 100 }}%">
                            </div>
                        </div>
                    </div>
                @endif
                <div class="mb-3">
                    <div class="d-flex justify-content-between">
                        <span>Trạng thái:</span>
                        <span class="badge bg-{{ $promotion->is_active ? 'success' : 'secondary' }}">
                            {{ $promotion->status }}
                        </span>
                    </div>
                </div>
                <div>
                    <div class="d-flex justify-content-between">
                        <span>Ngày tạo:</span>
                        <span>{{ $promotion->created_at->format('d/m/Y') }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Global script for debug function --}}
<script>
// Global debug function - defined immediately
function debugFormData() {
    console.log('=== EDIT FORM DEBUG CLICKED (GLOBAL) ===');
    alert('Edit Debug clicked! Check console (F12)');
    
    const form = document.getElementById('promotion-form');
    if (!form) {
        console.log('ERROR: Form not found!');
        alert('ERROR: Form not found!');
        return;
    }
    
    const formData = new FormData(form);
    console.log('Form data entries:');
    for (let [key, value] of formData.entries()) {
        console.log(key + ': ' + value);
    }
    
    // Check specific values with jQuery
    if (typeof $ !== 'undefined') {
        console.log('apply_scope_input value:', $('#apply_scope_input').val());
        console.log('room_type_ids checked:', $('.room-type-checkbox:checked').map(function() { return $(this).val(); }).get());
        console.log('room_ids checked:', $('.specific-room-checkbox:checked').map(function() { return $(this).val(); }).get());
    } else {
        console.log('jQuery not available');
    }
}

console.log('=== GLOBAL DEBUG FUNCTION LOADED ===');
</script>

@push('scripts')
<script>
$(document).ready(function() {
    console.log('=== PROMOTION EDIT FORM DEBUG MODE ===');
    
    // Debug button với jQuery (backup)
    $('#debug-form').click(function() {
        console.log('=== EDIT FORM DEBUG CLICKED (JQUERY) ===');
        debugFormData();
    });
    
    // Check Scope button để kiểm tra ngay lập tức
    $('#check-scope-btn').click(function() {
        const currentScope = $('#apply_scope_input').val();
        const checkedRooms = $('.specific-room-checkbox:checked').length;
        const checkedTypes = $('.room-type-checkbox:checked').length;
        
        console.log('=== CURRENT SCOPE CHECK ===');
        console.log('Current apply_scope:', currentScope);
        console.log('Checked rooms:', checkedRooms);
        console.log('Checked types:', checkedTypes);
        
        alert(`Current scope: ${currentScope}\nChecked rooms: ${checkedRooms}\nChecked types: ${checkedTypes}`);
        
        // Force update if mismatch
        if (checkedRooms > 0 && currentScope !== 'specific_rooms') {
            console.log('FORCING UPDATE TO specific_rooms');
            $('#apply_scope_input').val('specific_rooms');
            $('#specific-rooms-tab').tab('show');
            alert('Updated scope to specific_rooms!');
        }
    });
    
    // Tab switching logic
    $('#applyScopeTabs button[data-bs-toggle="tab"]').on('shown.bs.tab', function (e) {
        const scope = $(e.target).data('scope');
        console.log('=== TAB SWITCHED TO:', scope);
        
        // Update hidden input IMMEDIATELY
        $('#apply_scope_input').val(scope);
        console.log('Updated apply_scope_input to:', scope);
        
        // Clear checkboxes when switching tabs
        if (scope !== 'room_types') {
            $('.room-type-checkbox').prop('checked', false);
            $('#select-all-room-types').prop('checked', false).prop('indeterminate', false);
        }
        if (scope !== 'specific_rooms') {
            $('.specific-room-checkbox').prop('checked', false);
            $('.select-all-in-type').html('<i class="fas fa-check-double"></i> Chọn tất cả');
        }
        
        setupSelectAllEvents();
    });
    
    // Also update on checkbox changes (for manual detection)
    $(document).on('change', '.specific-room-checkbox', function() {
        const checkedRooms = $('.specific-room-checkbox:checked').length;
        if (checkedRooms > 0) {
            console.log('Detected room selection, updating scope to specific_rooms');
            $('#apply_scope_input').val('specific_rooms');
            // Also activate the specific rooms tab
            $('#specific-rooms-tab').tab('show');
        }
    });
    
    $(document).on('change', '.room-type-checkbox', function() {
        const checkedTypes = $('.room-type-checkbox:checked').length;
        if (checkedTypes > 0) {
            console.log('Detected room type selection, updating scope to room_types');
            $('#apply_scope_input').val('room_types');
            // Also activate the room types tab
            $('#room-types-tab').tab('show');
        }
    });

    // Setup select all events with detailed debugging
    function setupSelectAllEvents() {
        console.log('=== SETTING UP SELECT ALL EVENTS ===');
        
        // ROOM TYPES TAB - Select All Checkbox
        const selectAllRoomTypes = $('#select-all-room-types');
        const roomTypeCheckboxes = $('.room-type-checkbox');
        
        console.log('Select all room types element found:', selectAllRoomTypes.length);
        console.log('Room type checkboxes found:', roomTypeCheckboxes.length);
        
        // Remove old events
        selectAllRoomTypes.off('change.custom');
        roomTypeCheckboxes.off('change.custom');
        
        // Bind new events  
        selectAllRoomTypes.on('change.custom', function() {
            const isChecked = $(this).is(':checked');
            console.log('>>> SELECT ALL ROOM TYPES CLICKED:', isChecked);
            roomTypeCheckboxes.prop('checked', isChecked);
        });

        roomTypeCheckboxes.on('change.custom', function() {
            const total = roomTypeCheckboxes.length;
            const checked = roomTypeCheckboxes.filter(':checked').length;
            console.log(`>>> Room type checkbox changed. Checked: ${checked}/${total}`);
            
            selectAllRoomTypes.prop('checked', checked === total);
            selectAllRoomTypes.prop('indeterminate', checked > 0 && checked < total);
        });

        // SPECIFIC ROOMS TAB - Select All Buttons
        $('.select-all-in-type').off('click.custom').on('click.custom', function() {
            const roomTypeId = $(this).data('room-type-id');
            const checkboxes = $(`.room-type-${roomTypeId}-checkbox`);
            const allChecked = checkboxes.filter(':checked').length === checkboxes.length;
            
            console.log(`>>> SELECT ALL IN TYPE ${roomTypeId} CLICKED`);
            console.log(`Found ${checkboxes.length} checkboxes, ${checkboxes.filter(':checked').length} checked`);
            console.log(`All checked: ${allChecked}, will set to: ${!allChecked}`);
            
            checkboxes.prop('checked', !allChecked);
            
            // Update button text
            const newText = !allChecked ? 
                '<i class="fas fa-times"></i> Bỏ chọn tất cả' : 
                '<i class="fas fa-check-double"></i> Chọn tất cả';
            $(this).html(newText);
        });
        
        console.log('Select all in type buttons found:', $('.select-all-in-type').length);
    }

    // Image preview
    $('#image').change(function() {
        const file = this.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                // Update current image preview
                $('.card-body img').attr('src', e.target.result);
                $('.card-body .bg-light').hide();
            };
            reader.readAsDataURL(file);
        }
    });

    // Function to initialize form state
    function initializeFormState() {
        console.log('=== INITIALIZING EDIT FORM ===');
        
        // Setup existing state for edit form
        const totalRoomTypes = $('.room-type-checkbox').length;
        const checkedRoomTypes = $('.room-type-checkbox:checked').length;
        const totalRooms = $('.specific-room-checkbox').length;
        const checkedRooms = $('.specific-room-checkbox:checked').length;
        
        console.log(`Found ${checkedRoomTypes}/${totalRoomTypes} room types already checked`);
        console.log(`Found ${checkedRooms}/${totalRooms} specific rooms already checked`);
        
        // Determine correct scope based on existing data
        if (checkedRooms > 0) {
            console.log('Setting scope to specific_rooms (rooms are checked)');
            $('#apply_scope_input').val('specific_rooms');
            $('#specific-rooms-tab').tab('show');
        } else if (checkedRoomTypes > 0) {
            console.log('Setting scope to room_types (room types are checked)');
            $('#apply_scope_input').val('room_types');
            $('#room-types-tab').tab('show');
        } else {
            console.log('Setting scope to all (nothing checked)');
            $('#apply_scope_input').val('all');
            $('#all-rooms-tab').tab('show');
        }
        
        if (checkedRoomTypes > 0) {
            $('#select-all-room-types').prop('checked', totalRoomTypes === checkedRoomTypes);
            $('#select-all-room-types').prop('indeterminate', checkedRoomTypes > 0 && checkedRoomTypes < totalRoomTypes);
        }
        
        // For specific rooms in each type
        $('.select-all-in-type').each(function() {
            const roomTypeId = $(this).data('room-type-id');
            const checkboxes = $(`.room-type-${roomTypeId}-checkbox`);
            const checkedBoxes = checkboxes.filter(':checked');
            
            console.log(`Room type ${roomTypeId}: ${checkedBoxes.length}/${checkboxes.length} rooms checked`);
            
            if (checkedBoxes.length === checkboxes.length && checkboxes.length > 0) {
                $(this).html('<i class="fas fa-times"></i> Bỏ chọn tất cả');
            }
        });
        
        setupSelectAllEvents();
        console.log('=== EDIT FORM INITIALIZATION COMPLETE ===');
        
        // Force display of current scope
        const finalScope = $('#apply_scope_input').val();
        console.log('=== FINAL SCOPE SET TO:', finalScope);
    }
    
    // Initialize immediately 
    initializeFormState();
    
    // Also initialize after a delay for safety
    setTimeout(initializeFormState, 500);
    
    // Add a manual trigger button
    window.manualInitForm = initializeFormState;
});
</script>
@endpush
@endsection 