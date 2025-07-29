<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Interfaces\Services\SupportServiceInterface;
use Illuminate\Support\Facades\Auth;

class AdminSupportController extends Controller
{
    protected $supportService;

    public function __construct(SupportServiceInterface $supportService)
    {
        $this->supportService = $supportService;
    }

    // Danh sách tất cả ticket
    public function index()
    {
        $tickets = $this->supportService->getAllTickets();
        return view('admin.support.index', compact('tickets'));
    }

    // Xem chi tiết ticket và lịch sử chat
    public function showTicket($id)
    {
        $ticket = $this->supportService->getTicketWithMessages($id);
        return view('admin.support.show', compact('ticket'));
    }

    // Gửi tin nhắn trả lời
    public function sendMessage(Request $request, $id)
    {
        $request->validate([
            'message' => 'required|string',
        ]);
        $this->supportService->sendMessage(
            $id,
            Auth::id(),
            'admin',
            $request->input('message')
        );
        return redirect()->route('admin.support.showTicket', $id)->with('success', 'Đã gửi tin nhắn');
    }
}
