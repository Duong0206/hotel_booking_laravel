@extends('client.layouts.master')

@section('title', 'Xác nhận Thanh Toán')

@section('content')
    <div class="hero-wrap"
        style="background-image: url('{{ asset('client/images/bg_1.jpg') }}'); font-family: 'Segoe UI', sans-serif;">
        <div class="overlay"></div>
        <div class="container">
            <div class="row no-gutters slider-text d-flex align-items-end justify-content-center">
                <div class="col-md-9 text-center d-flex align-items-end justify-content-center">
                    <div class="text">
                        <p class="breadcrumbs mb-2">
                            <span class="mr-2"><a href="{{ route('index') }}">Trang chủ</a></span>
                            <span>Xác nhận thanh toán</span>
                        </p>
                        <h3 class="mb-4 bread">Chi tiết thông tin</h3>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <section class="ftco-section bg-light py-5" style="font-family: 'Segoe UI', sans-serif;">
        <div class="container">
            @if (session('success'))
                <div class="alert alert-success text-center">
                    <i class="fas fa-check-circle mr-2"></i> {{ session('success') }}
                </div>
            @endif
            @if (session('error'))
                <div class="alert alert-danger text-center">
                    <i class="fas fa-exclamation-triangle mr-2"></i> {{ session('error') }}
                </div>
            @endif

            <div class="card shadow-lg border-0 rounded-4 px-4 py-5 payment-card">
                <h2 class="text-center text-primary font-weight-bold mb-5">
                    <i class="fas fa-credit-card mr-2"></i> Xác nhận thông tin đặt phòng
                </h2>

                {{-- Thông tin --}}
                <div class="row">
                    <div class="col-md-6 mb-4">
                        <div class="p-4 border rounded-3 bg-light-subtle h-100">
                            <h4 class="text-secondary mb-4"><i class="fas fa-user mr-2"></i>Thông tin khách hàng</h4>
                            <ul class="list-unstyled mb-0">
                                <li class="mb-2"><strong>Họ tên:</strong> {{ $booking->user->name }}</li>
                                <li class="mb-2"><strong>Email:</strong> {{ $booking->user->email }}</li>
                            </ul>
                        </div>
                    </div>
                    <div class="col-md-6 mb-4">
                        <div class="p-4 border rounded-3 bg-light-subtle h-100">
                            <h4 class="text-secondary mb-4"><i class="fas fa-bed mr-2"></i>Thông tin đặt phòng</h4>
                            <ul class="list-unstyled mb-0">
                                <li class="mb-2"><strong>Mã đặt phòng:</strong>
                                    <span class="badge bg-primary text-white">{{ $booking->booking_id }}</span>
                                </li>
                                <li class="mb-2"><strong>Loại phòng:</strong>
                                    {{ $booking->room->roomType->name }}</li>
                                <li class="mb-2"><strong>Ngày đến:</strong>
                                    {{ \Carbon\Carbon::parse($booking->check_in_date)->format('d/m/Y') }}</li>
                                <li><strong>Ngày đi:</strong>
                                    {{ \Carbon\Carbon::parse($booking->check_out_date)->format('d/m/Y') }}</li>
                                
                            </ul>
                        </div>
                    </div>
                </div>
                {{-- Số tiền --}}
                <div class="border rounded-3 bg-light p-4 mb-5">
                    <h5 class="text-secondary mb-3"><i class="fas fa-receipt me-2"></i>Chi tiết giá</h5>
                    <div class="row">
                        <div class="col-md-8">
                            <p class="mb-2">Giá phòng ({{ $booking->room->roomType->name }})</p>
                            <p class="mb-2">Số đêm</p>
                            <p class="mb-2">Thuế & phí dịch vụ</p>
                            <p class="mb-2">Khuyến mãi</p>
                            <div id="promotion_description" class="form-text text-muted mb-2 small"></div>
                            
                            <p class="mb-2" id="discount_amount">Giảm giá</p>
                            <hr class="my-3">
                            <p class="fw-bold mb-0">Tổng cộng</p>
                        </div>
                        <div class="col-md-4 text-end">
                            <p class="mb-2">{{ number_format($booking->room->price, 0, ',', '.') }} VNĐ</p>
                            <p class="mb-2">{{ \Carbon\Carbon::parse($booking->check_in_date)->diffInDays(\Carbon\Carbon::parse($booking->check_out_date)) }}
                                    đêm</p>
                            <p class="mb-2">Miễn phí</p>
                            <p class="mb-2">
                                <select class="form-select form-select-sm" id="promotion_select" name="promotion_id">
                                    <option value="">Không áp dụng khuyến mãi</option>
                                    @foreach($promotions as $promotion)
                                        <option value="{{ $promotion->id }}" 
                                            data-discount-type="{{ $promotion->discount_type }}"
                                            data-discount-value="{{ $promotion->discount_value }}"
                                            data-description="{{ $promotion->description }}">
                                            {{ $promotion->name }} - 
                                            @if($promotion->discount_type == 'percentage')
                                                Giảm {{ $promotion->discount_value }}%
                                            @else
                                                Giảm {{ number_format($promotion->discount_value, 0, ',', '.') }} VNĐ
                                            @endif
                                        </option>
                                    @endforeach
                                </select>
                            </p>
                            <p class="mb-2 text-danger" id="discount_amount_value">0 VNĐ</p>
                            <hr>
                            <p class="fw-bold text-gold mb-0" id="total_price">{{ number_format($booking->price, 0, ',', '.') }} VNĐ</p>
                            <input type="hidden" id="original_price" value="{{ $booking->price }}">
                        </div>
                    </div>
                </div>
                <form action="{{ route('payment-method', $booking->id) }}" method="GET" id="payment-form">
                    <input type="hidden" name="promotion_id" id="selected_promotion_id" value="">
                    <div class="text-center mt-4">
                        <button type="submit" class="btn btn-primary btn-lg px-5">
                            <i class="fas fa-check-circle mr-2"></i> Tiếp tục thanh toán
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </section>

    <style>
        .payment-card {
            background: #fff;
            font-family: 'Segoe UI', sans-serif;
        }

        .bg-light-subtle {
            background-color: #f8f9fa;
        }

        .rounded-4 {
            border-radius: 1rem !important;
        }

        .badge.bg-primary {
            font-size: 0.9rem;
            padding: 0.5em 0.75em;
        }

        .payment-method-label {
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .payment-method-label:hover {
            background-color: #f1f1f1;
        }

        .payment-method-label input:checked+img,
        .payment-method-label input:checked+div {
            font-weight: bold;
            color: #007bff;
        }

        .payment-method-label input:checked~img {
            transform: scale(1.1);
        }
    </style>

    @push('scripts')
    <script>
        document.getElementById('promotion_select').addEventListener('change', function() {
            const promotionId = this.value;
            const description = this.options[this.selectedIndex].dataset.description;
            
            // Cập nhật promotion_id trong form
            document.getElementById('selected_promotion_id').value = promotionId;
            
            // Hiển thị mô tả khuyến mãi
            document.getElementById('promotion_description').textContent = description || '';
            
            if (promotionId) {
                // Gọi API tính toán giá
                fetch('{{ route("calculate-promotion") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        promotion_id: promotionId,
                        booking_id: '{{ $booking->id }}'
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Hiển thị số tiền giảm
                        document.getElementById('discount_amount_value').textContent = data.data.formatted.discount_amount;
                        
                        // Cập nhật tổng tiền
                        document.getElementById('total_price').textContent = data.data.formatted.final_price;
                        
                        // Hiển thị phần giảm giá
                        document.getElementById('discount_amount').style.display = 'block';
                        document.getElementById('discount_amount_value').style.display = 'block';
                    } else {
                        alert(data.message || 'Có lỗi xảy ra khi tính giá khuyến mãi');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Có lỗi xảy ra khi tính giá khuyến mãi');
                });
            } else {
                // Reset về giá gốc
                document.getElementById('discount_amount_value').textContent = '0 VNĐ';
                document.getElementById('total_price').textContent = '{{ number_format($booking->price, 0, ',', '.') }} VNĐ';
                document.getElementById('discount_amount').style.display = 'none';
                document.getElementById('discount_amount_value').style.display = 'none';
            }
        });
    </script>
    @endpush
@endsection
