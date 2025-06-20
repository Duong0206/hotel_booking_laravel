@extends('client.layouts.master')

@section('title', 'About Us')

@section('content')
    <div class="hero-wrap" style="background-image: url('client/images/bg_1.jpg');">
        <div class="overlay"></div>
        <div class="container">
            <div class="row no-gutters slider-text d-flex align-itemd-end justify-content-center">
                <div class="col-md-9 ftco-animate text-center d-flex align-items-end justify-content-center">
                    <div class="text">
                        <p class="breadcrumbs mb-2"><span class="mr-2"><a href="{{ route('index') }}">Home</a></span> <span>About</span></p>
                        <h1 class="mb-4 bread">Về Marron Hotel</h1>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <section class="ftco-section bg-light">
        <div class="container">
            <div class="row justify-content-center mb-5 pb-3">
                <div class="col-md-10 heading-section text-center ftco-animate">
                    <span class="subheading">Giới thiệu</span>
                    <h2 class="mb-4">Chào mừng đến với Marron Hotel</h2>
                    <p>Marron Hotel là điểm đến lý tưởng cho kỳ nghỉ dưỡng và công tác của bạn. Chúng tôi cam kết mang đến trải nghiệm lưu trú tuyệt vời với hệ thống phòng nghỉ hiện đại, dịch vụ chuyên nghiệp và không gian sang trọng. Sứ mệnh của chúng tôi là đem lại sự hài lòng tối đa cho mọi khách hàng.</p>
                </div>
            </div>
        </div>
    </section>

    <section class="ftco-section">
        <div class="container">
            <div class="row justify-content-center mb-5 pb-3">
                <div class="col-md-10 heading-section text-center ftco-animate">
                    <span class="subheading">Sản phẩm & Dịch vụ</span>
                    <h2 class="mb-4">Các loại phòng nổi bật</h2>
                    <p>Chúng tôi cung cấp nhiều loại phòng phù hợp với mọi nhu cầu của khách hàng, từ cá nhân, cặp đôi đến gia đình hoặc nhóm bạn.</p>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6 col-lg-4 ftco-animate mb-4">
                    <div class="card h-100 shadow-sm">
                        <div class="card-body text-center">
                            <h3 class="card-title">Phòng Đơn Tiêu Chuẩn</h3>
                            <p class="card-text">Phòng đơn tiêu chuẩn với đầy đủ tiện nghi cơ bản, phù hợp cho 1-2 người.</p>
                            <ul class="list-unstyled mb-2">
                                <li><strong>Sức chứa:</strong> 2 người</li>
                                <li><strong>Giá:</strong> 500.000đ/đêm</li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-lg-4 ftco-animate mb-4">
                    <div class="card h-100 shadow-sm">
                        <div class="card-body text-center">
                            <h3 class="card-title">Phòng Đôi Tiêu Chuẩn</h3>
                            <p class="card-text">Phòng đôi tiêu chuẩn với 2 giường đơn, thích hợp cho gia đình nhỏ hoặc nhóm bạn.</p>
                            <ul class="list-unstyled mb-2">
                                <li><strong>Sức chứa:</strong> 4 người</li>
                                <li><strong>Giá:</strong> 800.000đ/đêm</li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-lg-4 ftco-animate mb-4">
                    <div class="card h-100 shadow-sm">
                        <div class="card-body text-center">
                            <h3 class="card-title">Phòng Deluxe</h3>
                            <p class="card-text">Phòng deluxe với thiết kế sang trọng, đầy đủ tiện nghi cao cấp, view đẹp.</p>
                            <ul class="list-unstyled mb-2">
                                <li><strong>Sức chứa:</strong> 3 người</li>
                                <li><strong>Giá:</strong> 1.200.000đ/đêm</li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-lg-4 ftco-animate mb-4">
                    <div class="card h-100 shadow-sm">
                        <div class="card-body text-center">
                            <h3 class="card-title">Phòng Suite</h3>
                            <p class="card-text">Phòng suite sang trọng bậc nhất với không gian rộng rãi, bao gồm phòng khách và phòng ngủ riêng biệt.</p>
                            <ul class="list-unstyled mb-2">
                                <li><strong>Sức chứa:</strong> 4 người</li>
                                <li><strong>Giá:</strong> 2.000.000đ/đêm</li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-lg-4 ftco-animate mb-4">
                    <div class="card h-100 shadow-sm">
                        <div class="card-body text-center">
                            <h3 class="card-title">Phòng Gia Đình</h3>
                            <p class="card-text">Phòng gia đình rộng rãi với 2 phòng ngủ, phù hợp cho gia đình 4-6 người.</p>
                            <ul class="list-unstyled mb-2">
                                <li><strong>Sức chứa:</strong> 6 người</li>
                                <li><strong>Giá:</strong> 1.500.000đ/đêm</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="ftco-section ftco-counter img" id="section-counter" style="background-image: url(client/images/bg_2.jpg);">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-10">
                    <div class="row">
                        <div class="col-md-3 d-flex justify-content-center counter-wrap ftco-animate">
                            <div class="block-18 text-center">
                                <div class="text">
                                    <strong class="number" data-number="50000">0</strong>
                                    <span>Happy Guests</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 d-flex justify-content-center counter-wrap ftco-animate">
                            <div class="block-18 text-center">
                                <div class="text">
                                    <strong class="number" data-number="3000">0</strong>
                                    <span>Rooms</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 d-flex justify-content-center counter-wrap ftco-animate">
                            <div class="block-18 text-center">
                                <div class="text">
                                    <strong class="number" data-number="1000">0</strong>
                                    <span>Staffs</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 d-flex justify-content-center counter-wrap ftco-animate">
                            <div class="block-18 text-center">
                                <div class="text">
                                    <strong class="number" data-number="100">0</strong>
                                    <span>Destination</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="ftco-section ftc-no-pb ftc-no-pt">
        <div class="container">
            <div class="row">
                <div class="col-md-5 p-md-5 img img-2 img-3 d-flex justify-content-center align-items-center" style="background-image: url(client/images/about.jpg);">
                    <a href="https://vimeo.com/45830194" class="icon popup-vimeo d-flex justify-content-center align-items-center">
                        <span class="icon-play"></span>
                    </a>
                </div>
                <div class="col-md-7 py-5 wrap-about pb-md-5 ftco-animate">
                    <div class="heading-section heading-section-wo-line pt-md-4 mb-5">
                        <div class="ml-md-0">
                            <span class="subheading">Welcome to Deluxe Hotel</span>
                            <h2 class="mb-4">Welcome To Our Hotel</h2>
                        </div>
                    </div>
                    <div class="pb-md-4">
                        <p>On her way she met a copy. The copy warned the Little Blind Text, that where it came from it would have been rewritten a thousand times.</p>
                        <p class="pl-md-5">When she reached the first hills of the Italic Mountains, she had a last view back on the skyline of her hometown Bookmarksgrove, the headline of Alphabet Village and the subline of her own road, the Line Lane. Pityful a rethoric question ran over her cheek, then she continued her way.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="ftco-section">
        <div class="container">
            <div class="row justify-content-center mb-5 pb-3">
                <div class="col-md-7 heading-section text-center ftco-animate">
                    <h2>Our Menu</h2>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="pricing-entry d-flex ftco-animate">
                        <div class="img" style="background-image: url(client/images/menu-1.jpg);"></div>
                        <div class="desc pl-3">
                            <div class="d-flex text align-items-center">
                                <h3><span>Grilled Beef with potatoes</span></h3>
                                <span class="price">$20.00</span>
                            </div>
                            <div class="d-block">
                                <p>A small river named Duden flows by their place and supplies</p>
                            </div>
                        </div>
                    </div>
                    <div class="pricing-entry d-flex ftco-animate">
                        <div class="img" style="background-image: url(client/images/menu-2.jpg);"></div>
                        <div class="desc pl-3">
                            <div class="d-flex text align-items-center">
                                <h3><span>Grilled Beef with potatoes</span></h3>
                                <span class="price">$29.00</span>
                            </div>
                            <div class="d-block">
                                <p>A small river named Duden flows by their place and supplies</p>
                            </div>
                        </div>
                    </div>
                    <div class="pricing-entry d-flex ftco-animate">
                        <div class="img" style="background-image: url(client/images/menu-3.jpg);"></div>
                        <div class="desc pl-3">
                            <div class="d-flex text align-items-center">
                                <h3><span>Grilled Beef with potatoes</span></h3>
                                <span class="price">$20.00</span>
                            </div>
                            <div class="d-block">
                                <p>A small river named Duden flows by their place and supplies</p>
                            </div>
                        </div>
                    </div>
                    <div class="pricing-entry d-flex ftco-animate">
                        <div class="img" style="background-image: url(client/images/menu-4.jpg);"></div>
                        <div class="desc pl-3">
                            <div class="d-flex text align-items-center">
                                <h3><span>Grilled Beef with potatoes</span></h3>
                                <span class="price">$20.00</span>
                            </div>
                            <div class="d-block">
                                <p>A small river named Duden flows by their place and supplies</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="pricing-entry d-flex ftco-animate">
                        <div class="img" style="background-image: url(client/images/menu-5.jpg);"></div>
                        <div class="desc pl-3">
                            <div class="d-flex text align-items-center">
                                <h3><span>Grilled Beef with potatoes</span></h3>
                                <span class="price">$49.91</span>
                            </div>
                            <div class="d-block">
                                <p>A small river named Duden flows by their place and supplies</p>
                            </div>
                        </div>
                    </div>
                    <div class="pricing-entry d-flex ftco-animate">
                        <div class="img" style="background-image: url(client/images/menu-6.jpg);"></div>
                        <div class="desc pl-3">
                            <div class="d-flex text align-items-center">
                                <h3><span>Ultimate Overload</span></h3>
                                <span class="price">$20.00</span>
                            </div>
                            <div class="d-block">
                                <p>A small river named Duden flows by their place and supplies</p>
                            </div>
                        </div>
                    </div>
                    <div class="pricing-entry d-flex ftco-animate">
                        <div class="img" style="background-image: url(client/images/menu-7.jpg);"></div>
                        <div class="desc pl-3">
                            <div class="d-flex text align-items-center">
                                <h3><span>Grilled Beef with potatoes</span></h3>
                                <span class="price">$20.00</span>
                            </div>
                            <div class="d-block">
                                <p>A small river named Duden flows by their place and supplies</p>
                            </div>
                        </div>
                    </div>
                    <div class="pricing-entry d-flex ftco-animate">
                        <div class="img" style="background-image: url(client/images/menu-8.jpg);"></div>
                        <div class="desc pl-3">
                            <div class="d-flex text align-items-center">
                                <h3><span>Ham &amp; Pineapple</span></h3>
                                <span class="price">$20.00</span>
                            </div>
                            <div class="d-block">
                                <p>A small river named Duden flows by their place and supplies</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
