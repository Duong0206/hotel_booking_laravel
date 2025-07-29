@extends('client.layouts.master')

@section('title', 'Phương Thức Thanh Toán')

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
                            <span>Thanh toán</span>
                        </p>
                        <h3 class="mb-4 bread">Chọn phương thức thanh toán</h3>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <section class="ftco-section bg-light" style="font-family: 'Segoe UI', sans-serif;">
        <div class="container">
            <div class="row">
                {{-- Form chọn phương thức thanh toán --}}
                <div class="col-md-8 mb-4">
                    <div class="card p-4 shadow-sm rounded-lg h-100">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h4 class="mb-0">Bạn muốn thanh toán như thế nào ?</h4>
                        </div>
                        <div class="payment-options">
                            <ul class="nav nav-tabs mb-3" id="paymentTab" role="tablist">
                                <li class="nav-item">
                                    <a class="nav-link active" id="credit-tab" data-toggle="tab" href="#credit"
                                        role="tab">Thẻ tín dụng/ghi nợ</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" id="momo-tab" data-toggle="tab" href="#momo" role="tab">Ví
                                        MoMo</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" id="bank-tab" data-toggle="tab" href="#bank"
                                        role="tab">Chuyển khoản ngân hàng</a>
                                </li>
                            </ul>

                            <div class="tab-content" id="paymentTabContent">
                                {{-- Thẻ tín dụng --}}
                                <div class="tab-pane fade show active" id="credit" role="tabpanel">
                                    <div class="payment-method-item">
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="payment_method"
                                                id="creditCard" value="credit_card">
                                            <label class="form-check-label" for="creditCard">
                                                <span>Thanh toán bằng thẻ Visa / MasterCard</span>
                                                <span class="payment-icons">
                                                    <img src="https://img.icons8.com/color/32/000000/visa.png" />
                                                    <img src="https://img.icons8.com/color/32/000000/mastercard.png" />
                                                </span>
                                            </label>
                                        </div>
                                        <div class="payment-details mt-2">
                                            <ul>
                                                <li>Bạn sẽ được chuyển đến cổng thanh toán bảo mật</li>
                                                <li>Hỗ trợ Visa, MasterCard, JCB</li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>

                                {{-- MoMo --}}
                                <div class="tab-pane fade" id="momo" role="tabpanel">
                                    <div class="payment-method-item">
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="payment_method"
                                                id="momoPay" value="momo">
                                            <label class="form-check-label" for="momoPay">
                                                <span>Thanh toán bằng Ví MoMo</span>
                                                <span class="payment-icons">
                                                    <img src="https://img.icons8.com/color/32/000000/momo.png" />
                                                </span>
                                            </label>
                                        </div>
                                        <div class="payment-details mt-2">
                                            <ul>
                                                <li>Quét mã QR hoặc đăng nhập MoMo để thanh toán</li>
                                                <li>Bảo mật tuyệt đối</li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>

                                {{-- Chuyển khoản --}}
                                <div class="tab-pane fade" id="bank" role="tabpanel">
                                    <div class="payment-method-item">
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="payment_method"
                                                id="bankTransfer" value="bank_transfer">
                                            <label class="form-check-label" for="bankTransfer">
                                                <span>Chuyển khoản ngân hàng</span>
                                                <i class="fas fa-university text-muted ml-2"></i>
                                            </label>
                                        </div>
                                        <div class="payment-details mt-2">
                                            <ul>
                                                <li>Vui lòng chuyển khoản đúng số tiền và nội dung</li>
                                                <li>Thông tin tài khoản sẽ hiển thị sau khi bạn xác nhận</li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Sidebar tóm tắt đặt phòng --}}
                <div class="col-md-4">
                    <div class="card shadow-sm rounded-lg p-4 h-100 summary-card">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="text-dark mb-0">Tóm tắt khách sạn</h5>
                        </div>

                        <div class="mb-3">
                            <strong>{{ $booking->room->roomType->name }}</strong>
                        </div>

                        <div class="row mb-3 align-items-center">
                            <div class="col-5">
                                <p class="mb-1 text-muted small">Nhận phòng</p>
                                <strong
                                    class="text-dark">{{ \Carbon\Carbon::parse($booking->check_in_date)->translatedFormat('D, d \t\há\n\g m Y') }}</strong><br>
                                <span class="small text-muted">Từ 14:00</span>
                            </div>
                            <div class="col-2 text-center">
                                <i class="fas fa-arrow-right text-muted"></i>
                            </div>
                            <div class="col-5 text-right">
                                <p class="mb-1 text-muted small">Trả phòng</p>
                                <strong
                                    class="text-dark">{{ \Carbon\Carbon::parse($booking->check_out_date)->translatedFormat('D, d \t\há\n\g m Y') }}</strong><br>
                                <span class="small text-muted">Trước 12:00</span>
                            </div>
                        </div>

                        <hr class="my-3">

                        <div class="mb-2">
                            <p class="font-weight-bold">(1x) {{ $booking->room->roomType->name }}</p>
                            <ul class="list-unstyled mt-2 mb-0 text-muted small">
                                <li><i class="fas fa-users mr-1 text-secondary"></i>
                                    {{ $booking->room->roomType->capacity }} khách</li>
                                <li><i class="fas fa-utensils mr-1 text-secondary"></i> Gồm bữa sáng</li>
                                <li><i class="fas fa-wifi mr-1 text-secondary"></i> Wifi miễn phí</li>
                            </ul>
                        </div>

                        <div class="mt-3">
                            <p class="font-weight-bold">Yêu cầu đặc biệt (nếu có)</p>
                            <p class="text-muted small">{{ $booking->special_requests ?? '-' }}</p>
                        </div>

                        <hr class="my-3">

                        <div class="mb-1">
                            <p class="mb-1 text-muted small">Tên khách</p>
                            <strong class="text-dark">{{ $booking->user->name }}</strong>
                        </div>

                        <hr class="my-3">

                        <div class="mb-1">
                            <p class="mb-1 text-muted small">Chi tiết người liên lạc</p>
                            <strong class="text-dark">{{ $booking->user->name }}</strong>
                            <p class="text-dark mb-0"><i class="fas fa-phone-alt mr-1"></i>
                                {{ $booking->user->phone ?? '+84XXXXXXXXX' }}</p>
                            <p class="text-dark mb-0"><i class="fas fa-envelope mr-1"></i> {{ $booking->user->email }}
                            </p>
                        </div>

                        <div class="mt-4 p-3 text-white rounded" style="background-color: #72c02c;">
                            <p class="mb-0 text-center">Sự lựa chọn tuyệt vời cho kỳ nghỉ của bạn!</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <style>
        .card-summary {
            background: #f9fbff;
            border-left: 4px solid #007bff;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
        }

        .card-summary ul li i {
            width: 20px;
        }

        /* --- Custom styles for payment section to match image --- */
        .payment-options {
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            padding: 20px;
            background-color: #ffffff;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
        }

        .payment-options .nav-tabs {
            border-bottom: none;
            margin-bottom: 20px;
        }

        .payment-options .nav-tabs .nav-item .nav-link {
            border: none;
            padding: 8px 15px;
            font-size: 14px;
            color: #666;
            background-color: #f8f9fa;
            border-radius: 5px;
            margin-right: 5px;
            transition: all 0.3s ease;
        }

        .payment-options .nav-tabs .nav-item .nav-link.active {
            background-color: #e6f7ff;
            color: #007bff;
            font-weight: 600;
            border-bottom: 2px solid #007bff;
            /* Underline for active tab */
        }

        .payment-method-item {
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 10px;
            transition: all 0.2s ease-in-out;
            cursor: pointer;
            display: flex;
            flex-direction: column;
            position: relative;
            /* For radio button positioning */
        }

        .payment-method-item.active-payment {
            border-color: #007bff;
            background-color: #e6f7ff;
        }

        .payment-method-item .form-check-input {
            position: absolute;
            left: 15px;
            top: 18px;
            /* Adjust top to align with label */
            margin: 0;
            z-index: 1;
            /* Ensure it's above other elements if needed */
        }

        .payment-method-item .form-check-label {
            width: 100%;
            padding-left: 25px;
            /* Space for custom radio button */
            cursor: pointer;
            font-weight: 500;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .payment-method-item .payment-details {
            padding-left: 25px;
            /* Align details with label text */
            font-size: 13px;
            color: #555;
        }

        .payment-method-item .payment-details ul {
            list-style: disc;
            /* Use disc for bullet points */
            margin-left: 15px;
            padding-left: 0;
            margin-bottom: 0;
        }

        .payment-method-item .payment-details ul li {
            margin-bottom: 5px;
        }

        .payment-icons img,
        .payment-icons i {
            vertical-align: middle;
            margin-left: 5px;
        }

        /* Adjust summary card height to match payment section */
        .summary-card {
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .summary-card .list-unstyled li {
            line-height: 1.5;
        }

        /* Ensure form-check-input doesn't get messed up */
        .form-check-input:checked~.form-check-label::before {
            background-color: #007bff;
            border-color: #007bff;
        }

        .form-check-input:checked~.form-check-label::after {
            border-color: #fff;
        }

        .form-check-input {
            position: relative;
            /* Make it relative to position the actual radio button */
            top: initial;
            left: initial;
            margin-right: 10px;
            /* Space between radio and label */
        }
    </style>
@endsection
