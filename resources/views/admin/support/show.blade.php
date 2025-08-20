@extends('admin.layouts.admin-master')

@section('title', 'Chi tiết hỗ trợ')

@section('styles')
<style>
/* Admin Chat Styles */
:root {
    --primary-color: #1E88E5;
    --secondary-color: #F5F5F5;
    --text-dark: #333333;
    --text-light: #9E9E9E;
    --border-color: #E0E0E0;
    --hover-color: #ECEFF1;
    --success-color: #4CAF50;
    --danger-color: #F44336;
    --online-color: #4CAF50;
    --offline-color: #F44336;
    --neutral-color: #9E9E9E;
}

/* Ẩn page header chỉ trong trang chat */
.page-header {
    display: none !important;
}

/* Ngăn scroll của body và html */
html, body {
    overflow: hidden !important;
    height: 100vh !important;
}

/* Ngăn scroll của main content */
.main-content {
    overflow: hidden !important;
    height: 100vh !important;
    padding: 0;
}

.admin-chat-container {
    height: calc(100vh - 120px);
    max-height: calc(100vh - 120px);
    display: flex;
    background: #FFFFFF;
    font-family: 'Roboto', sans-serif;
    border-radius: 8px;
    overflow: hidden;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    position: relative;
    margin: 20px;
    width: calc(100% - 40px);
}

/* Sidebar */
.chat-sidebar {
    width: 25%;
    border-right: 1px solid var(--border-color);
    display: flex;
    flex-direction: column;
    background: #FFFFFF;
    height: 100%;
}

.sidebar-header {
    padding: 20px;
    border-bottom: 1px solid var(--border-color);
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-shrink: 0;
}

.company-logo {
    width: 32px;
    height: 32px;
    background: var(--primary-color);
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-weight: bold;
    font-size: 14px;
}

.settings-icon {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: background 0.2s;
}

.settings-icon:hover {
    background: var(--hover-color);
}

.search-container {
    padding: 16px 20px;
    border-bottom: 1px solid var(--border-color);
    flex-shrink: 0;
}

.search-input {
    width: 100%;
    padding: 12px 16px;
    border: 1px solid var(--border-color);
    border-radius: 24px;
    font-size: 14px;
    background: var(--secondary-color);
    outline: none;
    transition: border-color 0.2s;
}

.search-input:focus {
    border-color: var(--primary-color);
}

.filter-tabs {
    padding: 16px 20px;
    border-bottom: 1px solid var(--border-color);
    display: flex;
    gap: 8px;
    flex-shrink: 0;
}

.filter-tab {
    padding: 8px 16px;
    border: none;
    border-radius: 16px;
    background: transparent;
    color: var(--text-light);
    font-size: 13px;
    cursor: pointer;
    transition: all 0.2s;
}

.filter-tab.active {
    background: var(--primary-color);
    color: white;
}

.conversations-list {
    flex: 1;
    overflow-y: auto;
    height: 0;
}

.conversation-item {
    padding: 16px 20px;
    display: flex;
    align-items: center;
    gap: 12px;
    cursor: pointer;
    transition: background 0.2s;
    border-bottom: 1px solid var(--border-color);
}

.conversation-item:hover {
    background: var(--hover-color);
}

.conversation-item.active {
    background: var(--primary-color);
    color: white;
}

.conversation-item.active .conversation-meta {
    color: rgba(255, 255, 255, 0.8);
}

.user-avatar {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: var(--primary-color);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-weight: bold;
    font-size: 16px;
    flex-shrink: 0;
}

.conversation-content {
    flex: 1;
    min-width: 0;
}

