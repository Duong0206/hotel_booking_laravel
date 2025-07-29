<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Interfaces\Services\AuthServiceInterface;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Log;

class LoginController extends Controller
{
    protected $authService;

    public function __construct(AuthServiceInterface $authService)
    {
        $this->authService = $authService;
    }

    /**
     * Hiển thị form đăng nhập
     */
    public function showLoginForm()
    {
        return view('auth.login');
    }

    /**
     * Xử lý đăng nhập
     */
    public function login(Request $request)
    {
        $validated = $request->validate([
            'login' => ['required', 'string'],
            'password' => ['required', 'string', 'min:8'],
            'remember' => ['nullable', 'in:on,1,true'],
        ]);

        try {
            $this->authService->login(
                $validated,
                $request->input('remember') === 'on'
            );

            $request->session()->regenerate();

            // Kiểm tra role của user để chuyển hướng
            $user = $this->authService->getCurrentUser();
            
            // Debug logging
            \Illuminate\Support\Facades\Log::info('Login attempt', [
                'user_id' => $user->id,
                'user_name' => $user->name,
                'user_email' => $user->email,
                'role_id' => $user->role_id,
                'role_name' => $user->role ? $user->role->name : 'No role'
            ]);
            
            // Kiểm tra redirect parameter
            if ($request->has('redirect') && $request->redirect) {
                $redirectUrl = $request->redirect;
                // Kiểm tra URL có hợp lệ và thuộc domain của ứng dụng
                if (filter_var($redirectUrl, FILTER_VALIDATE_URL) && 
                    parse_url($redirectUrl, PHP_URL_HOST) === request()->getHost()) {
                    return redirect($redirectUrl)->with('success', 'Đăng nhập thành công!');
                }
            }
            
            if ($user && $user->role) {
                // Nếu user có role admin, super_admin hoặc staff thì chuyển đến admin dashboard
                if (in_array($user->role->name, ['admin', 'super_admin', 'staff'])) {
                    \Illuminate\Support\Facades\Log::info('Redirecting to admin dashboard');
                    return redirect()->route('admin.dashboard')->with('success', 'Chào mừng bạn đến với trang quản trị!');
                }
            }

            // Nếu không phải admin thì chuyển đến trang chủ
            \Illuminate\Support\Facades\Log::info('Redirecting to home page');
            return redirect()->route('index')->with('success', 'Đăng nhập thành công!');
        } catch (ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        }
    }

    /**
     * Xử lý đăng xuất
     */
    public function logout(Request $request)
    {
        $this->authService->logout();
        
        return redirect()->route('index');
    }
}
