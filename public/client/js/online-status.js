/**
 * Online Status Manager for Client Side
 * Quản lý trạng thái online/offline của khách hàng với realtime activity monitoring
 */

class OnlineStatusManager {
    constructor(conversationId, userId) {
        this.conversationId = conversationId;
        this.userId = userId;
        this.status = 'offline';
        this.lastSeen = null;
        this.heartbeatInterval = null;
        this.activityCheckInterval = null;
        this.isPageVisible = true;
        this.isConnected = true;
        this.isTyping = false; // Trạng thái đang gõ tin nhắn

        // Activity tracking
        this.lastActivityTime = Date.now();
        this.activityThreshold = 300000; // 5 phút (300 giây)
        this.isActive = true;
        this.activityEvents = [];
        this.maxActivityEvents = 100; // Giữ tối đa 100 sự kiện gần nhất

        this.init();
    }

    init() {
        // Bắt đầu monitoring
        this.startHeartbeat();
        this.startActivityMonitoring();
        this.setupEventListeners();
        this.markAsOnline();

        console.log('OnlineStatusManager initialized for user:', this.userId);
    }

    setupEventListeners() {
        // Khi trang trở nên visible
        document.addEventListener('visibilitychange', () => {
            if (document.hidden) {
                this.isPageVisible = false;
                this.recordActivity('visibility_change', 'page_hidden');
                this.markAsOffline();
            } else {
                this.isPageVisible = true;
                this.recordActivity('visibility_change', 'page_visible');
                this.markAsOnline();
            }
        });

        // Khi user tương tác với trang - Realtime monitoring
        const events = ['mousemove', 'keypress', 'scroll', 'click', 'touchstart', 'mousedown', 'keydown'];
        events.forEach(event => {
            document.addEventListener(event, (e) => {
                this.recordActivity(event, {
                    x: e.clientX || 0,
                    y: e.clientY || 0,
                    target: e.target.tagName || 'unknown',
                    timestamp: Date.now()
                });

                if (this.isPageVisible) {
                    this.markAsOnline();
                }
            }, { passive: true });
        });

        // Chat-specific events - Đánh dấu hoạt động khi nhắn tin
        this.setupChatEventListeners();

        // Khi trang bị unload
        window.addEventListener('beforeunload', () => {
            this.recordActivity('page_lifecycle', 'beforeunload');
            this.markAsOffline();
        });

        // Khi user rời khỏi trang
        window.addEventListener('pagehide', () => {
            this.recordActivity('page_lifecycle', 'pagehide');
            this.markAsOffline();
        });

        // Khi user quay lại trang
        window.addEventListener('pageshow', () => {
            this.recordActivity('page_lifecycle', 'pageshow');
            this.isPageVisible = true;
            this.markAsOnline();
        });

        // Khi mất kết nối mạng
        window.addEventListener('online', () => {
            this.recordActivity('network', 'online');
            this.isConnected = true;
            this.markAsOnline();
        });

        window.addEventListener('offline', () => {
            this.recordActivity('network', 'offline');
            this.isConnected = false;
            this.markAsOffline();
        });

        // Khi user focus/blur window
        window.addEventListener('focus', () => {
            this.recordActivity('window', 'focus');
            this.markAsOnline();
        });

        window.addEventListener('blur', () => {
            this.recordActivity('window', 'blur');
        });

        // Khi user resize window
        window.addEventListener('resize', () => {
            this.recordActivity('window', 'resize', {
                width: window.innerWidth,
                height: window.innerHeight
            });
        });

        // Khi user scroll (debounced)
        let scrollTimeout;
        window.addEventListener('scroll', () => {
            clearTimeout(scrollTimeout);
            scrollTimeout = setTimeout(() => {
                this.recordActivity('scroll', 'debounced', {
                    scrollY: window.scrollY,
                    scrollX: window.scrollX
                });
            }, 100);
        }, { passive: true });
    }

    setupChatEventListeners() {
        // Tìm tất cả các input, textarea liên quan đến chat
        this.setupChatInputListeners();

        // Theo dõi thay đổi DOM để thêm listeners cho các input mới
        this.observeChatInputs();
    }

    setupChatInputListeners() {
        // Tìm tất cả input và textarea hiện có
        const chatInputs = document.querySelectorAll('input[type="text"], input[type="search"], textarea, [contenteditable="true"]');

        chatInputs.forEach(input => {
            this.addChatInputListener(input);
        });
    }