.conversation-name {
    font-weight: 600;
    font-size: 14px;
    margin-bottom: 4px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.conversation-meta {
    display: flex;
    justify-content: space-between;
    align-items: center;
    font-size: 12px;
    color: var(--text-light);
}

.conversation-last-message {
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    max-width: 150px;
}

.conversation-status {
    display: flex;
    align-items: center;
    gap: 4px;
}

.online-indicator {
    width: 8px;
    height: 8px;
    border-radius: 50%;
    background: var(--success-color);
    transition: background-color 0.3s ease;
}

.online-indicator.offline {
    background: var(--danger-color);
}

.online-indicator.error {
    background: var(--text-light);
}

.unread-badge {
    background: var(--danger-color);
    color: white;
    border-radius: 10px;
    padding: 2px 6px;
    font-size: 10px;
    font-weight: bold;
    min-width: 16px;
    text-align: center;
}

/* Chat Window */
.chat-window {
    width: 70%;
    display: flex;
    flex-direction: column;
    background: #FFFFFF;
    height: 100%;
}

.chat-header {
    padding: 15px 20px;
    border-bottom: 1px solid var(--border-color);
    display: flex;
    align-items: center;
    gap: 12px;
    flex-shrink: 0;
}

.chat-user-info {
    flex: 1;
    display: flex;
    align-items: center;
    gap: 12px;
}

.chat-user-avatar {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: var(--primary-color);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-weight: bold;
    font-size: 16px;
}

.chat-user-details h5 {
    margin: 0;
    font-size: 16px;
    font-weight: 600;
}

.chat-user-status {
    font-size: 12px;
    color: var(--text-light);
    display: flex;
    align-items: center;
    gap: 4px;
}

.chat-actions {
    display: flex;
    gap: 8px;
}

.chat-action-btn {
    width: 32px;
    height: 32px;
    border: none;
    border-radius: 50%;
    background: transparent;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: background 0.2s;
}

.chat-action-btn:hover {
    background: var(--hover-color);
}

.chat-messages {
    flex: 1;
    padding: 15px 20px;
    overflow-y: auto;
    background: var(--secondary-color);
    height: 0;
}

.message {
    margin-bottom: 16px;
    display: flex;
    flex-direction: column;
}

.message.sent {
    align-items: flex-end;
}

.message.received {
    align-items: flex-start;
}

.message-bubble {
    max-width: 70%;
    padding: 12px 16px;
    border-radius: 16px;
    font-size: 14px;
    line-height: 1.4;
    position: relative;
}

.message.sent .message-bubble {
    background: var(--primary-color);
    color: white;
    border-bottom-right-radius: 4px;
}

.message.received .message-bubble {
    background: #F1F1F1;
    color: var(--text-dark);
    border-bottom-left-radius: 4px;
}

.message-time {
    font-size: 11px;
    color: var(--text-light);
    margin-top: 4px;
    text-align: center;
}

.message-status {
    font-size: 11px;
    color: var(--text-light);
    margin-top: 2px;
    display: flex;
    align-items: center;
    gap: 4px;
}

.chat-input-container {
    padding: 15px 20px;
    border-top: 1px solid var(--border-color);
    background: #FFFFFF;
    flex-shrink: 0;
}

.chat-input-wrapper {
    display: flex;
    align-items: flex-end;
    gap: 12px;
    background: var(--secondary-color);
    border-radius: 24px;
    padding: 8px 16px;
}

.chat-input {
    flex: 1;
    border: none;
    background: transparent;
    outline: none;
    font-size: 14px;
    resize: none;
    max-height: 100px;
    min-height: 20px;
    padding: 8px 0;
}

.chat-input::placeholder {
    color: var(--text-light);
}

.chat-attachments {
    display: flex;
    gap: 8px;
}

.attachment-btn {
    width: 32px;
    height: 32px;
    border: none;
    border-radius: 50%;
    background: transparent;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: background 0.2s;
    color: var(--text-light);
}

.attachment-btn:hover {
    background: rgba(0, 0, 0, 0.1);
}

.send-btn {
    width: 36px;
    height: 36px;
    border: none;
    border-radius: 50%;
    background: var(--primary-color);
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all 0.2s;
}

.send-btn:hover {
    background: #1976D2;
    transform: scale(1.05);
}

.send-btn:disabled {
    background: var(--text-light);
    cursor: not-allowed;
    transform: none;
}

/* Attachments Preview */
.attachments-preview {
    padding: 10px 15px;
    border-top: 1px solid var(--border-color);
    background: var(--background-light);
}

.preview-title {
    font-size: 12px;
    color: var(--text-light);
    margin-bottom: 8px;
    font-weight: 500;
}

#previewList {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
}

.attachment-item {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 6px 10px;
    background: white;
    border: 1px solid var(--border-color);
    border-radius: 6px;
    font-size: 12px;
    max-width: 200px;
}

.attachment-item img {
    width: 40px;
    height: 40px;
    object-fit: cover;
    border-radius: 4px;
}

.attachment-item .file-info {
    flex: 1;
    min-width: 0;
}

.attachment-item .file-name {
    font-weight: 500;
    color: var(--text-dark);
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.attachment-item .file-size {
    color: var(--text-light);
    font-size: 11px;
}

.remove-attachment {
    background: none;
    border: none;
    color: var(--danger-color);
    cursor: pointer;
    padding: 2px;
    border-radius: 3px;
    font-size: 14px;
}

.remove-attachment:hover {
    background: var(--danger-color);
    color: white;
}

/* Customer Info */
.customer-info {
    width: 30%;
    border-left: 1px solid var(--border-color);
    background: #FFFFFF;
    display: flex;
    flex-direction: column;
    height: 100%;
}

.customer-header {
    padding: 15px 20px;
    border-bottom: 1px solid var(--border-color);
    text-align: center;
    flex-shrink: 0;
}

.customer-avatar {
    width: 80px;
    height: 80px;
    border-radius: 50%;
    background: var(--primary-color);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-weight: bold;
    font-size: 24px;
    margin: 0 auto 16px;
}

.customer-name {
    font-size: 18px;
    font-weight: 600;
    margin-bottom: 8px;
}

.customer-email {
    font-size: 14px;
    color: var(--text-light);
    margin-bottom: 4px;
}

.customer-phone {
    font-size: 14px;
    color: var(--text-light);
}

.customer-details {
    padding: 15px 20px;
    border-bottom: 1px solid var(--border-color);
    flex-shrink: 0;
}

.detail-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 8px 0;
    font-size: 14px;
}

