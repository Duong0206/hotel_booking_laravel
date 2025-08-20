<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\SupportService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use App\Models\SupportMessage;

class AdminSupportController extends Controller
{
    protected $supportService;

    public function __construct(SupportService $supportService)
    {
        $this->supportService = $supportService;
    }

    public function index()
    {
        $conversations = $this->supportService->getAllConversations();
        return view('admin.support.index', compact('conversations'));
    }

    public function showConversation($conversationId)
    {
        $messages = $this->supportService->getConversationMessages($conversationId);

        if ($messages->isEmpty()) {
            abort(404, 'Không tìm thấy cuộc trò chuyện');
        }

        // Đánh dấu tin nhắn đã đọc
        $this->supportService->markMessagesAsRead($conversationId);

        // Lấy thông tin conversation
        $firstMessage = $messages->first();
        $conversation = [
            'id' => $conversationId,
            'subject' => $firstMessage->subject,
            'user' => $firstMessage->user,
            'created_at' => $firstMessage->created_at
        ];

        // Cập nhật hoạt động của admin khi vào xem conversation
        if (Auth::check()) {
            Cache::put("user_last_activity_" . Auth::id(), now(), 300);
            Cache::put("user_status_" . Auth::id(), 'online', 300);
        }

        return view('admin.support.show', compact('messages', 'conversation'));
    }

    public function sendMessage(Request $request, $conversationId)
    {
        try {
            $request->validate([
                'message' => 'required|string|min:1|max:1000',
                'attachments.*' => 'nullable|file|max:10240', // 10MB max
            ], [
                'message.required' => 'Vui lòng nhập tin nhắn',
                'message.min' => 'Tin nhắn phải có ít nhất 1 ký tự',
                'message.max' => 'Tin nhắn không được quá 1000 ký tự',
                'attachments.*.file' => 'Tệp đính kèm không hợp lệ',
                'attachments.*.max' => 'Tệp đính kèm không được quá 10MB'
            ]);

            $messageText = trim($request->input('message'));
            if(empty($messageText)) {
                if ($request->wantsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Tin nhắn không được để trống'
                    ], 400);
                }
                return redirect()->back()->withErrors(['message' => 'Tin nhắn không được để trống']);
            }

