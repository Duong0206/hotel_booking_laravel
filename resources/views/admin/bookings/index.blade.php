@extends('admin.layouts.admin-master')

@section('header', 'Quản lý đặt phòng')

@section('content')
<div class="container-fluid px-4">
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item active">Quản lý đặt phòng</li>
    </ol>
    
    <div class="card mb-4">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <i class="fas fa-table me-1"></i>
                    Danh sách đặt phòng
                </div>
                <div>
                    <a href="{{ route('admin.bookings.create') }}" class="btn btn-primary btn-sm">
                        <i class="fas fa-plus"></i> Tạo đặt phòng mới
                    </a>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="mb-3">
                <form action="{{ route('admin.bookings.index') }}" method="GET" class="row g-3">
                    <div class="col-auto">
                        <label for="status" class="col-form-label">Lọc theo trạng thái:</label>
                    </div>
                    <div class="col-auto">
                        <select name="status" id="status" class="form-select" onchange="this.form.submit()">
                            <option value="">Tất cả</option>
                            <option value="pending" {{ $status == 'pending' ? 'selected' : '' }}>Chờ xác nhận</option>
                            <option value="confirmed" {{ $status == 'confirmed' ? 'selected' : '' }}>Đã xác nhận</option>
                            <option value="checked_in" {{ $status == 'checked_in' ? 'selected' : '' }}>Đã nhận phòng</option>
                            <option value="checked_out" {{ $status == 'checked_out' ? 'selected' : '' }}>Đã trả phòng</option>
                            <option value="completed" {{ $status == 'completed' ? 'selected' : '' }}>Hoàn thành</option>
                            <option value="cancelled" {{ $status == 'cancelled' ? 'selected' : '' }}>Đã hủy</option>
                            <option value="no_show" {{ $status == 'no_show' ? 'selected' : '' }}>Không đến</option>
                        </select>
                    </div>
                </form>
            </div>

            <div class="table-responsive">
                <table class="table table-bordered table-striped" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>STT</th>
                            <th>Mã đặt phòng</th>
                            <th>Khách hàng</th>
                            <th>Phòng</th>
                            <th>Ngày check-in</th>
                            <th>Ngày check-out</th>>
                            <th>Giá</th>
                            <th>Trạng thái</th>
                            <th>Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($bookings as $booking)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $booking->booking_id }}</td>
                            <td>{{ $booking->user->name }}</td>
                            <td>{{ $booking->room->name }}</td>
                            <td>{{ $booking->check_in_date }}</td>    
                            
                            <td>{{ $booking->check_out_date }}</td>
                           
                            <td>{{ number_format($booking->price) }} VND</td>
                            <td>
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
                            </td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="{{ route('admin.bookings.show', $booking->id) }}" class="btn btn-sm btn-info">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.bookings.edit', $booking->id) }}" class="btn btn-sm btn-primary">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('admin.bookings.destroy', $booking->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Bạn có chắc chắn muốn xóa đặt phòng này?')">
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
            </div>

            <div class="d-flex justify-content-center mt-3">
                {{ $bookings->appends(request()->query())->links() }}
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