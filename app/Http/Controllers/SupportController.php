<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Interfaces\Services\SupportServiceInterface;
use Illuminate\Support\Facades\Auth;

class SupportController extends Controller
{
    protected $supportService;

    public function __construct(SupportServiceInterface $supportService)
    {
        $this->supportService = $supportService;
    }

    // Danh sách ticket của user
    public function index()
    {
        $tickets = $this->supportService->getUserTickets(Auth::id());
        return view('client.support.index', compact('tickets'));
    }

    // Tạo ticket mới
    public function createTicket(Request $request)
    {
        $request->validate([
            'subject' => 'required|string|max:255',
        ]);
        $ticket = $this->supportService->createTicket(Auth::id(), $request->input('subject'));
        return redirect()->route('support.showTicket', $ticket->id)->with('success', 'Đã tạo yêu cầu hỗ trợ');
    }

    // Xem chi tiết ticket và lịch sử chat
    public function showTicket($id)
    {
        $ticket = $this->supportService->getTicketWithMessages($id);
        return view('client.support.show', compact('ticket'));
    }

    // Gửi tin nhắn mới (sửa lại để nếu chưa có ticket thì tạo ticket trước)
    public function sendMessage(Request $request, $id = null)
    {
        $request->validate([
            'message' => 'required|string',
        ]);
        $userId = Auth::id();
        // Nếu không có ticket id hoặc ticket id không hợp lệ, tạo ticket mới
        if (!$id || !\App\Models\SupportTicket::where('id', $id)->where('user_id', $userId)->exists()) {
            $ticket = $this->supportService->createTicket($userId, 'Hỗ trợ nhanh');
            $id = $ticket->id;
        }
        $this->supportService->sendMessage(
            $id,
            $userId,
            'user',
            $request->input('message')
        );
        return redirect()->route('support.showTicket', $id)->with('success', 'Đã gửi tin nhắn');
    }
}
