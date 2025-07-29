@extends('client.layouts.master')

@section('title', 'Đặt Phòng')

@section('content')
    <div class="hero-wrap" style="background-image: url('client/images/bg_1.jpg');">
        <div class="overlay"></div>
        <div class="container">
            <div class="row no-gutters slider-text d-flex align-itemd-end justify-content-center">
                <div class="col-md-9 ftco-animate text-center d-flex align-items-end justify-content-center">
                    <div class="text">
                        <p class="breadcrumbs mb-2"><span class="mr-2"><a href="{{ route('index') }}">Trang chủ</a></span>
                            <span>Đặt Phòng</span></p>
                        <h1 class="mb-4 bread">Đặt Phòng</h1>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <section class="ftco-section contact-section bg-light">
        <div class="container">
            <div class="row block-9">
                <div class="col-md-8 mx-auto d-flex">
                    <div class="bg-white p-5 contact-form w-100">
                        @if (session('success'))
                            <div class="alert alert-success">
                                {{ session('success') }}
                            </div>
                        @endif

                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <h3 class="mb-4 text-center">Thông Tin Đặt Phòng</h3>

                        <form action="{{ route('booking') }}" method="post">
                            @csrf

                            <div class="form-group">
                                <label for="room_type_id">Chọn Phòng</label>
                                <select name="room_type_id" id="room_type_id" class="form-control" required>
                                    <option value="">-- Chọn loại phòng --</option>
                                    @foreach ($roomTypes as $type)
                                        <option value="{{ $type->id }}" data-price="{{ $type->price }}">
                                            {{ $type->name }} - {{ number_format($type->price) }}đ/đêm
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="check_in_date">Ngày Nhận Phòng (Check-in 14:00PM)</label>
                                        <input type="date" name="check_in_date" id="check_in_date" 
                                               class="form-control" required
                                               min="{{ date('Y-m-d') }}" 
                                               value="{{ old('check_in_date', date('Y-m-d')) }}">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="check_out_date">Ngày Trả Phòng (Check-out 12:00PM)</label>
                                        <input type="date" name="check_out_date" id="check_out_date" 
                                               class="form-control" required
                                               min="{{ date('Y-m-d', strtotime('+1 day')) }}" 
                                               value="{{ old('check_out_date', date('Y-m-d', strtotime('+1 day'))) }}">
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="guests">Số Lượng Khách</label>
                                <select name="guests" id="guests" class="form-control" required>
                                    @for ($i = 1; $i <= 5; $i++)
                                        <option value="{{ $i }}" {{ old('guests') == $i ? 'selected' : '' }}>
                                            {{ $i }} {{ $i > 1 ? 'người' : 'người' }}
                                        </option>
                                    @endfor
                                </select>
                            </div>

                            <div class="form-group">
                                <label>Thông Tin Khách Hàng</label>
                                <input type="text" class="form-control" disabled
                                    value="{{ $user->name }} ({{ $user->email }})">
                            </div>

                            <div class="form-group">
                                <label for="phone">Số Điện Thoại</label>
                                <input type="text" name="phone" id="phone" class="form-control"
                                    value="{{ $user->phone ?? old('phone') }}" required>
                            </div>

                            <div class="form-group">
                                <label for="notes">Ghi Chú</label>
                                <textarea name="notes" id="notes" cols="30" rows="3" class="form-control">{{ old('notes') }}</textarea>
                            </div>

                            <div class="price-calculation bg-light p-3 my-4" style="display: none;">
                                <h5>Chi Tiết Thanh Toán</h5>
                                <div class="d-flex justify-content-between">
                                    <span>Giá phòng:</span>
                                    <span id="room-price">0đ</span>
                                </div>
                                <div class="d-flex justify-content-between">
                                    <span>Số đêm:</span>
                                    <span id="nights-count">0</span>
                                </div>
                                <div class="d-flex justify-content-between font-weight-bold mt-2">
                                    <span>Tổng tiền:</span>
                                    <span id="total-price">0đ</span>
                                </div>
                            </div>

                            <div class="form-group text-center">
                                <button type="submit" class="btn btn-primary py-3 px-5">Đặt Phòng</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>

@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const roomSelect = document.getElementById('room_type_id');
            const checkInDateInput = document.getElementById('check_in_date');
            const checkOutDateInput = document.getElementById('check_out_date');
            const priceCalculation = document.querySelector('.price-calculation');
            const roomPriceElement = document.getElementById('room-price');
            const nightsCountElement = document.getElementById('nights-count');
            const totalPriceElement = document.getElementById('total-price');

            function updatePriceCalculation() {
                const selectedOption = roomSelect.options[roomSelect.selectedIndex];
                const checkInDate = new Date(checkInDateInput.value);
                const checkOutDate = new Date(checkOutDateInput.value);

                if (roomSelect.value && checkInDateInput.value && checkOutDateInput.value) {
                    const roomPrice = parseFloat(selectedOption.dataset.price);
                    const timeDiff = checkOutDate - checkInDate;
                    const nights = Math.ceil(timeDiff / (1000 * 3600 * 24));

                    if (nights > 0) {
                        const totalPrice = roomPrice * nights;

                        roomPriceElement.textContent = formatCurrency(roomPrice);
                        nightsCountElement.textContent = nights;
                        totalPriceElement.textContent = formatCurrency(totalPrice);

                        priceCalculation.style.display = 'block';
                    } else {
                        priceCalculation.style.display = 'none';
                    }
                } else {
                    priceCalculation.style.display = 'none';
                }
            }

            function formatCurrency(amount) {
                return new Intl.NumberFormat('vi-VN', {
                    style: 'currency',
                    currency: 'VND'
                }).format(amount);
            }

            roomSelect.addEventListener('change', updatePriceCalculation);
            
            checkInDateInput.addEventListener('change', function() {
                // Cập nhật ngày trả phòng tối thiểu
                const checkInDate = new Date(this.value);
                const nextDay = new Date(checkInDate);
                nextDay.setDate(nextDay.getDate() + 1);

                checkOutDateInput.min = nextDay.toISOString().split('T')[0];

                // Nếu ngày trả phòng nhỏ hơn ngày nhận phòng + 1, thì đặt lại
                if (new Date(checkOutDateInput.value) < nextDay) {
                    checkOutDateInput.value = nextDay.toISOString().split('T')[0];
                }

                updatePriceCalculation();
            });
            
            checkOutDateInput.addEventListener('change', updatePriceCalculation);

            // Khởi tạo khi trang load
            updatePriceCalculation();
        });
    </script>
@endpush
