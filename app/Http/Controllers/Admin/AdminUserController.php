<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Interfaces\Services\UserServiceInterface;
use Illuminate\Http\Request;

class AdminUserController extends Controller
{
    protected $userService;

    public function __construct(UserServiceInterface $userService)
    {
        $this->userService = $userService;
    }

    // Danh sách user
    public function index()
    {
        $users = $this->userService->getAll();
        return view('admin.users.index', compact('users'));
    }

    // Xem chi tiết user
    public function show($id)
    {
        $user = $this->userService->findById($id);
        if (!$user) abort(404);
        return view('admin.users.show', compact('user'));
    }

    // Form sửa user
    public function edit($id)
    {
        $user = $this->userService->findById($id);
        if (!$user) abort(404);
        return view('admin.users.edit', compact('user'));
    }

    // Cập nhật user
    public function update(Request $request, $id)
    {
        $data = $request->only(['name', 'username', 'email', 'phone', 'role_id']);
        $success = $this->userService->updateUser($id, $data);
        
        if (!$success) {
            abort(404);
        }
        
        return redirect()->route('admin.users.show', $id)->with('success', 'Cập nhật thành công!');
    }

    // Xóa user
    public function destroy($id)
    {
        $success = $this->userService->deleteUser($id);
        
        if (!$success) {
            abort(404);
        }
        
        return redirect()->route('admin.users.index')->with('success', 'Đã xóa user!');
    }
} 