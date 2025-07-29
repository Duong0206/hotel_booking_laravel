@extends(auth()->user()->role && auth()->user()->role->name === 'admin' ? 'admin.layouts.admin-master' : 'client.layouts.master')

@section('title', 'Thêm ghi chú mới')

@if(auth()->user()->role && auth()->user()->role->name === 'admin')
@section('header', 'Thêm ghi chú mới')
@endif

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header">
                    <h4 class="mb-0">
                        <i class="fas fa-plus-circle"></i>
                        Thêm ghi chú mới cho booking #{{ $booking->booking_id }}
                    </h4>
                </div>
                <div class="card-body">
                    <form action="{{ route('booking-notes.store', $bookingId) }}" method="POST">
                        @csrf
                        <input type="hidden" name="booking_id" value="{{ $bookingId }}">
                        
                        <!-- Loại ghi chú -->
                        <div class="mb-3">
                            <label for="type" class="form-label">Loại ghi chú <span class="text-danger">*</span></label>
                            <select name="type" id="type" class="form-select @error('type') is-invalid @enderror" required>
                                <option value="">Chọn loại ghi chú</option>
                                <option value="customer" {{ old('type') == 'customer' ? 'selected' : '' }}>Khách hàng</option>
                                @if(auth()->user()->role && in_array(auth()->user()->role->name, ['admin', 'staff']))
                                    <option value="staff" {{ old('type') == 'staff' ? 'selected' : '' }}>Nhân viên</option>
                                @endif
                                @if(auth()->user()->role && auth()->user()->role->name === 'admin')
                                    <option value="admin" {{ old('type') == 'admin' ? 'selected' : '' }}>Quản trị</option>
                                @endif
                            </select>
                            @error('type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Quyền xem -->
                        <div class="mb-3">
                            <label for="visibility" class="form-label">Quyền xem <span class="text-danger">*</span></label>
                            <select name="visibility" id="visibility" class="form-select @error('visibility') is-invalid @enderror" required>
                                <option value="">Chọn quyền xem</option>
                                <option value="public" {{ old('visibility') == 'public' ? 'selected' : '' }}>Công khai</option>
                                <option value="private" {{ old('visibility') == 'private' ? 'selected' : '' }}>Riêng tư</option>
                                @if(auth()->user()->role && in_array(auth()->user()->role->name, ['admin', 'staff']))
                                    <option value="internal" {{ old('visibility') == 'internal' ? 'selected' : '' }}>Nội bộ</option>
                                @endif
                            </select>
                            @error('visibility')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">
                                <i class="fas fa-info-circle me-1"></i>
                                <strong>Công khai:</strong> Mọi người có thể xem | 
                                <strong>Riêng tư:</strong> Chỉ bạn xem được | 
                                <strong>Nội bộ:</strong> Chỉ nhân viên và quản lý xem được
                            </div>
                        </div>

                        <!-- Nội dung -->
                        <div class="mb-3">
                            <label for="content" class="form-label">Nội dung ghi chú <span class="text-danger">*</span></label>
                            <textarea name="content" id="content" class="form-control @error('content') is-invalid @enderror" 
                                      rows="6" placeholder="Nhập nội dung ghi chú..." required maxlength="1000">{{ old('content') }}</textarea>
                            @error('content')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">
                                <span id="char-count">0</span>/1000 ký tự
                            </div>
                        </div>

                        <!-- Ghi chú nội bộ (chỉ admin) -->
                        @if(auth()->user()->role && auth()->user()->role->name === 'admin')
                            <div class="mb-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="is_internal" id="is_internal" value="1" {{ old('is_internal') ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_internal">
                                        <i class="fas fa-eye-slash text-warning me-1"></i>
                                        Ghi chú nội bộ (chỉ admin xem được)
                                    </label>
                                </div>
                            </div>
                        @endif

                        <!-- Nút điều khiển -->
                        <div class="d-flex justify-content-between">
                            @if(auth()->user()->role && auth()->user()->role->name === 'admin')
                                <a href="{{ route('admin.bookings.show', $bookingId) }}" class="btn btn-secondary">
                                    <i class="fas fa-arrow-left"></i> Quay lại chi tiết booking
                                </a>
                            @else
                                <a href="{{ route('booking-notes.index', $bookingId) }}" class="btn btn-secondary">
                                    <i class="fas fa-arrow-left"></i> Quay lại
                                </a>
                            @endif
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Lưu ghi chú
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const contentTextarea = document.getElementById('content');
    const charCount = document.getElementById('char-count');
    
    // Đếm ký tự
    function updateCharCount() {
        const length = contentTextarea.value.length;
        charCount.textContent = length;
        
        if (length > 900) {
            charCount.style.color = '#dc3545';
        } else if (length > 800) {
            charCount.style.color = '#ffc107';
        } else {
            charCount.style.color = '#6c757d';
        }
    }
    
    contentTextarea.addEventListener('input', updateCharCount);
    updateCharCount(); // Cập nhật ban đầu
});
</script>
@endsection 