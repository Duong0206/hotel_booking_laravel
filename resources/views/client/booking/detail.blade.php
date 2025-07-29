@extends('client.layouts.master')
@section('title', 'Chi tiết đặt phòng')
@section('content')
<div class="container py-4">
    @if (session('success'))
        <div class="alert alert-success d-flex align-items-center justify-content-center mb-4" role="alert">
            <i class="fas fa-check-circle me-2"></i>
            <div>{{ session('success') }}</div>
        </div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger d-flex align-items-center justify-content-center mb-4" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i>
            <div>{{ session('error') }}</div>
        </div>
    @endif
    @if ($errors->any())
        <div class="alert alert-danger d-flex align-items-center justify-content-center mb-4" role="alert">
            <i class="fas fa-exclamation-triangle me-2"></i>
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">Chi tiết đặt phòng #{{ $booking->booking_id }}</h4>
                </div>
                <div class="card-body">
                    <p><strong>Ngày đặt:</strong> {{ $booking->created_at->format('d/m/Y H:i') }}</p>
                    <p><strong>Check-in:</strong> {{ $booking->check_in_date->format('d/m/Y') }}</p>
                    <p><strong>Check-out:</strong> {{ $booking->check_out_date->format('d/m/Y') }}</p>
                    <p><strong>Trạng thái:</strong> <span class="badge badge-{{ $booking->status == 'pending' ? 'warning' : ($booking->status == 'confirmed' ? 'success' : ($booking->status == 'cancelled' ? 'danger' : 'primary')) }}">{{ $booking->status_text ?? $booking->status }}</span></p>
                    <p><strong>Tổng tiền:</strong> {{ number_format($booking->price) }}đ</p>
                    <hr>
                    <h5>Thông tin phòng</h5>
                    <p><strong>Loại phòng:</strong> {{ $booking->room->roomType->name ?? 'N/A' }}</p>
                    <p><strong>Sức chứa:</strong> {{ $booking->room->roomType->capacity ?? 'N/A' }} người</p>
                    <p><strong>Ghi chú:</strong> {{ $booking->notes ?? 'Không có' }}</p>
                </div>
            </div>
            <div class="mt-3">
                <a href="{{ url()->previous() }}" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Quay lại</a>
            </div>
            <div class="mt-4">
                <x-booking-notes :booking="$booking" :showAddButton="true" :showSearch="true" />
            </div>
        </div>
    </div>
</div>
@endsection 