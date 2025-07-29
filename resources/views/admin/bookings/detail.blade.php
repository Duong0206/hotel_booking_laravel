<div class="container-fluid px-4">
    <div class="row">
        <div class="col-xl-12">
            <div class="card mb-4">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <i class="fas fa-info-circle me-1"></i>
                            Thông tin đặt phòng #{{ $booking->booking_id }}
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
                                    <p><strong>Tổng tiền:</strong> {{ number_format($booking->price) }} VND</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div> 