            // Kiểm tra conversation tồn tại
            $firstMessage = SupportMessage::where('conversation_id', $conversationId)->first();
            if (!$firstMessage) {
                if ($request->wantsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Cuộc trò chuyện không tồn tại!'
                    ], 404);
                }
                return redirect()->back()->withErrors(['message' => 'Cuộc trò chuyện không tồn tại!']);
            }

            // Cập nhật hoạt động của admin
            Cache::put("user_last_activity_" . Auth::id(), now(), 300);
            Cache::put("user_status_" . Auth::id(), 'online', 300);

            // Xử lý attachments nếu có
            $attachments = [];
            if ($request->hasFile('attachments')) {
                foreach ($request->file('attachments') as $file) {
                    if ($file->isValid()) {
                        $fileName = time() . '_' . $file->getClientOriginalName();
                        $filePath = $file->storeAs('support_attachments', $fileName, 'public');

                        $attachments[] = [
                            'original_name' => $file->getClientOriginalName(),
                            'file_path' => $filePath,
                            'file_size' => $file->getSize(),
                            'mime_type' => $file->getMimeType()
                        ];
                    }
                }
            }

            // Gửi tin nhắn từ admin
            try {
                $message = $this->supportService->sendMessage(
                    $conversationId,
                    Auth::id(),
                    'admin',
                    $messageText,
                    null, // subject
                    $attachments
                );
            } catch (\Exception $e) {
                Log::error('Error sending admin support message: ' . $e->getMessage());
                if ($request->wantsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Không thể gửi tin nhắn. Vui lòng thử lại!'
                    ], 500);
                }
                return redirect()->back()->withErrors(['message' => 'Không thể gửi tin nhắn. Vui lòng thử lại!']);
            }

            // Nếu request yêu cầu JSON response
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message_id' => $message->id,
                    'message' => 'Tin nhắn đã được gửi'
                ]);
            }

            // Redirect như cũ
            return redirect()->route('admin.support.showConversation', $conversationId)->with('success', 'Đã gửi tin nhắn');

        } catch (\Illuminate\Validation\ValidationException $e) {
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $e->errors()['message'][0] ?? 'Dữ liệu không hợp lệ'
                ], 422);
            }
            throw $e;
        } catch (\Exception $e) {
            Log::error('Unexpected error in admin sendMessage: ' . $e->getMessage());
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Có lỗi xảy ra. Vui lòng thử lại sau!'
                ], 500);
            }
            return redirect()->back()->withErrors(['message' => 'Có lỗi xảy ra. Vui lòng thử lại sau!']);
        }
    }

    public function getNewMessages(Request $request, $conversationId)
    {
        try {
            $lastId = $request->get('last_id', 0);

            // Kiểm tra conversation tồn tại
            $firstMessage = SupportMessage::where('conversation_id', $conversationId)->first();
            if (!$firstMessage) {
                return response()->json([
                    'success' => false,
                    'message' => 'Không tìm thấy cuộc trò chuyện'
                ], 404);
            }

            $messages = $this->supportService->getNewMessages($conversationId, $lastId);

            return response()->json([
                'success' => true,
                'messages' => $messages->map(function($msg) {
                    return [
                        'id' => $msg->id,
                        'message' => $msg->message,
                        'sender_type' => $msg->sender_type,
                        'created_at' => $msg->created_at,
                        'user' => $msg->user ? [
                            'name' => $msg->user->name,
                            'email' => $msg->user->email
                        ] : null
                    ];
                })
            ]);

        } catch (\Exception $e) {
            Log::error('Error getting new messages: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi lấy tin nhắn mới'
            ], 500);
        }
    }

    public function getUpdates(Request $request)
    {
        try {
            $lastUpdate = $request->input('last_update', 0);
            $lastUpdateTime = date('Y-m-d H:i:s', $lastUpdate / 1000);

            // Lấy thống kê hiện tại
            $stats = [
                'unread_count' => SupportMessage::where('sender_type', 'user')->where('is_read', false)->count(),
                'total_count' => SupportMessage::select('conversation_id')->distinct()->count()
            ];

            // Lấy các conversation đã cập nhật
            $updatedConversations = SupportMessage::select('conversation_id')
                ->where('updated_at', '>', $lastUpdateTime)
                ->distinct()
                ->get()
                ->pluck('conversation_id');

            $updates = [];
            foreach ($updatedConversations as $conversationId) {
                $lastMessage = SupportMessage::where('conversation_id', $conversationId)
                    ->with('user')
                    ->orderBy('created_at', 'desc')
                    ->first();

                if ($lastMessage) {
                    $unreadCount = SupportMessage::where('conversation_id', $conversationId)
                        ->where('sender_type', 'user')
                        ->where('is_read', false)
                        ->count();

                    $updates[] = [
                        'conversation_id' => $conversationId,
                        'unread_count' => $unreadCount,
                        'last_message' => $lastMessage->message,
                        'updated_at' => $lastMessage->updated_at,
                        'user' => $lastMessage->user ? [
                            'name' => $lastMessage->user->name,
                            'email' => $lastMessage->user->email
                        ] : null
                    ];
                }
            }

            // Lấy các conversation mới
            $newConversations = SupportMessage::select('conversation_id')
                ->where('created_at', '>', $lastUpdateTime)
                ->distinct()
                ->get()
                ->pluck('conversation_id');

            $newConversationsData = [];
            foreach ($newConversations as $conversationId) {
                $firstMessage = SupportMessage::where('conversation_id', $conversationId)
                    ->with('user')
                    ->orderBy('created_at', 'asc')
                    ->first();

                if ($firstMessage) {
                    $unreadCount = SupportMessage::where('conversation_id', $conversationId)
                        ->where('sender_type', 'user')
                        ->where('is_read', false)
                        ->count();

                    $newConversationsData[] = [
                        'conversation_id' => $conversationId,
                        'subject' => $firstMessage->subject,
                        'unread_count' => $unreadCount,
                        'last_message' => $firstMessage->message,
                        'created_at' => $firstMessage->created_at,
                        'user' => $firstMessage->user ? [
                            'name' => $firstMessage->user->name,
                            'email' => $firstMessage->user->email
                        ] : null
                    ];
                }
            }

            return response()->json([
                'success' => true,
                'stats' => $stats,
                'updates' => $updates,
                'new_conversations' => $newConversationsData
            ]);

        } catch (\Exception $e) {
            Log::error('Error getting updates: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi lấy cập nhật'
            ], 500);
        }
    }

    public function getUserStatus(Request $request, $conversationId)
    {
        try {
            $userId = $request->get('user_id');
            if (!$userId) {
                return response()->json([
                    'success' => false,
                    'message' => 'User ID không hợp lệ'
                ], 400);
            }

            // Kiểm tra conversation tồn tại
            $firstMessage = SupportMessage::where('conversation_id', $conversationId)->first();
            if (!$firstMessage) {
                return response()->json([
                    'success' => false,
                    'message' => 'Không tìm thấy cuộc trò chuyện'
                ], 404);
            }

            // Kiểm tra user tồn tại và thuộc về conversation này
            $user = \App\Models\User::find($userId);
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Không tìm thấy user'
                ], 404);
            }

            // Giả lập trạng thái online/offline dựa trên hoạt động gần đây
            // Trong thực tế, bạn có thể lưu trạng thái này vào database hoặc cache
            $lastActivity = Cache::get("user_last_activity_{$userId}");
            $isOnline = false;
            $lastSeen = null;

            if ($lastActivity) {
                $timeDiff = now()->diffInMinutes($lastActivity);
                $isOnline = $timeDiff <= 5; // Coi là online nếu hoạt động trong 5 phút qua
                if (!$isOnline) {
                    $lastSeen = $lastActivity;
                }
            } else {
                // Nếu không có thông tin hoạt động, coi như offline
                $lastSeen = $user->updated_at ?? now()->subHours(1);
            }

            return response()->json([
                'success' => true,
                'status' => $isOnline ? 'online' : 'offline',
                'last_seen' => $lastSeen
            ]);

        } catch (\Exception $e) {
            Log::error('Error getting user status: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi lấy trạng thái user'
            ], 500);
        }
    }

    public function updateUserStatus(Request $request, $conversationId)
    {
        try {
            $userId = $request->input('user_id');
            $status = $request->input('status');

            if (!$userId || !$status) {
                return response()->json([
                    'success' => false,
                    'message' => 'Dữ liệu không hợp lệ'
                ], 400);
            }

            // Kiểm tra conversation tồn tại
            $firstMessage = SupportMessage::where('conversation_id', $conversationId)->first();
            if (!$firstMessage) {
                return response()->json([
                    'success' => false,
                    'message' => 'Không tìm thấy cuộc trò chuyện'
                ], 404);
            }

            // Kiểm tra user tồn tại
            $user = \App\Models\User::find($userId);
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Không tìm thấy user'
                ], 404);
            }

            // Cập nhật trạng thái user trong cache
            if ($status === 'online') {
                Cache::put("user_last_activity_{$userId}", now(), 300); // Lưu trong 5 phút
                Cache::put("user_status_{$userId}", 'online', 300);
            } else {
                Cache::forget("user_status_{$userId}");
                // Không xóa last_activity để có thể hiển thị "last seen"
            }

            Log::info("User status updated: User {$userId} is now {$status}");

            return response()->json([
                'success' => true,
                'message' => 'Cập nhật trạng thái thành công',
                'status' => $status
            ]);

        } catch (\Exception $e) {
            Log::error('Error updating user status: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi cập nhật trạng thái'
            ], 500);
        }
    }
}
