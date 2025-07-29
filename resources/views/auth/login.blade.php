@extends('client.layouts.master')

@section('title', 'Đăng nhập')

@section('content')
<div class="hero-wrap" style="background-image: url('client/images/bg_1.jpg');">
    <div class="overlay"></div>
    <div class="container">
        <div class="row no-gutters slider-text d-flex align-itemd-end justify-content-center">
            <div class="col-md-9 ftco-animate text-center d-flex align-items-end justify-content-center">
                <div class="text">
                    <p class="breadcrumbs mb-2"><span class="mr-2"><a href="{{ route('index') }}">Trang chủ</a></span> <span>Đăng nhập</span></p>
                    <h1 class="mb-4 bread">Đăng nhập</h1>
                </div>
            </div>
        </div>
    </div>
</div>

<section class="ftco-section contact-section bg-light">
    <div class="container">
        <div class="row block-9">
            <div class="col-md-6 mx-auto d-flex">
                <form action="{{ route('login') }}" method="POST" class="bg-white p-5 contact-form w-100">
                    @csrf
                    
                    @if(request()->has('redirect'))
                        <input type="hidden" name="redirect" value="{{ request()->redirect }}">
                    @endif
                    
                    @if($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <div class="form-group">
                        <label for="login">Email hoặc Tên đăng nhập</label>
                        <input type="text" class="form-control @error('login') is-invalid @enderror" id="login" name="login" value="{{ old('login') }}" placeholder="Email hoặc tên đăng nhập" required>
                    </div>

                    <div class="form-group">
                        <label for="password">Mật khẩu</label>
                        <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password" placeholder="Mật khẩu" required>
                    </div>

                    <div class="form-group d-flex justify-content-between align-items-center">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                            <label class="form-check-label" for="remember">
                                Ghi nhớ đăng nhập
                            </label>
                        </div>
                        <a href="{{ route('password.request') }}" class="text-primary">Quên mật khẩu?</a>
                    </div>

                    <div class="form-group">
                        <button type="submit" class="btn btn-primary py-3 px-5">Đăng nhập</button>
                    </div>

                    <div class="form-group text-center">
                        <p>Chưa có tài khoản? <a href="{{ route('register') }}">Đăng ký ngay</a></p>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>
@endsection 