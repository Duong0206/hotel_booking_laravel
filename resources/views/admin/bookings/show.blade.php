@extends('admin.layouts.admin-master')

@section('header', 'Chi tiết đặt phòng')

@section('content')
<div class="container-fluid px-4">
    <!-- Hiển thị thông báo -->
    {{-- @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-triangle me-2"></i>
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif --}}

    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="{{ route('admin.bookings.index') }}">Quản lý đặt phòng</a></li>
        <li class="breadcrumb-item active">Chi tiết đặt phòng #{{ $booking->booking_id }}</li>
    </ol>

    <div class="row">
        <div class="col-xl-12">
            <div class="card mb-4">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <i class="fas fa-info-circle me-1"></i>
                            Thông tin đặt phòng #{{ $booking->booking_id }}
                        </div>
                        <div>
                            <a href="{{ route('admin.bookings.edit', $booking->id) }}" class="btn btn-primary btn-sm">
                                <i class="fas fa-edit"></i> Chỉnh sửa
                            </a>
                            <form action="{{ route('admin.bookings.destroy', $booking->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Bạn có chắc chắn muốn xóa đặt phòng này?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm">
                                    <i class="fas fa-trash"></i> Xóa
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="card h-100">
                                <div class="card-header">
                                    <i class="fas fa-user me-1"></i>
                                    Thông tin khách hàng
                                </div>
                                <div class="card-body">
                                    <p><strong>Họ tên:</strong> {{ $booking->user->name }}</p>
                                    <p><strong>Email:</strong> {{ $booking->user->email }}</p>
                                    <p><strong>Số điện thoại:</strong> {{ $booking->user->phone ?? 'Không có' }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card h-100">
                                <div class="card-header">
                                    <i class="fas fa-hotel me-1"></i>
                                    Thông tin phòng
                                </div>
                                <div class="card-body">
                                    <p><strong>Phòng:</strong> {{ $booking->room->name }}</p>
                                    <p><strong>Loại phòng:</strong> {{ $booking->room->roomType->name ?? 'Không có' }}</p>
                                    <p><strong>Giá phòng:</strong> {{ number_format($booking->room->price) }} VND/đêm</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="card h-100">
                                <div class="card-header">
                                    <i class="fas fa-calendar-alt me-1"></i>
                                    Thông tin đặt phòng
                                </div>
                                <div class="card-body">
                                    <p><strong>Mã đặt phòng:</strong> {{ $booking->booking_id }}</p>
                                    <p><strong>Ngày đặt:</strong> {{ date('d/m/Y H:i', strtotime($booking->created_at)) }}</p>
                                    <p><strong>Check-in:</strong> {{ date('d/m/Y', strtotime($booking->check_in_date)) }}</p>
                                    <p><strong>Check-out:</strong> {{ date('d/m/Y', strtotime($booking->check_out_date)) }}</p>
                                    <p><strong>Số đêm:</strong> {{ date_diff(new DateTime($booking->check_in_date), new DateTime($booking->check_out_date))->days }}</p>
                                    <hr>
                                    <p><strong>Tiền phòng:</strong> {{ number_format($booking->price) }} VNĐ</p>
                                    <p><strong>Tiền dịch vụ:</strong> 
                                        <span class="text-{{ $booking->total_services_price > 0 ? 'success' : 'muted' }}">
                                            {{ number_format($booking->total_services_price) }} VNĐ
                                        </span>
                                    </p>
                                    <p class="mb-0"><strong>Tổng cộng:</strong> 
                                        <span class="text-primary fw-bold">
                                            {{ number_format($booking->price + $booking->total_services_price) }} VNĐ
                                        </span>
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card h-100">
                                <div class="card-header">
                                    <i class="fas fa-tasks me-1"></i>
                                    Trạng thái đặt phòng
                                </div>
                                <div class="card-body">
                                    <p><strong>Trạng thái hiện tại:</strong> 
                                        <span class="badge bg-{{ 
                                            $booking->status == 'pending' ? 'warning' : 
                                            ($booking->status == 'confirmed' ? 'primary' : 
                                            ($booking->status == 'checked_in' ? 'info' :
                                            ($booking->status == 'checked_out' ? 'secondary' :
                                            ($booking->status == 'completed' ? 'success' : 
                                            ($booking->status == 'no_show' ? 'dark' : 'danger'))))) 
                                        }}">
                                            {{ $booking->status_text }}
                                        </span>
                                    </p>
                                    <p><strong>Cập nhật trạng thái:</strong></p>
                                    <form action="{{ route('admin.bookings.update-status', $booking->id) }}" method="POST">
                                        @csrf
                                        @method('PATCH')
                                        <div class="input-group mb-3">
                                            <select name="status" class="form-select">
                                                <option value="{{ $booking->status }}" selected>{{ $booking->status_text }} (Hiện tại)</option>
                                                @foreach($validNextStatuses as $status => $label)
                                                    <option value="{{ $status }}">{{ $label }}</option>
                                                @endforeach
                                            </select>
                                            <button class="btn btn-primary" type="submit">Cập nhật</button>
                                        </div>
                                    </form>
                                    @if(empty($validNextStatuses))
                                        <div class="alert alert-warning">
                                            <i class="fas fa-exclamation-triangle"></i>
                                            <strong>Lưu ý:</strong> Booking đã ở trạng thái cuối cùng. Chỉ có thể chuyển sang "Đã hủy" hoặc "Khách không đến".
                                        </div>
                                    @else
                                        <div class="alert alert-info">
                                            <i class="fas fa-info-circle"></i>
                                            <strong>Hướng dẫn:</strong> Có thể chuyển sang bất kỳ trạng thái nào phía trước hoặc chuyển đặc biệt sang "Đã hủy"/"Khách không đến". 
                                            <br><small class="text-muted">Trạng thái hiện tại: <strong>{{ $booking->status_text }}</strong></small>
                                        </div>
                                        <div class="alert alert-success">
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
                            </div>
                        </div>
                    </div>

       <!-- Quản lý dịch vụ -->
