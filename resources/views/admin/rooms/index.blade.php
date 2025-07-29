@extends('admin.layouts.admin-master')

@section('content')
<div class="container-fluid">
    <!-- Bộ lọc (Không đổi) -->
    <div class="card mb-3">
        <div class="card-body d-flex flex-column justify-content-between" style="min-height: 140px;">
            <form method="GET" action="{{ route('admin.rooms.index') }}">
                <div class="row align-items-end">
                    <div class="col-md-2">
                        <label for="floor" class="form-label">Tầng</label>
                        <select name="floor" id="floor" class="form-select">
                            <option value="">Tất cả</option>
                            @foreach($floors as $floor)
                                <option value="{{ $floor }}" {{ ($filters['floor'] ?? '') == $floor ? 'selected' : '' }}>
                                    Tầng {{ $floor }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label for="room_type" class="form-label">Loại phòng</label>
                        <select name="room_type" id="room_type" class="form-select">
                            <option value="">Tất cả</option>
                            @foreach($roomTypes as $type)
                                <option value="{{ $type->id }}" {{ ($filters['room_type'] ?? '') == $type->id ? 'selected' : '' }}>
                                    {{ $type->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label for="search" class="form-label">Tìm kiếm</label>
                        <input type="text" name="search" id="search" class="form-control" placeholder="Số phòng" value="{{ $filters['search'] ?? '' }}">
                    </div>
                    <div class="col-md-2">
                        <label for="status" class="form-label">Trạng thái</label>
                        <select name="status" id="status" class="form-select">
                            <option value="">Tất cả</option>
                            <option value="available" {{ ($filters['status'] ?? '') == 'available' ? 'selected' : '' }}>Trống</option>
                            <option value="booked" {{ ($filters['status'] ?? '') == 'booked' ? 'selected' : '' }}>Đã đặt</option>
                            <option value="repair" {{ ($filters['status'] ?? '') == 'repair' ? 'selected' : '' }}>Bảo trì</option>
                            
                            
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label for="date" class="form-label">Ngày kiểm tra</label>
                        <input type="date" name="date" id="date" class="form-control" value="{{ $filters['date'] ?? '' }}">
                    </div>
                    
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary w-100">Lọc</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Sơ đồ phòng (Room Map/Grid) -->
    <div class="card">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
                <h5>Sơ đồ phòng ({{ $totalRooms }} phòng)</h5>
                <a href="{{ route('admin.rooms.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Thêm phòng
                </a>
            </div>
        </div>
        <div class="card-body">
            @php
                $statusClass = [
                    'available' => 'border-success',
                    'pending'   => 'border-warning',
                    'booked'    => 'border-secondary',
                    'repair'    => 'border-danger',
                ];
                $statusText = [
                    'available' => 'Trống',
                    'pending'   => 'Chờ xác nhận',
                    'booked'    => 'Đã đặt',
                    'repair'    => 'Bảo trì',
                ];
                $statusBgClass = [
                    'available' => 'bg-success bg-opacity-50',   // xanh đậm
                    'pending'   => 'bg-warning bg-opacity-50',   // vàng đậm
                    'booked'    => 'bg-danger bg-opacity-50',    // đỏ đậm
                    'repair'    => 'bg-secondary bg-opacity-50', // xám đậm
                ];
            @endphp

            @if($paginatedFloors->isEmpty())
                <div class="text-center p-5">
                    <i class="fas fa-search fa-3x text-muted"></i>
                    <p class="mt-3">Không tìm thấy phòng nào phù hợp.</p>
                </div>
            @endif

            @foreach($paginatedFloors as $floor => $floorRooms)
                <h5 class="mt-4 border-bottom pb-2 mb-3">Tầng {{ $floor }}</h5>
                <div class="row g-2">
                    @foreach($floorRooms as $room)
                        @php
                            $status = !empty($filters['date']) ? $room->getStatusForDate($filters['date']) : $room->status_for_display;
                        @endphp
                        <div class="col-auto">
                            <a href="{{ route('admin.rooms.show', $room->id) }}" style="text-decoration: none; color: inherit;">
                                <div class="card m-1 p-0 shadow-sm {{ $statusClass[$status] ?? '' }} {{ $statusBgClass[$status] ?? '' }}"
                                     style="width: 150px; min-height: 260px; border-width: 2px; display: flex; flex-direction: column;">
                                    @if($room->primaryImage)
                                        <img src="{{ Illuminate\Support\Facades\Storage::url($room->primaryImage->image_url) }}" class="card-img-top" alt="Ảnh phòng" style="height: 80px; object-fit: cover;">
                                    @else
                                        <div class="d-flex justify-content-center align-items-center bg-light" style="height: 80px;">
                                            <i class="fas fa-image fa-2x text-muted"></i>
                                        </div>
                                    @endif
                                    <div class="card-body p-2 text-center d-flex flex-column justify-content-between" style="flex: 1 1 auto; min-height: 120px;">
                                        <div>
                                            <div class="fw-bold text-truncate" style="font-size: 1.1em;" title="{{ $room->room_number }}">{{ $room->room_number }}</div>
                                            <div class="small text-muted text-wrap" style="white-space: normal;" title="{{ $room->roomType->name }}">{{ $room->roomType->name }}</div>
                                            <div class="mt-1">
                                                <span class="fw-semibold small">
                                                    {{ $statusText[$status] ?? 'Không rõ' }}
                                                </span>
                                            </div>
                                        </div>
                                        <div class="mt-auto">
                                            <div class="d-flex justify-content-center align-items-center w-100">
                                                <form action="{{ route('admin.rooms.destroy', $room->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Bạn có chắc chắn muốn xoá phòng này?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger btn-md px-4 py-1 room-action-btn" style="font-size: 1em;">
                                                        <i class="fas fa-trash-alt"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        </div>
                    @endforeach
                </div>
            @endforeach

            <!-- Phân trang -->
            <div class="d-flex justify-content-center mt-4">
                {{ $paginatedFloors->appends(request()->query())->links() }}
            </div>
        </div>
    </div>
</div>
@endsection