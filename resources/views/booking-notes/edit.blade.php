@extends(auth()->user()->role && auth()->user()->role->name === 'admin' ? 'admin.layouts.admin-master' : 'client.layouts.master')

@section('title', 'Chỉnh sửa ghi chú')

@if(auth()->user()->role && auth()->user()->role->name === 'admin')
@section('header', 'Chỉnh sửa ghi chú')
@endif

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h4 class="mb-0">
                        <i class="fas fa-edit"></i>
                        Chỉnh sửa ghi chú cho booking #{{ $booking->booking_id }}
                    </h4>
                </div>
                <div class="card-body">
                    <form action="{{ route('booking-notes.update', [$note->booking_id, $note->id]) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <!-- Thông tin ghi chú -->
                        <div class="alert alert-info">
                            <div class="row">
                                <div class="col-md-6">
                                    <strong>Loại:</strong> 
                                    <span class="badge bg-{{ $note->type === 'admin' ? 'danger' : ($note->type === 'staff' ? 'warning' : 'info') }}">
                                        {{ $note->type === 'customer' ? 'Khách hàng' : ($note->type === 'staff' ? 'Nhân viên' : 'Quản trị') }}
                                    </span>
                                </div>
                                <div class="col-md-6">
                                    <strong>Quyền xem:</strong> 
                                    <span class="badge bg-{{ $note->visibility === 'private' ? 'secondary' : ($note->visibility === 'internal' ? 'warning' : 'primary') }}">
                                        {{ $note->visibility === 'public' ? 'Công khai' : ($note->visibility === 'private' ? 'Riêng tư' : 'Nội bộ') }}
                                    </span>
                                </div>
                            </div>
                            <div class="mt-2">
                                <strong>Tạo bởi:</strong> {{ $note->user->name }} | 
                                <strong>Ngày tạo:</strong> {{ $note->created_at->format('d/m/Y H:i') }}
                            </div>
                        </div>

                        <!-- Quyền xem (có thể thay đổi) -->
                        <div class="mb-3">
                            <label for="visibility" class="form-label">Quyền xem</label>
                            <select name="visibility" id="visibility" class="form-select @error('visibility') is-invalid @enderror">
                                <option value="public" {{ old('visibility', $note->visibility) == 'public' ? 'selected' : '' }}>Công khai</option>
                                <option value="private" {{ old('visibility', $note->visibility) == 'private' ? 'selected' : '' }}>Riêng tư</option>
                                @if(auth()->user()->role && in_array(auth()->user()->role->name, ['admin', 'staff']))
                                    <option value="internal" {{ old('visibility', $note->visibility) == 'internal' ? 'selected' : '' }}>Nội bộ</option>
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
                                      rows="6" placeholder="Nhập nội dung ghi chú..." required maxlength="1000">{{ old('content', $note->content) }}</textarea>
                            @error('content')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">
                                <span id="char-count">0</span>/1000 ký tự
                            </div>
                        </div>

                        <!-- Nút điều khiển -->
                        <div class="d-flex justify-content-between">
                            @if(auth()->user()->role && auth()->user()->role->name === 'admin')
                                <a href="{{ route('admin.bookings.show', $note->booking_id) }}" class="btn btn-secondary">
                                    <i class="fas fa-arrow-left"></i> Quay lại chi tiết booking
                                </a>
                            @else
                                <a href="{{ route('booking-notes.index', $note->booking_id) }}" class="btn btn-secondary">
                                    <i class="fas fa-arrow-left"></i> Quay lại
                                </a>
                            @endif
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Cập nhật ghi chú
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