.detail-label {
    color: var(--text-light);
}

.detail-value {
    font-weight: 500;
}

.chat-history {
    flex: 1;
    padding: 15px 20px;
    overflow-y: auto;
    height: 0;
}

.history-title {
    font-size: 16px;
    font-weight: 600;
    margin-bottom: 16px;
}

.history-item {
    padding: 12px 0;
    border-bottom: 1px solid var(--border-color);
}

.history-date {
    font-size: 12px;
    color: var(--text-light);
    margin-bottom: 4px;
}

.history-subject {
    font-size: 14px;
    font-weight: 500;
    margin-bottom: 4px;
}

.history-status {
    font-size: 12px;
    padding: 2px 8px;
    border-radius: 10px;
    display: inline-block;
}

.history-status.open {
    background: #FFF3E0;
    color: #F57C00;
}

.history-status.closed {
    background: #E8F5E8;
    color: var(--success-color);
}

.internal-notes {
    padding: 15px 20px;
    border-top: 1px solid var(--border-color);
    flex-shrink: 0;
}

.notes-title {
    font-size: 16px;
    font-weight: 600;
    margin-bottom: 12px;
}

.notes-textarea {
    width: 100%;
    min-height: 100px;
    border: 1px solid var(--border-color);
    border-radius: 8px;
    padding: 12px;
    font-size: 14px;
    resize: vertical;
    outline: none;
    transition: border-color 0.2s;
}

.notes-textarea:focus {
    border-color: var(--primary-color);
}

/* Responsive */
@media (max-width: 1200px) {
    .chat-sidebar {
        width: 25%;
    }
    .chat-window {
        width: 55%;
    }
    .customer-info {
        width: 20%;
    }
}

@media (max-width: 768px) {
    .admin-chat-container {
        flex-direction: column;
        height: auto;
    }
    .chat-sidebar,
    .chat-window,
    .customer-info {
        width: 100%;
        height: auto;
    }
}

/* Custom scrollbar */
.chat-messages::-webkit-scrollbar,
.conversations-list::-webkit-scrollbar,
.chat-history::-webkit-scrollbar {
    width: 6px;
}

.chat-messages::-webkit-scrollbar-track,
.conversations-list::-webkit-scrollbar-track,
.chat-history::-webkit-scrollbar-track {
    background: transparent;
}

.chat-messages::-webkit-scrollbar-thumb,
.conversations-list::-webkit-scrollbar-thumb,
.chat-history::-webkit-scrollbar-thumb {
    background: rgba(0, 0, 0, 0.2);
    border-radius: 3px;
}

.chat-messages::-webkit-scrollbar-thumb:hover,
.conversations-list::-webkit-scrollbar-thumb:hover,
.chat-history::-webkit-scrollbar-thumb:hover {
    background: rgba(0, 0, 0, 0.3);
}
</style>
@endsection

