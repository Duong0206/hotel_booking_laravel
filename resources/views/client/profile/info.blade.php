<form action="{{ route('user.profile.update') }}" method="POST" autocomplete="off">
    @csrf
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
    <div class="form-group mb-3">
        <label for="name">Họ tên</label>
        <input type="text" class="form-control" id="name" name="name" value="{{ old('name', $user->name) }}" required>
    </div>
    <div class="form-group mb-3">
        <label for="username">Tên đăng nhập</label>
        <input type="text" class="form-control" id="username" name="username" value="{{ old('username', $user->username) }}" required>
    </div>
    <div class="form-group mb-3">
        <label for="email">Email</label>
        <input type="email" class="form-control" id="email" name="email" value="{{ old('email', $user->email) }}" required>
    </div>
    <div class="form-group mb-4">
        <label for="phone">Số điện thoại</label>
        <input type="text" class="form-control" id="phone" name="phone" value="{{ old('phone', $user->phone) }}">
    </div>
    <button type="submit" class="btn btn-primary">Cập nhật</button>
</form> 