    addChatInputListener(input) {
        // Đánh dấu input này đã được xử lý
        if (input.dataset.chatListenerAdded) return;
        input.dataset.chatListenerAdded = 'true';

        // Khi user bắt đầu gõ
        input.addEventListener('input', (e) => {
            this.recordActivity('chat', 'typing', {
                inputType: input.type || 'text',
                valueLength: e.target.value?.length || 0,
                isTyping: true
            });
            this.markAsOnline();
            this.markAsTyping(true);
        });

        // Khi user focus vào input
        input.addEventListener('focus', () => {
            this.recordActivity('chat', 'input_focus', {
                inputType: input.type || 'text',
                placeholder: input.placeholder || ''
            });
            this.markAsOnline();
        });

        // Khi user blur khỏi input
        input.addEventListener('blur', () => {
            this.recordActivity('chat', 'input_blur');
            this.markAsTyping(false);
        });

        // Khi user nhấn Enter để gửi tin nhắn
        input.addEventListener('keydown', (e) => {
            if (e.key === 'Enter' && !e.shiftKey) {
                this.recordActivity('chat', 'message_sent', {
                    inputType: input.type || 'text',
                    valueLength: e.target.value?.length || 0,
                    method: 'enter_key'
                });
                this.markAsOnline();
                this.markAsTyping(false);
            }
        });

        // Khi user paste nội dung
        input.addEventListener('paste', () => {
            this.recordActivity('chat', 'content_pasted', {
                inputType: input.type || 'text'
            });
            this.markAsOnline();
        });

        // Khi user copy nội dung
        input.addEventListener('copy', () => {
            this.recordActivity('chat', 'content_copied', {
                inputType: input.type || 'text'
            });
            this.markAsOnline();
        });
    }

    observeChatInputs() {
        // Sử dụng MutationObserver để theo dõi DOM changes
        if (window.MutationObserver) {
            const observer = new MutationObserver((mutations) => {
                mutations.forEach((mutation) => {
                    if (mutation.type === 'childList') {
                        mutation.addedNodes.forEach((node) => {
                            if (node.nodeType === Node.ELEMENT_NODE) {
                                // Kiểm tra node mới có phải là input/textarea không
                                const newInputs = node.querySelectorAll ?
                                    node.querySelectorAll('input[type="text"], input[type="search"], textarea, [contenteditable="true"]') :
                                    [];

                                if (node.matches && node.matches('input[type="text"], input[type="search"], textarea, [contenteditable="true"]')) {
                                    this.addChatInputListener(node);
                                }

                                newInputs.forEach(input => {
                                    this.addChatInputListener(input);
                                });
                            }
                        });
                    }
                });
            });

            // Bắt đầu observe
            observer.observe(document.body, {
                childList: true,
                subtree: true
            });

            // Lưu observer để có thể disconnect sau
            this.chatObserver = observer;
        }
    }

    markAsTyping(isTyping) {
        if (this.isTyping === isTyping) return;

        this.isTyping = isTyping;

        if (isTyping) {
            this.recordActivity('chat', 'typing_started', {
                timestamp: Date.now()
            });
        } else {
            this.recordActivity('chat', 'typing_stopped', {
                timestamp: Date.now()
            });
        }

        // Gửi trạng thái typing ngay lập tức
        this.sendTypingStatus(isTyping);
    }