@section('content')
<div class="admin-chat-container">
    <!-- Chat Window -->
    <div class="chat-window">
        <div class="chat-header">
            <div class="chat-user-info">
                <div class="chat-user-avatar">{{ substr($conversation['user']->name ?? 'U', 0, 1) }}</div>
                <div>
                    <h5>{{ $conversation['user']->name ?? 'Khách hàng' }}</h5>
                    <div class="chat-user-status">
                        <div class="online-indicator" id="onlineIndicator" style="background-color: var(--text-light);"></div>
                        <span id="statusText">Không xác định</span>
                    </div>
                </div>
            </div>
            <div class="chat-actions">
                <a href="{{ route('admin.support.index') }}" class="chat-action-btn" title="Quay lại danh sách" style="text-decoration: none; color: inherit;">
                    <i class="fas fa-arrow-left"></i>
                </a>
                <button class="chat-action-btn" title="Gắn nhãn">
                    <i class="fas fa-tag"></i>
                </button>
                <button class="chat-action-btn" title="Xem thông tin">
                    <i class="fas fa-info-circle"></i>
                </button>
                <button class="chat-action-btn" title="Đóng hội thoại">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div>

        <div class="chat-messages" id="chatMessages">
            @foreach($messages as $msg)
                @if(!empty(trim($msg->message)))
                    <div class="message {{ $msg->sender_type == 'admin' ? 'sent' : 'received' }}" data-message-id="{{ $msg->id }}">
                        <div class="message-bubble">{{ $msg->message }}</div>
                        <div class="message-time">{{ $msg->created_at->format('H:i') }}</div>
                        @if($msg->sender_type == 'admin')
                            <div class="message-status">
                            </div>
                        @endif
                    </div>
                @endif
            @endforeach
        </div>

        <div class="chat-input-container">
            <form id="chatForm">
                @csrf
                <div class="chat-input-wrapper">
                    <textarea id="chatInput" name="message" class="chat-input" placeholder="Nhập tin nhắn..." required></textarea>
                    <div class="chat-attachments">
                        <input type="file" id="imageInput" accept="image/*" style="display: none;">
                        <button type="button" class="attachment-btn" id="imageBtn" title="Đính kèm ảnh">
                            <i class="fas fa-image"></i>
                        </button>
                        <input type="file" id="fileInput" accept=".pdf,.doc,.docx,.txt,.zip,.rar" style="display: none;">
                        <button type="button" class="attachment-btn" id="fileBtn" title="Đính kèm file">
                            <i class="fas fa-paperclip"></i>
                        </button>
                    </div>
                    <button type="submit" id="sendBtn" class="send-btn" title="Gửi tin nhắn">
                        <i class="fas fa-paper-plane"></i>
                    </button>
                </div>

                <!-- Preview attachments -->
                <div id="attachmentsPreview" class="attachments-preview" style="display: none;">
                    <div class="preview-title">Tệp đính kèm:</div>
                    <div id="previewList"></div>
                </div>
            </form>
        </div>
    </div>

    <!-- Customer Info -->
    <div class="customer-info">
        <div class="customer-header">
            <div class="customer-avatar">{{ substr($conversation['user']->name ?? 'U', 0, 1) }}</div>
            <div class="customer-name">{{ $conversation['user']->name ?? 'Khách hàng' }}</div>
            <div class="customer-email">{{ $conversation['user']->email ?? 'N/A' }}</div>
            <div class="customer-phone">{{ $conversation['user']->phone ?? 'N/A' }}</div>
        </div>

        <div class="customer-details">
            <div class="detail-item">
                <span class="detail-label">Conversation ID:</span>
                <span class="detail-value">#{{ $conversation['id'] }}</span>
            </div>
            <div class="detail-item">
                <span class="detail-label">Chủ đề:</span>
                <span class="detail-value">{{ $conversation['subject'] }}</span>
            </div>
            <div class="detail-item">
                <span class="detail-label">Tạo lúc:</span>
                <span class="detail-value">{{ $conversation['created_at']->format('d/m/Y H:i') }}</span>
            </div>
        </div>

        <div class="chat-history">
            <div class="history-title">Lịch sử chat</div>
            <!-- History items will be loaded here -->
            <div class="history-item">
                <div class="history-date">Hôm nay, 14:30</div>
                <div class="history-subject">Hỗ trợ nhanh</div>
                <span class="history-status open">Đang mở</span>
            </div>
        </div>

        <div class="internal-notes">
            <div class="notes-title">Ghi chú nội bộ</div>
            <textarea class="notes-textarea" placeholder="Ghi chú về khách hàng này..."></textarea>
        </div>
    </div>
</div>

