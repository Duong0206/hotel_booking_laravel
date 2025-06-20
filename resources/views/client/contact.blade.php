@extends('client.layouts.master')

@section('title', 'Contact Us')

@section('content')
    <div class="hero-wrap" style="background-image: url('client/images/bg_1.jpg');">
        <div class="overlay"></div>
        <div class="container">
            <div class="row no-gutters slider-text d-flex align-itemd-end justify-content-center">
                <div class="col-md-9 ftco-animate text-center d-flex align-items-end justify-content-center">
                    <div class="text">
                        <p class="breadcrumbs mb-2"><span class="mr-2"><a href="{{ route('index') }}">Home</a></span> <span>Contact</span></p>
                        <h1 class="mb-4 bread">Contact Us</h1>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <section class="ftco-section contact-section bg-light">
        <div class="container">
            <div class="row justify-content-center mb-5 pb-3">
                <div class="col-md-8 heading-section text-center ftco-animate">
                    <h2 class="mb-4">Liên hệ với Marron Hotel</h2>
                    <p>Chúng tôi luôn sẵn sàng lắng nghe ý kiến và hỗ trợ bạn!</p>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6 mb-4">
                    <div class="card shadow-lg border-0 rounded-lg p-4 h-100">
                        <form action="{{ route('contact.send') }}" method="POST" class="contact-form">
                            @csrf
                            <div class="form-group mb-3">
                                <label for="name" class="font-weight-bold">Họ tên</label>
                                <input type="text" name="name" class="form-control rounded-pill" placeholder="Nhập họ tên của bạn" required>
                            </div>
                            <div class="form-group mb-3">
                                <label for="email" class="font-weight-bold">Email</label>
                                <input type="email" name="email" class="form-control rounded-pill" placeholder="Nhập email" required>
                            </div>
                            <div class="form-group mb-3">
                                <label for="subject" class="font-weight-bold">Chủ đề</label>
                                <input type="text" name="subject" class="form-control rounded-pill" placeholder="Chủ đề liên hệ" required>
                            </div>
                            <div class="form-group mb-4">
                                <label for="message" class="font-weight-bold">Nội dung</label>
                                <textarea name="message" cols="30" rows="5" class="form-control rounded" placeholder="Nội dung liên hệ..." required></textarea>
                            </div>
                            <div class="form-group text-center">
                                <button type="submit" class="btn btn-primary px-5 py-2 rounded-pill shadow-sm" style="transition:0.3s;">Gửi liên hệ</button>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="col-md-6 mb-4">
                    <div class="card shadow-lg border-0 rounded-lg p-4 h-100 bg-primary text-white">
                        <h4 class="mb-4"><i class="icon-info-circle mr-2"></i> Thông tin liên hệ</h4>
                        <div class="mb-3 d-flex align-items-center">
                            <span class="icon-map-marker display-4 mr-3"></span>
                            <div>
                                <div class="font-weight-bold">Địa chỉ</div>
                                198 West 21th Street, Suite 721 New York NY 10016
                            </div>
                        </div>
                        <div class="mb-3 d-flex align-items-center">
                            <span class="icon-phone display-4 mr-3"></span>
                            <div>
                                <div class="font-weight-bold">Điện thoại</div>
                                <a href="tel://1234567920" class="text-white">+ 1235 2355 98</a>
                            </div>
                        </div>
                        <div class="mb-3 d-flex align-items-center">
                            <span class="icon-envelope display-4 mr-3"></span>
                            <div>
                                <div class="font-weight-bold">Email</div>
                                <a href="mailto:info@yoursite.com" class="text-white">info@yoursite.com</a>
                            </div>
                        </div>
                        <div class="mb-3 d-flex align-items-center">
                            <span class="icon-globe display-4 mr-3"></span>
                            <div>
                                <div class="font-weight-bold">Website</div>
                                <a href="#" class="text-white">yoursite.com</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