    async sendTypingStatus(isTyping) {
        try {
            await fetch(`/support/conversation/${this.conversationId}/typing-status`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    is_typing: isTyping,
                    user_id: this.userId
                })
            });
        } catch (error) {
            console.error('Error sending typing status:', error);
        }
    }

    recordActivity(eventType, action, data = {}) {
        const now = Date.now();
        const activity = {
            type: eventType,
            action: action,
            timestamp: now,
            data: data,
            pageVisible: this.isPageVisible,
            networkStatus: this.isConnected ? 'online' : 'offline'
        };

        // Thêm vào danh sách hoạt động
        this.activityEvents.push(activity);

        // Giữ chỉ số lượng sự kiện gần nhất
        if (this.activityEvents.length > this.maxActivityEvents) {
            this.activityEvents.shift();
        }

        // Cập nhật thời gian hoạt động cuối cùng
        this.lastActivityTime = now;
        this.isActive = true;

        // Log hoạt động nếu debug mode
        if (localStorage.getItem('debug_online_status') === 'true') {
            console.log('Activity recorded:', activity);
        }
    }

    startActivityMonitoring() {
        // Kiểm tra hoạt động mỗi 10 giây
        this.activityCheckInterval = setInterval(() => {
            this.checkActivityStatus();
        }, 10000);
    }

    stopActivityMonitoring() {
        if (this.activityCheckInterval) {
            clearInterval(this.activityCheckInterval);
            this.activityCheckInterval = null;
        }
    }

    checkActivityStatus() {
        const now = Date.now();
        const timeSinceLastActivity = now - this.lastActivityTime;

        // Nếu không có hoạt động trong 5 phút, đánh dấu không hoạt động
        if (timeSinceLastActivity > this.activityThreshold) {
            if (this.isActive) {
                this.isActive = false;
                this.recordActivity('system', 'inactive_timeout', {
                    inactiveDuration: timeSinceLastActivity / 1000
                });

                if (localStorage.getItem('debug_online_status') === 'true') {
                    console.log('User marked as inactive after', Math.floor(timeSinceLastActivity / 1000), 'seconds');
                }
            }
        } else {
            if (!this.isActive) {
                this.isActive = true;
                this.recordActivity('system', 'active_resumed');
            }
        }

        // Gửi thống kê hoạt động mỗi phút
        if (now % 60000 < 10000) { // Mỗi phút
            this.sendActivityStats();
        }
    }

    async sendActivityStats() {
        try {
            const stats = {
                total_events: this.activityEvents.length,
                last_activity: this.lastActivityTime,
                is_active: this.isActive,
                page_visible: this.isPageVisible,
                network_status: this.isConnected ? 'online' : 'offline',
                session_duration: Date.now() - (this.activityEvents[0]?.timestamp || Date.now()),
                is_typing: this.isTyping, // Thêm trạng thái typing
                typing_started: this.isTyping ? this.lastActivityTime : null
            };

            // Gửi thống kê đến server
            await fetch(`/support/conversation/${this.conversationId}/activity-stats`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                    'Accept': 'application/json'
                },
                body: JSON.stringify(stats)
            });

            if (localStorage.getItem('debug_online_status') === 'true') {
                console.log('Activity stats sent:', stats);
            }
        } catch (error) {
            console.error('Error sending activity stats:', error);
        }
    }

    startHeartbeat() {
        // Gửi heartbeat mỗi 30 giây
        this.heartbeatInterval = setInterval(() => {
            if (this.isPageVisible && this.isConnected && this.isActive) {
                this.markAsOnline();
            }
        }, 30000);
    }

    stopHeartbeat() {
        if (this.heartbeatInterval) {
            clearInterval(this.heartbeatInterval);
            this.heartbeatInterval = null;
        }
    }

    async markAsOnline() {
        if (this.status === 'online') return;

        try {
            const response = await fetch(`/support/conversation/${this.conversationId}/status`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    status: 'online',
                    activity_data: {
                        is_active: this.isActive,
                        last_activity: this.lastActivityTime,
                        total_events: this.activityEvents.length
                    }
                })
            });

            if (response.ok) {
                this.status = 'online';
                this.lastSeen = new Date();
                console.log('Marked as online');
            }
        } catch (error) {
            console.error('Error marking as online:', error);
        }
    }

    async markAsOffline() {
        if (this.status === 'offline') return;

        try {
            // Sử dụng sendBeacon nếu có thể (để gửi request khi trang đang unload)
            if (navigator.sendBeacon) {
                const data = new FormData();
                data.append('status', 'offline');
                data.append('_token', document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '');
                data.append('activity_data', JSON.stringify({
                    is_active: this.isActive,
                    last_activity: this.lastActivityTime,
                    total_events: this.activityEvents.length
                }));

                navigator.sendBeacon(`/support/conversation/${this.conversationId}/status`, data);
            } else {
                // Fallback cho các trình duyệt không hỗ trợ sendBeacon
                await fetch(`/support/conversation/${this.conversationId}/status`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        status: 'offline',
                        activity_data: {
                            is_active: this.isActive,
                            last_activity: this.lastActivityTime,
                            total_events: this.activityEvents.length
                        }
                    })
                });
            }

            this.status = 'offline';
            console.log('Marked as offline');
        } catch (error) {
            console.error('Error marking as offline:', error);
        }
    }

    // Lấy thống kê hoạt động
    getActivityStats() {
        return {
            isActive: this.isActive,
            lastActivity: this.lastActivityTime,
            totalEvents: this.activityEvents.length,
            timeSinceLastActivity: Date.now() - this.lastActivityTime,
            recentEvents: this.activityEvents.slice(-10) // 10 sự kiện gần nhất
        };
    }

    // Bật/tắt debug mode
    setDebugMode(enabled) {
        if (enabled) {
            localStorage.setItem('debug_online_status', 'true');
            console.log('Debug mode enabled for OnlineStatusManager');
        } else {
            localStorage.removeItem('debug_online_status');
            console.log('Debug mode disabled for OnlineStatusManager');
        }
    }

    destroy() {
        this.stopHeartbeat();
        this.stopActivityMonitoring();

        // Cleanup chat observer
        if (this.chatObserver) {
            this.chatObserver.disconnect();
            this.chatObserver = null;
        }

        // Đánh dấu không còn gõ
        if (this.isTyping) {
            this.markAsTyping(false);
        }

        this.markAsOffline();
        console.log('OnlineStatusManager destroyed');
    }
}

// Export để sử dụng trong các file khác
if (typeof module !== 'undefined' && module.exports) {
    module.exports = OnlineStatusManager;
} else {
    window.OnlineStatusManager = OnlineStatusManager;
}