<input type="hidden" id="conversationId" value="{{ $conversation['id'] }}">
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Ngăn scroll của body
    document.body.style.overflow = 'hidden';
    document.documentElement.style.overflow = 'hidden';

    const chatMessages = document.getElementById('chatMessages');
    const chatForm = document.getElementById('chatForm');
    const chatInput = document.getElementById('chatInput');
    const sendBtn = document.getElementById('sendBtn');
    const conversationId = document.getElementById('conversationId').value;
    const searchInput = document.querySelector('.search-input');
    const filterTabs = document.querySelectorAll('.filter-tab');
    const onlineIndicator = document.getElementById('onlineIndicator');
    const statusText = document.getElementById('statusText');

    // Attachment elements
    const imageBtn = document.getElementById('imageBtn');
    const imageInput = document.getElementById('imageInput');
    const fileBtn = document.getElementById('fileBtn');
    const fileInput = document.getElementById('fileInput');
    const attachmentsPreview = document.getElementById('attachmentsPreview');
    const previewList = document.getElementById('previewList');

    // Realtime chat variables
    let isRealtimeEnabled = false;
    let lastMessageId = 0;
    let isSending = false;
    let userStatusCheckInterval;
    let heartbeatInterval;

    // Lưu trữ tin nhắn gần đây để tránh trùng lặp
    let recentMessages = [];
    const MAX_RECENT_MESSAGES = 10;

    // Lưu trữ attachments
    let attachments = [];
    const MAX_ATTACHMENTS = 5;
    const MAX_FILE_SIZE = 10 * 1024 * 1024; // 10MB

    // Khởi tạo lastMessageId từ tin nhắn cuối cùng
    const lastMessage = document.querySelector('.message[data-message-id]');
    if (lastMessage) {
        lastMessageId = parseInt(lastMessage.getAttribute('data-message-id'));
    }

    // Khởi tạo recentMessages từ tin nhắn hiện có
    const existingMessages = document.querySelectorAll('.message');
    existingMessages.forEach(message => {
        const messageBubble = message.querySelector('.message-bubble');
        const messageTime = message.querySelector('.message-time');
        const messageId = message.getAttribute('data-message-id');

        if(messageBubble && messageTime) {
            const content = messageBubble.textContent.trim();
            const senderType = message.classList.contains('sent') ? 'admin' : 'user';

            // Parse thời gian từ text
            const timeText = messageTime.textContent;
            const timeMatch = timeText.match(/(\d{1,2}):(\d{2})/);
            let timestamp = new Date().getTime();

            if(timeMatch) {
                const now = new Date();
                now.setHours(parseInt(timeMatch[1]), parseInt(timeMatch[2]), 0, 0);
                timestamp = now.getTime();
            }

            recentMessages.push({
                content: content,
                senderType: senderType,
                timestamp: timestamp,
                messageId: messageId
            });
        }
    });

    // Giữ chỉ MAX_RECENT_MESSAGES tin nhắn gần nhất
    if(recentMessages.length > MAX_RECENT_MESSAGES) {
        recentMessages = recentMessages.slice(-MAX_RECENT_MESSAGES);
    }

    console.log('Initialized recentMessages:', recentMessages);

    // Bắt đầu realtime khi trang load
    startRealtimeChat();
    startUserStatusMonitoring();
    showAdminChatInfo('Đã kết nối với cuộc trò chuyện!');

    // Auto-resize textarea
    chatInput.addEventListener('input', function() {
        this.style.height = 'auto';
        this.style.height = Math.min(this.scrollHeight, 100) + 'px';
    });

    // Attachment event listeners
    imageBtn.addEventListener('click', () => imageInput.click());
    fileBtn.addEventListener('click', () => fileInput.click());

    imageInput.addEventListener('change', handleImageUpload);
    fileInput.addEventListener('change', handleFileUpload);

    // Send message
    chatForm.addEventListener('submit', function(e) {
        e.preventDefault();
        const message = chatInput.value.trim();
        if (message && !isSending) {
            sendMessage(message);
        }
    });

    // Send message on Enter (but allow Shift+Enter for new line)
    chatInput.addEventListener('keydown', function(e) {
        if (e.key === 'Enter' && !e.shiftKey) {
            e.preventDefault();
            const message = this.value.trim();
            if (message && !isSending) {
                sendMessage(message);
            }
        }
    });

    // Attachment handling functions
    function handleImageUpload(event) {
        const files = Array.from(event.target.files);
        files.forEach(file => {
            if (attachments.length >= MAX_ATTACHMENTS) {
                showAdminChatError(`Chỉ được đính kèm tối đa ${MAX_ATTACHMENTS} tệp!`);
                return;
            }

            if (file.size > MAX_FILE_SIZE) {
                showAdminChatError(`Tệp ${file.name} quá lớn! Kích thước tối đa: ${formatFileSize(MAX_FILE_SIZE)}`);
                return;
            }

            if (!file.type.startsWith('image/')) {
                showAdminChatError(`Tệp ${file.name} không phải là ảnh!`);
                return;
            }

            addAttachment(file, 'image');
        });

        // Reset input
        event.target.value = '';
    }

    function handleFileUpload(event) {
        const files = Array.from(event.target.files);
        files.forEach(file => {
            if (attachments.length >= MAX_ATTACHMENTS) {
                showAdminChatError(`Chỉ được đính kèm tối đa ${MAX_ATTACHMENTS} tệp!`);
                return;
            }

            if (file.size > MAX_FILE_SIZE) {
                showAdminChatError(`Tệp ${file.name} quá lớn! Kích thước tối đa: ${formatFileSize(MAX_FILE_SIZE)}`);
                return;
            }

            addAttachment(file, 'file');
        });

        // Reset input
        event.target.value = '';
    }

    function addAttachment(file, type) {
        const attachment = {
            id: Date.now() + Math.random(),
            file: file,
            type: type,
            name: file.name,
            size: file.size
        };

        attachments.push(attachment);
        updateAttachmentsPreview();
    }

    function removeAttachment(id) {
        attachments = attachments.filter(att => att.id !== id);
        updateAttachmentsPreview();
    }

    function updateAttachmentsPreview() {
        if (attachments.length === 0) {
            attachmentsPreview.style.display = 'none';
            return;
        }

        attachmentsPreview.style.display = 'block';
        previewList.innerHTML = '';

        attachments.forEach(attachment => {
            const item = document.createElement('div');
            item.className = 'attachment-item';

            if (attachment.type === 'image') {
                const reader = new FileReader();
                reader.onload = function(e) {
                    item.innerHTML = `
                        <img src="${e.target.result}" alt="${attachment.name}">
                        <div class="file-info">
                            <div class="file-name">${attachment.name}</div>
                            <div class="file-size">${formatFileSize(attachment.size)}</div>
                        </div>
                        <button class="remove-attachment" onclick="removeAttachment(${attachment.id})">
                            <i class="fas fa-times"></i>
                        </button>
                    `;
                };
                reader.readAsDataURL(attachment.file);
            } else {
                item.innerHTML = `
                    <i class="fas fa-file" style="font-size: 20px; color: var(--text-light);"></i>
                    <div class="file-info">
                        <div class="file-name">${attachment.name}</div>
                        <div class="file-size">${formatFileSize(attachment.size)}</div>
                    </div>
                    <button class="remove-attachment" onclick="removeAttachment(${attachment.id})">
                        <i class="fas fa-times"></i>
                    </button>
                `;
            }

            previewList.appendChild(item);
        });
    }

    function formatFileSize(bytes) {
        if (bytes === 0) return '0 Bytes';
        const k = 1024;
        const sizes = ['Bytes', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
    }

    function sendMessage(message) {
        if(isSending) return;

        isSending = true;
        sendBtn.disabled = true;
        sendBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';

        // Tạo FormData nếu có attachments
        let requestBody;
        let headers;

        if (attachments.length > 0) {
            const formData = new FormData();
            formData.append('message', message);
            formData.append('_token', '{{ csrf_token() }}');

            attachments.forEach((attachment, index) => {
                formData.append(`attachments[${index}]`, attachment.file);
            });

            requestBody = formData;
            headers = {
                'Accept': 'application/json'
            };
        } else {
            requestBody = JSON.stringify({ message: message });
            headers = {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            };
        }

        fetch(`/admin/support/conversation/${conversationId}/message`, {
            method: 'POST',
            headers: headers,
            body: requestBody
        })
        .then(res => res.json())
        .then(data => {
            if(data.success) {
                addMessageToUI(message, 'admin', data.message_id);
                chatInput.value = '';
                chatInput.style.height = 'auto';

                // Xóa attachments sau khi gửi thành công
                if (attachments.length > 0) {
                    attachments = [];
                    updateAttachmentsPreview();
                }

                // Bắt đầu realtime nếu chưa bật
                if(!isRealtimeEnabled) {
                    startRealtimeChat();
                    showAdminChatInfo('Đã bật chế độ realtime!');
                } else {
                    showAdminChatSuccess('Tin nhắn đã được gửi!');
                }
            } else {
                showAdminChatError(data.message || 'Có lỗi khi gửi tin nhắn!');
            }
        })
        .catch(error => {
            console.error('Chat error:', error);
            showAdminChatError('Kết nối mạng có vấn đề. Vui lòng thử lại sau!');
        })
        .finally(() => {
            isSending = false;
            sendBtn.disabled = false;
            sendBtn.innerHTML = '<i class="fas fa-paper-plane"></i>';
        });
    }

    function addMessageToUI(message, senderType, messageId = null, createdAt = null) {
        if(!message || message.trim() === '') return;

        const messageDiv = document.createElement('div');
        messageDiv.className = `message ${senderType === 'admin' ? 'sent' : 'received'}`;
        if(messageId) {
            messageDiv.setAttribute('data-message-id', messageId);
        }

        const messageBubble = document.createElement('div');
        messageBubble.className = 'message-bubble';
        messageBubble.textContent = message.trim();

        const messageTime = document.createElement('div');
        messageTime.className = 'message-time';
        if(createdAt) {
            messageTime.textContent = new Date(createdAt).toLocaleTimeString('vi-VN', {hour: '2-digit', minute:'2-digit'});
        } else {
            messageTime.textContent = new Date().toLocaleTimeString('vi-VN', {hour: '2-digit', minute:'2-digit'});
        }

        messageDiv.appendChild(messageBubble);
        messageDiv.appendChild(messageTime);

        // Thêm trạng thái cho tin nhắn admin
        if(senderType === 'admin') {
            const messageStatus = document.createElement('div');
            messageStatus.className = 'message-status';
            messageStatus.innerHTML = '';
            messageDiv.appendChild(messageStatus);
        }

        chatMessages.appendChild(messageDiv);
        chatMessages.scrollTop = chatMessages.scrollHeight;

        // Lưu tin nhắn vào recentMessages để tránh trùng lặp
        const messageData = {
            content: message.trim(),
            senderType: senderType,
            timestamp: new Date().getTime(),
            messageId: messageId
        };

        recentMessages.push(messageData);

        // Giữ chỉ MAX_RECENT_MESSAGES tin nhắn gần nhất
        if(recentMessages.length > MAX_RECENT_MESSAGES) {
            recentMessages.shift();
        }

        console.log('Added message to UI:', messageData);
    }

    function startRealtimeChat() {
        if(isRealtimeEnabled) return;
        isRealtimeEnabled = true;
        checkNewMessages();
    }

    function stopRealtimeChat() {
        isRealtimeEnabled = false;
    }

    function checkNewMessages() {
        if(!isRealtimeEnabled) return;

        fetch(`/admin/support/conversation/${conversationId}/messages?last_id=${lastMessageId}`)
            .then(res => res.json())
            .then(data => {
                if(data.success && data.messages && data.messages.length > 0) {
                    console.log('Received new messages:', data.messages.length);

                    data.messages.forEach(msg => {
                        // Kiểm tra xem tin nhắn đã tồn tại chưa để tránh duplicate
                        const existingMessage = document.querySelector(`[data-message-id="${msg.id}"]`);

                        if(!existingMessage && msg.id > lastMessageId) {
                            // Kiểm tra thêm xem có tin nhắn trùng nội dung không
                            const duplicateContent = checkDuplicateMessage(msg.message, msg.sender_type);

                            if(!duplicateContent) {
                                addMessageToUI(msg.message, msg.sender_type, msg.id, msg.created_at);
                                lastMessageId = Math.max(lastMessageId, msg.id);

                                // Thông báo khi có tin nhắn mới từ user
                                if(msg.sender_type === 'user') {
                                    showAdminChatInfo('Có tin nhắn mới từ khách hàng!');
                                }
                            } else {
                                console.log('Duplicate message content detected, skipping:', msg.message);
                            }
                        } else if(existingMessage) {
                            console.log('Message already exists with ID:', msg.id);
                        }
                    });
                }
            })
            .catch(error => {
                console.error('Realtime error:', error);
            })
            .finally(() => {
                if(isRealtimeEnabled) {
                    setTimeout(checkNewMessages, 3000);
                }
            });
    }

    // Hàm kiểm tra tin nhắn trùng lặp về nội dung
    function checkDuplicateMessage(messageContent, senderType) {
        const trimmedContent = messageContent.trim();
        const now = new Date().getTime();
        const DUPLICATE_THRESHOLD = 10000; // 10 giây

        // Kiểm tra trong recentMessages
        for(let i = recentMessages.length - 1; i >= 0; i--) {
            const recentMsg = recentMessages[i];

            // Kiểm tra nội dung và sender type
            if(recentMsg.content === trimmedContent && recentMsg.senderType === senderType) {
                // Kiểm tra thời gian
                const timeDiff = now - recentMsg.timestamp;

                if(timeDiff < DUPLICATE_THRESHOLD) {
                    console.log('Duplicate detected in recentMessages:', {
                        content: trimmedContent,
                        senderType: senderType,
                        timeDiff: timeDiff / 1000 + ' seconds',
                        existingMessageId: recentMsg.messageId
                    });
                    return true; // Trùng lặp
                }
            }
        }

        // Kiểm tra thêm trong DOM (backup)
        const messages = document.querySelectorAll('.message');
        const recentDOMMessages = Array.from(messages).slice(-3);

        for(let i = recentDOMMessages.length - 1; i >= 0; i--) {
            const message = recentDOMMessages[i];
            const messageBubble = message.querySelector('.message-bubble');

            if(messageBubble) {
                const existingContent = messageBubble.textContent.trim();
                const existingSenderType = message.classList.contains('sent') ? 'admin' : 'user';

                if(existingContent === trimmedContent && existingSenderType === senderType) {
                    console.log('Duplicate detected in DOM:', {
                        content: trimmedContent,
                        senderType: senderType
                    });
                    return true; // Trùng lặp
                }
            }
        }

        return false; // Không trùng lặp
    }

    function showAdminChatError(message) {
        // Hiển thị lỗi trong chat
        const errorDiv = document.createElement('div');
        errorDiv.className = 'message received';
        errorDiv.innerHTML = `
            <div class="message-bubble" style="background: #ffebee; color: #c62828; border: 1px solid #ffcdd2;">
                <i class="fas fa-exclamation-triangle"></i> ${message}
            </div>
        `;
        chatMessages.appendChild(errorDiv);
        chatMessages.scrollTop = chatMessages.scrollHeight;

        // Tự động ẩn sau 5 giây
        setTimeout(() => {
            if (errorDiv.parentNode) {
                errorDiv.remove();
            }
        }, 5000);
    }

    function showAdminChatInfo(message) {
        // Hiển thị thông tin trong chat
        const infoDiv = document.createElement('div');
        infoDiv.className = 'message received';
        infoDiv.innerHTML = `
            <div class="message-bubble" style="background: #e3f2fd; color: #1976d2; border: 1px solid #bbdefb;">
                <i class="fas fa-info-circle"></i> ${message}
            </div>
        `;
        chatMessages.appendChild(infoDiv);
        chatMessages.scrollTop = chatMessages.scrollHeight;

        // Tự động ẩn sau 3 giây
        setTimeout(() => {
            if (infoDiv.parentNode) {
                infoDiv.remove();
            }
        }, 3000);
    }

    function showAdminChatSuccess(message) {
        // Hiển thị thành công trong chat
        const successDiv = document.createElement('div');
        successDiv.className = 'message received';
        successDiv.innerHTML = `
            <div class="message-bubble" style="background: #e8f5e8; color: #2e7d32; border: 1px solid #c8e6c9;">
                <i class="fas fa-check-circle"></i> ${message}
            </div>
        `;
        chatMessages.appendChild(successDiv);
        chatMessages.scrollTop = chatMessages.scrollHeight;

        // Tự động ẩn sau 3 giây
        setTimeout(() => {
            if (successDiv.parentNode) {
                successDiv.remove();
            }
        }, 3000);
    }

    // Filter tabs
    filterTabs.forEach(tab => {
        tab.addEventListener('click', function() {
            filterTabs.forEach(t => t.classList.remove('active'));
            this.classList.add('active');
            // TODO: Implement filter logic
            console.log('Filter:', this.textContent);
        });
    });

    // Search functionality
    searchInput.addEventListener('input', function() {
        // TODO: Implement search logic
        console.log('Searching for:', this.value);
    });

    // Function to check online status
    function checkOnlineStatus() {
        const userId = {{ $conversation['user']->id ?? 'null' }};
        if (!userId) return;

        fetch(`/admin/support/conversation/${conversationId}/user-status?user_id=${userId}`)
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    const isOnline = data.status === 'online';
                    const lastSeen = data.last_seen;

                                         // Cập nhật UI
                     onlineIndicator.style.backgroundColor = isOnline ? 'var(--online-color)' : 'var(--offline-color)';

                    if (isOnline) {
                        statusText.textContent = 'Online';
                    } else {
                        if (lastSeen) {
                            const lastSeenDate = new Date(lastSeen);
                            const now = new Date();
                            const diffMinutes = Math.floor((now - lastSeenDate) / (1000 * 60));

                            if (diffMinutes < 1) {
                                statusText.textContent = 'Vừa offline';
                            } else if (diffMinutes < 60) {
                                statusText.textContent = `Offline ${diffMinutes} phút trước`;
                            } else {
                                const diffHours = Math.floor(diffMinutes / 60);
                                statusText.textContent = `Offline ${diffHours} giờ trước`;
                            }
                        } else {
                            statusText.textContent = 'Offline';
                        }
                    }
                }
            })
                         .catch(error => {
                 console.error('Error checking online status:', error);
                 onlineIndicator.style.backgroundColor = 'var(--neutral-color)';
                 statusText.textContent = 'Lỗi kết nối';
             });
    }

    // Function to start user status monitoring
    function startUserStatusMonitoring() {
        // Kiểm tra trạng thái mỗi 30 giây
        userStatusCheckInterval = setInterval(checkOnlineStatus, 30000);

        // Kiểm tra lần đầu
        checkOnlineStatus();
    }

    // Function to stop user status monitoring
    function stopUserStatusMonitoring() {
        if (userStatusCheckInterval) {
            clearInterval(userStatusCheckInterval);
        }
    }

    // Function to send heartbeat (simulate user activity)
    function sendHeartbeat() {
        const userId = {{ $conversation['user']->id ?? 'null' }};
        if (!userId) return;

        fetch(`/admin/support/conversation/${conversationId}/status`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                status: 'online',
                user_id: userId
            })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                console.log('Heartbeat sent successfully');
            }
        })
        .catch(error => {
            console.error('Error sending heartbeat:', error);
        });
    }

    // Start heartbeat when page becomes visible
    document.addEventListener('visibilitychange', function() {
        if (!document.hidden) {
            sendHeartbeat();
        }
    });

    // Send heartbeat when user interacts with the page
    document.addEventListener('mousemove', function() {
        sendHeartbeat();
    });

    document.addEventListener('keypress', function() {
        sendHeartbeat();
    });

    // Send heartbeat when user scrolls
    document.addEventListener('scroll', function() {
        sendHeartbeat();
    });

    // Send heartbeat when user clicks
    document.addEventListener('click', function() {
        sendHeartbeat();
    });

    // Cleanup when page is unloaded
    window.addEventListener('beforeunload', function() {
        const userId = {{ $conversation['user']->id ?? 'null' }};
        if (!userId) return;

        // Gửi request để đánh dấu user offline
        navigator.sendBeacon(`/admin/support/conversation/${conversationId}/status`, JSON.stringify({
            status: 'offline',
            user_id: userId
        }));
    });

    // Cleanup when page is hidden
    document.addEventListener('visibilitychange', function() {
        if (document.hidden) {
            const userId = {{ $conversation['user']->id ?? 'null' }};
            if (!userId) return;

            // Gửi request để đánh dấu user offline
            fetch(`/admin/support/conversation/${conversationId}/status`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    status: 'offline',
                    user_id: userId
                })
            }).catch(error => {
                console.error('Error marking user offline:', error);
            });
        }
    });
});
</script>
@endsection