<div class="row mt-4">
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <!-- Tiêu đề -->
            <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center">
                <h6 class="mb-0 fs-6">
                    <i class="fas fa-concierge-bell text-primary me-2"></i>
                    Dịch vụ Booking
                    <span class="badge bg-primary ms-2">{{ $bookingServices->count() }}</span>
                </h6>
                @if($bookingServices->count())
                    <div class="text-end">
                        <small class="text-muted">Tổng tiền:</small><br>
                        <span class="text-success fw-semibold">{{ number_format($booking->total_services_price) }} VNĐ</span>
                    </div>
                @endif
            </div>

            <div class="card-body row">
                <!-- Bảng dịch vụ -->
                <div class="col-lg-8">
                    @if($bookingServices->count())
                        <div class="table-responsive">
                            <table class="table table-sm table-hover align-middle small">
                                <thead class="table-light">
                                    <tr>
                                        <th>Dịch vụ</th>
                                        <th>Loại</th>
                                        <th>Đơn giá</th>
                                        <th>SL</th>
                                        <th>Thành tiền</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($bookingServices as $service)
                                        <tr>
                                            <td>
                                                <strong>{{ $service->service->name }}</strong><br>
                                                @if($service->notes)
                                                    <small class="text-muted">{{ $service->notes }}</small>
                                                @endif
                                            </td>
                                            <td>
                                                <span class="badge bg-{{ $service->type === 'room_type' ? 'info' : ($service->type === 'additional' ? 'success' : 'warning') }}">
                                                    {{ $service->type === 'room_type' ? 'Loại phòng' : ($service->type === 'additional' ? 'Bổ sung' : 'Tùy chỉnh') }}
                                                </span>
                                            </td>
                                            <td>{{ $service->formatted_unit_price }}</td>
                                            <td class="text-center">
                                                <span class="badge bg-light text-dark">{{ $service->quantity }}</span>
                                            </td>
                                            <td class="text-success fw-semibold">{{ $service->formatted_total_price }}</td>
                                            <td class="text-end">
                                                @if($service->type !== 'room_type')
                                                    <form action="{{ route('admin.bookings.services.destroy', [$booking->id, $service->id]) }}" method="POST" onsubmit="return confirm('Xóa dịch vụ này?')" style="display:inline;">
                                                        @csrf @method('DELETE')
                                                        <button class="btn btn-sm btn-outline-danger" title="Xóa">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </form>
                                                @else
                                                    <small class="text-muted">Tự động</small>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot class="table-light small">
                                    <tr>
                                        <td colspan="4" class="text-end fw-semibold">Tổng cộng:</td>
                                        <td class="text-success fw-bold">{{ number_format($bookingServices->sum('total_price')) }} VNĐ</td>
                                        <td></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    @else
                        <div class="text-center text-muted py-4 small">
                            <i class="fas fa-inbox fa-2x mb-2"></i>
                            <div>Chưa có dịch vụ nào</div>
                            <small>Sử dụng form bên phải để thêm</small>
                        </div>
                    @endif
                </div>

                <!-- Form thêm -->
                <div class="col-lg-4">
                    <div class="card bg-light border-0 h-100">
                        <div class="card-header bg-success text-white py-2">
                            <h6 class="mb-0 fs-6"><i class="fas fa-plus me-1"></i> Thêm dịch vụ</h6>
                        </div>
                        <div class="card-body small">
                            <form action="{{ route('admin.bookings.services.add', $booking->id) }}" method="POST">
                                @csrf
                                <div class="mb-2">
                                    <label class="form-label mb-1">Tên dịch vụ <span class="text-danger">*</span></label>
                                    <input type="text" name="service_name" value="{{ old('service_name') }}"
                                           class="form-control form-control-sm @error('service_name') is-invalid @enderror" required>
                                    @error('service_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>

                                <div class="mb-2">
                                    <label class="form-label mb-1">Giá (VNĐ) <span class="text-danger">*</span></label>
                                    <input type="number" name="service_price" value="{{ old('service_price') }}"
                                           class="form-control form-control-sm @error('service_price') is-invalid @enderror"
                                           required min="0" step="1000">
                                    @error('service_price')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>

                                <div class="mb-2">
                                    <label class="form-label mb-1">Số lượng <span class="text-danger">*</span></label>
                                    <input type="number" name="quantity" value="{{ old('quantity', 1) }}"
                                           class="form-control form-control-sm @error('quantity') is-invalid @enderror"
                                           required min="1">
                                    @error('quantity')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>

                                <div class="mb-3">
                                    <label class="form-label mb-1">Ghi chú</label>
                                    <textarea name="notes" rows="2" class="form-control form-control-sm">{{ old('notes') }}</textarea>
                                </div>

                                <button type="submit" class="btn btn-sm btn-success w-100">
                                    <i class="fas fa-plus me-1"></i> Thêm dịch vụ
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
                <!-- End form -->
            </div>
        </div>
    </div>
</div>

                    <!-- Ghi chú đặt phòng -->
                    @include('admin.bookings.partials.notes')
                </div>
            </div>
        </div>
    </div>

    <div class="mt-4">
        <a href="{{ route('admin.bookings.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Quay lại danh sách
        </a>
    </div>
</div>
@endsection 