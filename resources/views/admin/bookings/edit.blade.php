@extends('admin.layouts.admin-master')

@section('title', 'Chỉnh sửa đặt phòng')

@section('header', 'Chỉnh sửa đặt phòng')

@section('content')
<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex justify-content-between align-items-center">
        <h6 class="m-0 font-weight-bold">Phòng #{{ $booking->booking_id }}</h6>
        <div>
            <a href="{{ route('admin.bookings.show', $booking->id) }}" class="btn btn-sm btn-info">
                <i class="fas fa-eye"></i> Xem chi tiết
            </a>
            <a href="{{ route('admin.bookings.index') }}" class="btn btn-sm btn-secondary">
                <i class="fas fa-arrow-left"></i> Quay lại
            </a>
        </div>
    </div>
    <div class="card-body">
        <form action="{{ route('admin.bookings.update', $booking->id) }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="user_id" class="form-label">Khách hàng</label>
                    <select name="user_id" id="user_id" class="form-select @error('user_id') is-invalid @enderror" required>
                        <option value="">-- Chọn khách hàng --</option>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}" {{ (old('user_id', $booking->user_id) == $user->id) ? 'selected' : '' }}>
                                {{ $user->name }} ({{ $user->email }})
                            </option>
                        @endforeach
                    </select>
                    @error('user_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="col-md-6">
                    <label for="room_id" class="form-label">Phòng</label>
                    <select name="room_id" id="room_id" class="form-select @error('room_id') is-invalid @enderror" required>
                        <option value="">-- Chọn phòng --</option>
                        @foreach($rooms as $room)
                            <option value="{{ $room->id }}" {{ (old('room_id', $booking->room_id) == $room->id) ? 'selected' : '' }}>
                                {{ $room->name }} - {{ number_format($room->price, 0, ',', '.') }} VNĐ/đêm
                            </option>
                        @endforeach
                    </select>
                    @error('room_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            
            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="check_in_date" class="form-label">Ngày nhận phòng</label>
                    <input type="date" name="check_in_date" id="check_in_date" class="form-control @error('check_in_date') is-invalid @enderror" value="{{ old('check_in_date', $booking->check_in_date->format('Y-m-d')) }}" required>
                    @error('check_in_date')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="col-md-6">
                    <label for="check_out_date" class="form-label">Ngày trả phòng</label>
                    <input type="date" name="check_out_date" id="check_out_date" class="form-control @error('check_out_date') is-invalid @enderror" value="{{ old('check_out_date', $booking->check_out_date->format('Y-m-d')) }}" required>
                    @error('check_out_date')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            
            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="status" class="form-label">Trạng thái đặt phòng</label>
                    <select name="status" id="status" class="form-select @error('status') is-invalid @enderror" required>
                        <option value="{{ $booking->status }}" selected>{{ $booking->status_text }} (Hiện tại)</option>
                        @foreach($validNextStatuses as $status => $label)
                            <option value="{{ $status }}" {{ (old('status') == $status) ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                    <div class="form-text">
                        <strong>Hướng dẫn chuyển trạng thái:</strong><br>
                        • <strong>Thứ tự đề xuất:</strong> Chờ xác nhận → Đã xác nhận → Đã nhận phòng → Đã trả phòng → Hoàn thành<br>
                        • <strong>Chuyển linh hoạt:</strong> Có thể chuyển sang bất kỳ trạng thái nào phía trước<br>
                        • <strong>Chuyển đặc biệt:</strong> Có thể chuyển sang "Đã hủy" hoặc "Khách không đến" ở bất kỳ trạng thái nào<br>
                        • <strong>Không được lùi:</strong> Không thể chuyển về trạng thái trước đó
                        <br><small class="text-muted">Trạng thái hiện tại: <strong>{{ $booking->status_text }}</strong></small>
                    </div>
                    @error('status')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    @if(empty($validNextStatuses))
                        <div class="alert alert-warning mt-2">
                            <i class="fas fa-exclamation-triangle"></i>
                            <strong>Lưu ý:</strong> Booking đã ở trạng thái cuối cùng. Chỉ có thể chuyển sang "Đã hủy" hoặc "Khách không đến".
                        </div>
                    @else
                        <div class="alert alert-success mt-2">
                            <i class="fas fa-check-circle"></i>
                            <strong>Thông tin:</strong> Có <strong>{{ count($validNextStatuses) }}</strong> trạng thái có thể chuyển đổi từ trạng thái hiện tại.
                            <br><small class="text-muted">Các trạng thái có thể chuyển: 
                                @foreach($validNextStatuses as $status => $label)
                                    <span class="badge bg-light text-dark me-1">{{ $label }}</span>
                                @endforeach
                            </small>
                        </div>
                    @endif
                </div>
                
                <div class="col-md-6">
                    <label class="form-label">Tổng tiền hiện tại</label>
                    <div class="form-control bg-light">{{ number_format($booking->price, 0, ',', '.') }} VNĐ</div>
                    <small class="text-muted">Tổng tiền sẽ được tính lại nếu thay đổi phòng hoặc ngày</small>
                </div>
            </div>
            
            <div class="row mb-3">
                <div class="col-md-12">
                    <label for="admin_notes" class="form-label">Ghi chú quản lý</label>
                    <textarea name="admin_notes" id="admin_notes" class="form-control @error('admin_notes') is-invalid @enderror" rows="3" placeholder="Ghi chú nội bộ cho quản lý (khách hàng không thấy)">{{ old('admin_notes', $booking->admin_notes) }}</textarea>
                    <div class="form-text">
                        Ghi chú nội bộ để theo dõi tình trạng đặt phòng, lý do thay đổi trạng thái, hoặc thông tin liên hệ với khách hàng.
                    </div>
                    @error('admin_notes')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            
            <div class="mt-4">
                <button type="submit" class="btn btn-primary">Cập nhật</button>
                <a href="{{ route('admin.bookings.index') }}" class="btn btn-secondary">Hủy</a>
            </div>
        </form>
    </div>
</div>

<!-- Ghi chú đặt phòng -->
@include('admin.bookings.partials.notes')
@endsection

@section('scripts')
<script>
    // Kiểm tra ngày trả phải sau ngày nhận
    document.getElementById('check_in_date').addEventListener('change', function() {
        const checkInDate = new Date(this.value);
        const checkOutInput = document.getElementById('check_out_date');
        const checkOutDate = new Date(checkOutInput.value);
        
        if (checkOutDate <= checkInDate) {
            const nextDay = new Date(checkInDate);
            nextDay.setDate(nextDay.getDate() + 1);
            checkOutInput.value = nextDay.toISOString().split('T')[0];
        }
    });
</script>
@endsection 