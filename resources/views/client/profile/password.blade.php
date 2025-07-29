<form action="{{ route('user.profile.change_password') }}" method="POST" autocomplete="off">
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
        <label for="current_password">Mật khẩu hiện tại</label>
        <input type="password" class="form-control" id="current_password" name="current_password" required>
    </div>
    <div class="form-group mb-3">
        <label for="password">Mật khẩu mới</label>
        <input type="password" class="form-control" id="password" name="password" required>
    </div>
    <div class="form-group mb-4">
        <label for="password_confirmation">Xác nhận mật khẩu mới</label>
        <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" required>
    </div>
    <button type="submit" class="btn btn-primary">Đổi mật khẩu</button>
</form> 