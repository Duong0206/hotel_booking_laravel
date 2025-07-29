@extends('admin.layouts.admin-master')

@section('content')
    <div class="container mt-4">
        <div class="card shadow rounded">
            <div class="card-header d-flex justify-content-between align-items-center bg-success text-white">
                <h5 class="mb-0">
                    <i class="fas fa-link me-2"></i>Gán dịch vụ cho loại phòng
                </h5>
                <div class="d-flex gap-2">
                    <a href="{{ route('admin.services.index') }}" class="btn btn-outline-light btn-sm">
                        <i class="fas fa-list"></i> Quản lý dịch vụ
                    </a>
                    <a href="{{ route('admin.services.create') }}" class="btn btn-outline-light btn-sm">
                        <i class="fas fa-plus"></i> Thêm dịch vụ
                    </a>
                </div>
            </div>

            <div class="card-body">
                @if (session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif

                <div class="table-responsive">
                    <table class="table table-bordered table-hover align-middle text-center mb-0">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 60px;">#</th>
                                <th>Tên loại phòng</th>
                                <th>Dịch vụ đã gán</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($roomTypes as $roomType)
                                @php
                                    $serviceCount = $roomType->services->count();
                                    $editUrl = route('admin.room-type-services.edit', $roomType->id);
                                @endphp
                                <tr onclick="window.location='{{ $editUrl }}'" style="cursor: pointer;">
                                    <td>{{ $roomType->id }}</td>
                                    <td class="text-start">
                                        <strong>{{ $roomType->name }}</strong>
                                        @if ($roomType->price)
                                            <br><small class="text-muted">{{ number_format($roomType->price, 0, ',', '.') }}
                                                đ/đêm</small>
                                        @endif
                                    </td>
                                    <td>
                                        @if ($serviceCount > 0)
                                            <span class="badge bg-success"><i
                                                    class="fas fa-check-circle me-1"></i>{{ $serviceCount }} dịch vụ</span>
                                        @else
                                            <span class="badge bg-secondary"><i class="fas fa-times-circle me-1"></i>Chưa
                                                gán</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted py-4">
                                        <i class="fas fa-bed fa-2x mb-3"></i><br>
                                        Không có loại phòng nào.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
