document.addEventListener('DOMContentLoaded', function() {
  const openBtn = document.getElementById('openChatModal');
  const chatBox = document.getElementById('chatBox');
  const closeBtn = document.getElementById('closeChatBox');
  const chatInput = document.getElementById('chatInput');
  const chatForm = document.getElementById('chatForm');
  const chatMessages = document.querySelector('#chatMessages');
  const conversationIdInput = document.getElementById('conversationIdInput');

  // File upload elements
  const attachmentInput = document.getElementById('attachmentInput');
  const filePreview = document.getElementById('filePreview');
  const fileName = document.getElementById('fileName');
  const removeFile = document.getElementById('removeFile');

  // Modal elements
  const imageModal = document.getElementById('imageModal');
  const modalImage = document.getElementById('modalImage');
  const modalClose = document.querySelector('.image-modal-close');

  // Debug info
  console.log('Chat initialized for user:', window.currentUserId || 'unknown');
  console.log('Conversation ID:', conversationIdInput ? conversationIdInput.value : 'none');

  // Biến để lưu trạng thái realtime
  let isRealtimeEnabled = false;
  let lastMessageId = 0;
  let isSending = false;
  let unreadCount = 0;
  let messageCount = 1;
  let newMessageCount = 0;
  let realtimeInterval = null;

  // Khởi tạo lastMessageId từ tin nhắn cuối cùng và đếm số tin nhắn
  if(chatMessages) {
    const lastMessage = chatMessages.querySelector('.message[data-message-id]:last-child');
    if (lastMessage) {
      lastMessageId = parseInt(lastMessage.getAttribute('data-message-id'));
      console.log('Last message ID:', lastMessageId);
    }
  }

  // Hàm cập nhật counter tin nhắn
  function updateMessageCounter() {
    const counter = document.getElementById('messageCounter');
    if(counter) {
      counter.textContent = `Messages: ${messageCount}`;
      console.log('Updated message counter to:', messageCount);
    }
  }

  // Đếm số tin nhắn hiện tại
  if(chatMessages) {
    messageCount = chatMessages.querySelectorAll('.message').length;
    updateMessageCounter();
  }

  // Khởi tạo newMessageCount = 0 (chỉ đếm tin nhắn realtime mới)
  newMessageCount = 0;

  // Hàm cập nhật badge tin nhắn mới trên button
  function updateNewMessageBadge() {
    const badge = document.getElementById('chatNotificationBadge');
    if(badge) {
      if(newMessageCount > 0) {
        badge.textContent = newMessageCount;
        badge.style.display = 'flex';
        console.log('Updated new message badge to:', newMessageCount);
      } else {
        badge.style.display = 'none';
      }
    }
  }

  // Hàm phát âm thanh thông báo
  function playNotificationSound() {
    try {
      // Tạo âm thanh thông báo bằng Web Audio API
      const audioContext = new (window.AudioContext || window.webkitAudioContext)();
      const oscillator = audioContext.createOscillator();
      const gainNode = audioContext.createGain();

      oscillator.connect(gainNode);
      gainNode.connect(audioContext.destination);

      // Cấu hình âm thanh (tần số 800Hz, âm nhẹ)
      oscillator.frequency.setValueAtTime(800, audioContext.currentTime);
      oscillator.type = 'sine';

      // Cấu hình âm lượng (fade in/out)
      gainNode.gain.setValueAtTime(0, audioContext.currentTime);
      gainNode.gain.linearRampToValueAtTime(0.6, audioContext.currentTime + 0.1);
      gainNode.gain.linearRampToValueAtTime(0, audioContext.currentTime + 0.6);

      // Phát âm thanh trong 0.3 giây
      oscillator.start(audioContext.currentTime);
      oscillator.stop(audioContext.currentTime + 0.6);

      console.log('Notification sound played');
    } catch (error) {
      console.log('Could not play notification sound:', error);
    }

    // Hàm cập nhật counter tin nhắn
    function updateMessageCounter() {
        const counter = document.getElementById('messageCounter');
        if (counter) {
            counter.textContent = `Messages: ${messageCount}`;
            console.log('Updated message counter to:', messageCount);
        }
    }

    // Đếm số tin nhắn hiện tại
    if (chatMessages) {
        messageCount = chatMessages.querySelectorAll('.message').length;
        updateMessageCounter();
    }

    // Khởi tạo newMessageCount = 0 (chỉ đếm tin nhắn realtime mới)
    // Badge sẽ chỉ mất khi người dùng nhấn vào button để mở chat
    // newMessageCount = 0 → badge ẩn, newMessageCount > 0 → badge hiển thị
    newMessageCount = 0;

         // Hàm cập nhật badge tin nhắn mới trên button
     // Badge sẽ hiển thị tổng số tin nhắn mới và chỉ mất khi người dùng nhấn vào button
     function updateNewMessageBadge() {
         const badge = document.getElementById('chatNotificationBadge');
         if (badge) {
             // Kiểm tra chính xác: nếu có tin nhắn mới thì hiển thị, không có thì ẩn
             if (newMessageCount > 0) {
                 badge.textContent = newMessageCount;
                 badge.style.display = 'flex';
                 console.log('Badge displayed with count:', newMessageCount);
             } else {
                 badge.style.display = 'none';
                 console.log('Badge hidden - newMessageCount = 0');
             }
         }
     }

    // Hàm phát âm thanh thông báo
    function playNotificationSound() {
        try {
            // Tạo âm thanh thông báo bằng Web Audio API
            const audioContext = new (window.AudioContext || window.webkitAudioContext)();
            const oscillator = audioContext.createOscillator();
            const gainNode = audioContext.createGain();

            oscillator.connect(gainNode);
            gainNode.connect(audioContext.destination);

            // Cấu hình âm thanh (tần số 800Hz, âm nhẹ)
            oscillator.frequency.setValueAtTime(800, audioContext.currentTime);
            oscillator.type = 'sine';

            // Cấu hình âm lượng (fade in/out)
            gainNode.gain.setValueAtTime(0, audioContext.currentTime);
            gainNode.gain.linearRampToValueAtTime(0.1, audioContext.currentTime + 0.1);
            gainNode.gain.linearRampToValueAtTime(0, audioContext.currentTime + 0.3);

            // Phát âm thanh trong 0.3 giây
            oscillator.start(audioContext.currentTime);
            oscillator.stop(audioContext.currentTime + 0.3);

            console.log('Notification sound played');
        } catch (error) {
            console.log('Could not play notification sound:', error);
        }
    }

    // Event listeners
    if (openBtn) {
        openBtn.onclick = function () {
            console.log('Open chat button clicked');

            // Reset new message count khi người dùng click vào button để xem tin nhắn
            // Đây là thời điểm DUY NHẤT badge bị mất - khi người dùng chủ động mở chat
            if (newMessageCount > 0) {
                newMessageCount = 0;
                updateNewMessageBadge();
                console.log('New message count reset to 0 when opening chat');
            }

            if (chatBox) {
                chatBox.style.display = 'flex';
                console.log('Chat box displayed');
                setTimeout(() => {
                    if (chatInput) {
                        chatInput.focus();
                        console.log('Chat input focused');
                    }
                    // Scroll xuống cuối tin nhắn khi mở modal
                    if (chatMessages) {
                        chatMessages.scrollTop = chatMessages.scrollHeight;
                        console.log('Scrolled to bottom');
                    }
                }, 200);

                // Reset unread count khi mở chat
                updateUnreadBadge(0);
                console.log('Chat opened, unread count reset to 0');

                // Đảm bảo realtime chat đang chạy khi mở modal
                if (conversationIdInput && conversationIdInput.value && !isRealtimeEnabled) {
                    console.log('Starting realtime chat when opening modal');
                    startRealtimeChat();
                }
            } else {
                console.error('Chat box element not found');
            }
        };
    } else {
        console.error('Open chat button not found');
    }

    // Bắt đầu realtime nếu đã có conversation
    if (conversationIdInput) {
        const conversationId = conversationIdInput.value;
        if (conversationId) {
            console.log('Starting realtime for conversation:', conversationId);
            startRealtimeChat();
            showChatInfo('Đã kết nối với cuộc trò chuyện! Badge sẽ hiển thị số tin nhắn mới khi có tin nhắn đến.');
        } else {
            showChatInfo('Chào mừng! Hãy gửi tin nhắn để bắt đầu.');
        }
    }

    if (closeBtn) {
        closeBtn.onclick = function () {
            console.log('Close chat button clicked');
            if (chatBox) {
                chatBox.style.display = 'none';
                console.log('Chat box hidden');

                // Hiển thị thông báo khi đóng modal
                showChatInfo('Chat đã được đóng. Badge sẽ hiển thị số tin nhắn mới khi có tin nhắn đến!');
            }
            // KHÔNG dừng realtime chat khi đóng modal
            // Realtime vẫn chạy để nhận tin nhắn mới
            console.log('Realtime chat continues running in background');
        };
    }

    // Hàm gửi tin nhắn
    function sendMessage(message, attachment = null) {
        if (isSending) return;

        isSending = true;
        const sendBtn = document.getElementById('sendChatBtn');
        if (sendBtn) {
            sendBtn.disabled = true;
            sendBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
        }

        const conversationId = conversationIdInput ? conversationIdInput.value : '';
        let url = '';

        // Tạo FormData nếu có file, JSON nếu không có file
        let requestData;
        let headers;

        if (attachment) {
            // Gửi file với FormData
            requestData = new FormData();
            requestData.append('message', message);
            requestData.append('attachment', attachment);
            requestData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));
            headers = {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json'
            };
        } else {
            // Gửi text với JSON
            requestData = JSON.stringify({
                message: message,
                _token: document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            });
            headers = {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json'
            };
        }

        if (conversationId) {
            url = '/support/conversation/' + conversationId + '/message';
            console.log('Sending message to existing conversation:', conversationId);
        } else {
            url = '/support/message';
            console.log('Creating new conversation for user:', window.currentUserId || 'unknown');
        }

        fetch(url, {
            method: 'POST',
            headers: headers,
            body: requestData
        })
            .then(res => {
                if (!res.ok) {
                    throw new Error(`HTTP error! status: ${res.status}`);
                }
                return res.json();
            })
            .then(data => {
                console.log('Send message response:', data);
                if (data.success) {
                    // Cập nhật conversation ID nếu là tin nhắn đầu tiên
                    if (!conversationId && data.conversation_id && conversationIdInput) {
                        conversationIdInput.value = data.conversation_id;
                        console.log('New conversation created:', data.conversation_id);
                        showChatSuccess('Cuộc trò chuyện đã được tạo! Badge sẽ hiển thị số tin nhắn mới khi có tin nhắn đến.');
                    }

                    // Thêm tin nhắn vào UI
                    addMessageToUI(message, 'user', data.message_id, data.attachment);
                    if (chatInput) {
                        chatInput.value = '';
                        chatInput.style.height = 'auto';
                    }

                    // Xóa file preview nếu có
                    if (attachment) {
                        clearFilePreview();
                    }

                    // Bắt đầu realtime sau khi gửi tin nhắn đầu tiên
                    if (!isRealtimeEnabled) {
                        startRealtimeChat();
                        showChatInfo('Đã bật chế độ realtime! Badge sẽ hiển thị số tin nhắn mới khi có tin nhắn đến.');
                    }
                } else {
                    showChatError(data.message || 'Có lỗi khi gửi tin nhắn!');
                }
            })
            .catch((error) => {
                console.error('Chat error:', error);
                showChatError('Kết nối mạng có vấn đề. Vui lòng thử lại sau! Badge vẫn sẽ hiển thị tin nhắn mới.');
            })
            .finally(() => {
                isSending = false;
                if (sendBtn) {
                    sendBtn.disabled = false;
                    sendBtn.innerHTML = '<i class="fas fa-paper-plane"></i>';
                }
            });
    }

    // Hàm hiển thị lỗi chat
    function showChatError(message) {
        const errorDiv = document.createElement('div');
        errorDiv.className = 'chat-error';
        errorDiv.innerHTML = `
      <i class="fas fa-exclamation-triangle"></i>
      <span>${message}</span>
    `;

        if (chatMessages) {
            chatMessages.insertBefore(errorDiv, chatMessages.firstChild);

            setTimeout(() => {
                if (errorDiv.parentNode) {
                    errorDiv.remove();
                }
            }, 5000);
        }
    }

    // Hàm hiển thị thông báo thành công
    function showChatSuccess(message) {
        const successDiv = document.createElement('div');
        successDiv.className = 'chat-success';
        successDiv.innerHTML = `
      <i class="fas fa-check-circle"></i>
      <span>${message}</span>
    `;

        if (chatMessages) {
            chatMessages.insertBefore(successDiv, chatMessages.firstChild);

            setTimeout(() => {
                if (successDiv.parentNode) {
                    successDiv.remove();
                }
            }, 3000);
        }
    }

    // Hàm hiển thị thông báo thông tin
    function showChatInfo(message) {
        const infoDiv = document.createElement('div');
        infoDiv.className = 'chat-info';
        infoDiv.innerHTML = `
      <i class="fas fa-info-circle"></i>
      <span>${message}</span>
    `;

        if (chatMessages) {
            chatMessages.insertBefore(infoDiv, chatMessages.firstChild);

            setTimeout(() => {
                if (infoDiv.parentNode) {
                    infoDiv.remove();
                }
            }, 4000);
        }
    }

    // Auto-resize textarea
    if (chatInput) {
        chatInput.addEventListener('input', function () {
            this.style.height = 'auto';
            this.style.height = Math.min(this.scrollHeight, 100) + 'px';
        });
    }

    // KHÔNG reset new message count khi scroll - chỉ reset khi người dùng nhấn vào button
    if (chatMessages) {
        chatMessages.addEventListener('scroll', function () {
            // Không làm gì khi scroll - giữ nguyên số tin nhắn mới
            console.log('Scrolled but keeping new message count:', newMessageCount);
        });
    }

    // Hàm kiểm tra tin nhắn trùng lặp
    function checkDuplicateMessage(message, senderType) {
        if (!message || message.trim() === '') return false;

        const messages = chatMessages.querySelectorAll('.message');
        const lastFewMessages = Array.from(messages).slice(-5); // Kiểm tra 5 tin nhắn cuối

        return lastFewMessages.some(msgDiv => {
            const bubble = msgDiv.querySelector('.message-bubble');
            const isSameSender = msgDiv.classList.contains(senderType === 'user' ? 'sent' : 'received');
            return bubble && bubble.textContent.trim() === message.trim() && isSameSender;
        });
    }

    // Hàm thêm tin nhắn vào UI
    function addMessageToUI(message, senderType, messageId = null, createdAt = null, attachment = null) {
        // Cho phép gửi chỉ attachment (không cần text)
        if ((!message || message.trim() === '') && !attachment) return;

        const messageDiv = document.createElement('div');
        messageDiv.className = `message ${senderType === 'user' ? 'sent' : 'received'}`;

        if (messageId) {
            messageDiv.setAttribute('data-message-id', messageId);
        }

        // Thêm tin nhắn text nếu có
        if (message && message.trim()) {
            const messageBubble = document.createElement('div');
            messageBubble.className = 'message-bubble';
            messageBubble.textContent = message.trim();
            messageDiv.appendChild(messageBubble);
        }

        // Thêm file đính kèm nếu có
        if (attachment) {
            if (attachment.type && attachment.type.startsWith('image/')) {
                // Hiển thị ảnh
                const imageBubble = document.createElement('div');
                imageBubble.className = 'message-bubble';
                imageBubble.style.cursor = 'pointer';
                imageBubble.onclick = () => openImageModal(attachment.url);

                const img = document.createElement('img');
                img.src = attachment.url;
                img.alt = 'attachment';
                img.style.maxWidth = '200px';
                img.style.maxHeight = '150px';
                img.style.borderRadius = '8px';

                imageBubble.appendChild(img);
                messageDiv.appendChild(imageBubble);
            } else {
                // Hiển thị file khác
                const fileBubble = document.createElement('div');
                fileBubble.className = 'message-bubble';

                const fileLink = document.createElement('a');
                fileLink.href = attachment.url;
                fileLink.target = '_blank';
                fileLink.rel = 'noopener';
                fileLink.textContent = attachment.name || 'Tệp đính kèm';

                fileBubble.appendChild(fileLink);
                messageDiv.appendChild(fileBubble);
            }
        }

        const messageTime = document.createElement('div');
        messageTime.className = 'message-time';
        if (createdAt) {
            messageTime.textContent = new Date(createdAt).toLocaleTimeString('vi-VN', { hour: '2-digit', minute: '2-digit' });
        } else {
            messageTime.textContent = new Date().toLocaleTimeString('vi-VN', { hour: '2-digit', minute: '2-digit' });
        }

        messageDiv.appendChild(messageTime);
        if (chatMessages) {
            chatMessages.appendChild(messageDiv);

            // Cập nhật counter tin nhắn
            messageCount++;
            updateMessageCounter();
            console.log('Message added to UI, new count:', messageCount);

            // Scroll xuống tin nhắn mới nhất
            chatMessages.scrollTop = chatMessages.scrollHeight;

            // KHÔNG reset new message count khi hiển thị tin nhắn - chỉ reset khi người dùng nhấn vào button
            console.log('Message displayed but keeping new message count:', newMessageCount);
        }
    }

    // Hàm bắt đầu realtime chat
    function startRealtimeChat() {
        if (isRealtimeEnabled) return;

        isRealtimeEnabled = true;
        console.log('Starting realtime chat...');

        // Cập nhật lastMessageId nếu chưa có
        if (lastMessageId === 0) {
            const lastMessage = chatMessages.querySelector('.message[data-message-id]:last-child');
            if (lastMessage) {
                lastMessageId = parseInt(lastMessage.getAttribute('data-message-id'));
                console.log('Updated last message ID:', lastMessageId);
            }
        }

        console.log('Starting realtime chat with conversation ID:', conversationIdInput ? conversationIdInput.value : 'none');

        // Kiểm tra ngay lập tức
        if (conversationIdInput && conversationIdInput.value) {
            checkNewMessages();
        }

        realtimeInterval = setInterval(() => {
            if (conversationIdInput && conversationIdInput.value) {
                checkNewMessages();
            }
        }, 3000); // Kiểm tra mỗi 3 giây

        console.log('Realtime chat started and will continue running even when modal is closed. Badge will show new messages.');
    }

    // Hàm dừng realtime chat (chỉ sử dụng khi cần thiết)
    function stopRealtimeChat() {
        if (realtimeInterval) {
            clearInterval(realtimeInterval);
            realtimeInterval = null;
        }
        isRealtimeEnabled = false;
        console.log('Stopped realtime chat - only use when absolutely necessary. Badge will not show new messages.');
    }

    // Hàm kiểm tra tin nhắn mới
    function checkNewMessages() {
        if (!isRealtimeEnabled) {
            console.log('Realtime not enabled, restarting... Badge will show new messages');
            startRealtimeChat();
            return;
        }

        const conversationId = conversationIdInput ? conversationIdInput.value : '';
        if (!conversationId) {
            console.log('No conversation ID, waiting for new message... Badge will show when message arrives');
            return;
        }

        console.log('Checking new messages for conversation:', conversationId, 'last ID:', lastMessageId);

        fetch(`/support/conversation/${conversationId}/messages?last_id=${lastMessageId}`, {
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
            .then(res => res.json())
            .then(data => {
                if (data.success && data.messages && data.messages.length > 0) {
                    console.log('Received new messages:', data.messages.length);

                    data.messages.forEach(msg => {
                        // Kiểm tra tin nhắn đã tồn tại chưa
                        const existingMessage = document.querySelector(`[data-message-id="${msg.id}"]`);
                        if (!existingMessage && msg.id > lastMessageId) {
                            const duplicateContent = checkDuplicateMessage(msg.message, msg.sender_type);
                            if (!duplicateContent) {
                                console.log('Adding new message:', msg.id, msg.message);
                                addMessageToUI(msg.message, msg.sender_type, msg.id, msg.created_at, msg.attachment);
                                lastMessageId = Math.max(lastMessageId, msg.id);

                                // Thông báo khi nhận tin nhắn mới từ admin (chỉ tin nhắn realtime)
                                if (msg.sender_type === 'admin') {
                                    console.log('New realtime admin message received');

                                    // Phát âm thanh thông báo
                                    playNotificationSound();

                                                                     // Hiển thị toast notification đẹp mắt
                                 if (window.ToastNotification && window.ToastNotification.isAvailable()) {
                                     try {
                                         const toast = window.ToastNotification.showNewMessage('Hỗ trợ', msg.message);
                                         if (toast) {
                                             console.log('Toast notification displayed successfully');
                                         } else {
                                             console.log('Toast notification skipped (user in chat)');
                                         }
                                     } catch (error) {
                                         console.error('Error showing toast notification:', error);
                                         showChatSuccess('Bạn có tin nhắn mới từ hỗ trợ! Badge đã được cập nhật.');
                                     }
                                 } else {
                                     console.log('Toast notification system not available, using fallback');
                                     showChatSuccess('Bạn có tin nhắn mới từ hỗ trợ! Badge đã được cập nhật.');
                                 }

                                    // Cập nhật badge nếu chat đang đóng
                                    // Badge sẽ tích lũy tổng số tin nhắn mới cho đến khi người dùng nhấn vào button
                                    if (chatBox && (chatBox.style.display === 'none' || chatBox.style.display === '')) {
                                        newMessageCount++;
                                        updateNewMessageBadge(); // Tự động hiển thị/ẩn badge dựa trên newMessageCount
                                        fetchUnreadCount();
                                        console.log('Badge updated for closed chat, count:', newMessageCount);
                                    } else {
                                        // Nếu chat đang mở, không cần cập nhật badge vì người dùng đã thấy tin nhắn
                                        console.log('Chat is open, no need to update badge');
                                    }
                                }
                            } else {
                                console.log('Duplicate message detected, skipping:', msg.message);
                            }
                        } else {
                            console.log('Message already exists or ID not greater:', msg.id, 'lastMessageId:', lastMessageId);
                        }
                    });
                }
            })
            .catch(err => {
                console.error('Error checking new messages:', err);
                // Không dừng realtime khi có lỗi, tiếp tục thử lại
                console.log('Realtime chat continues despite error, badge will still show new messages');
            })
            .finally(() => {
                // Tiếp tục polling nếu realtime vẫn được bật
                if (isRealtimeEnabled) {
                    setTimeout(checkNewMessages, 3000);
                } else {
                    // Nếu realtime bị tắt, khởi động lại
                    console.log('Realtime was disabled, restarting... Badge will continue to show new messages');
                    startRealtimeChat();
                }
            });
    }

    // Form submit handler
    if (chatForm) {
        chatForm.onsubmit = function (e) {
            e.preventDefault();
            const msg = chatInput ? chatInput.value.trim() : '';
            const attachment = attachmentInput ? attachmentInput.files[0] : null;

            if ((!msg || msg.trim() === '') && !attachment) {
                showChatError('Vui lòng nhập tin nhắn hoặc chọn tệp đính kèm');
                return;
            }

            if (isSending) return;

            // Gửi tin nhắn với attachment (có thể chỉ có attachment)
            sendMessage(msg || '', attachment);
        };
    }

    // Enter key handler
    if (chatInput) {
        chatInput.addEventListener('keydown', function (e) {
            if (e.key === 'Enter' && !e.shiftKey) {
                e.preventDefault();
                const msg = this.value.trim();
                const attachment = attachmentInput ? attachmentInput.files[0] : null;
                if ((!msg || msg.trim() === '') && !attachment) return;
                if (isSending) return;
                sendMessage(msg || '', attachment);
            }
        });

        // Paste image handler - cho phép copy & paste ảnh trực tiếp
        chatInput.addEventListener('paste', function (e) {
            const items = (e.clipboardData || e.originalEvent.clipboardData).items;

            for (let item of items) {
                if (item.type.indexOf('image') !== -1) {
                    e.preventDefault();

                    const file = item.getAsFile();
                    if (file) {
                        // Tạo FileList object để gán vào input
                        const dataTransfer = new DataTransfer();
                        dataTransfer.items.add(file);
                        if (attachmentInput) attachmentInput.files = dataTransfer.files;

                        // Hiển thị preview
                        showFilePreview(file);

                        // Bỏ required cho message input
                        chatInput.removeAttribute('required');

                        showChatSuccess('Đã paste ảnh! Bạn có thể gửi ngay hoặc thêm tin nhắn.');
                    }
                    break;
                }
            }
        });

        // Drag and drop ảnh trực tiếp vào input text
        chatInput.addEventListener('dragover', function (e) {
            e.preventDefault();
            this.style.borderColor = '#007bff';
            this.style.backgroundColor = '#f8f9fa';
        });

        chatInput.addEventListener('dragleave', function (e) {
            e.preventDefault();
            this.style.borderColor = '';
            this.style.backgroundColor = '';
        });

        chatInput.addEventListener('drop', function (e) {
            e.preventDefault();
            this.style.borderColor = '';
            this.style.backgroundColor = '';

            const files = e.dataTransfer.files;
            if (files.length > 0) {
                const file = files[0];
                if (file.type.startsWith('image/')) {
                    // Tạo FileList object để gán vào input
                    const dataTransfer = new DataTransfer();
                    dataTransfer.items.add(file);
                    if (attachmentInput) attachmentInput.files = dataTransfer.files;

                    // Hiển thị preview
                    showFilePreview(file);

                    // Bỏ required cho message input
                    chatInput.removeAttribute('required');

                    showChatSuccess('Đã kéo thả ảnh! Bạn có thể gửi ngay hoặc thêm tin nhắn.');
                }
            }
        });
    }

    // File input event listeners
    if (attachmentInput) {
        attachmentInput.addEventListener('change', function (e) {
            const file = e.target.files[0];
            if (file) {
                showFilePreview(file);
                // Bỏ required cho message input nếu có file
                if (chatInput) chatInput.removeAttribute('required');
            }
        });
    }

    // Remove file button
    if (removeFile) {
        removeFile.addEventListener('click', function () {
            clearFilePreview();
            // Khôi phục required cho message input
            if (chatInput) chatInput.setAttribute('required', 'required');
        });
    }

    // Modal event listeners
    if (modalClose) {
        modalClose.addEventListener('click', function () {
            if (imageModal) imageModal.style.display = 'none';
        });
    }

    // Đóng modal khi click bên ngoài
    if (imageModal) {
        imageModal.addEventListener('click', function (e) {
            if (e.target === imageModal) {
                imageModal.style.display = 'none';
            }
        });
    }

    // Đóng modal bằng ESC key
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape' && imageModal && imageModal.style.display === 'block') {
            imageModal.style.display = 'none';
        }
    });

    // Xử lý visibility change - realtime vẫn chạy ngay cả khi tab không active
    document.addEventListener('visibilitychange', function () {
        if (document.hidden) {
            console.log('Tab hidden, but realtime chat continues running');
            // KHÔNG dừng realtime chat khi tab không active
            // Realtime vẫn chạy để nhận tin nhắn mới
        } else {
            console.log('Tab visible again');
            // Tab visible trở lại, không cần làm gì đặc biệt
            // Realtime đã chạy rồi
        }
    });

    // Hàm hiển thị file preview
    function showFilePreview(file) {
        if (fileName) fileName.textContent = file.name;

        // Thay đổi icon tùy theo loại file
        if (filePreview) {
            const iconElement = filePreview.querySelector('.file-preview-icon i');
            if (iconElement) {
                if (file.type.startsWith('image/')) {
                    iconElement.className = 'fas fa-image';
                } else if (file.type === 'application/pdf') {
                    iconElement.className = 'fas fa-file-pdf';
                } else if (file.type === 'application/zip') {
                    iconElement.className = 'fas fa-file-archive';
                } else if (file.type === 'text/plain') {
                    iconElement.className = 'fas fa-file-alt';
                } else {
                    iconElement.className = 'fas fa-file';
                }
            }
            filePreview.style.display = 'flex';
        }
    }

    // Hàm xóa file preview
    function clearFilePreview() {
        if (attachmentInput) attachmentInput.value = '';
        if (filePreview) filePreview.style.display = 'none';
        if (fileName) fileName.textContent = '';
    }

    // Hàm mở modal ảnh
    function openImageModal(imageSrc) {
        if (modalImage && imageModal) {
            modalImage.src = imageSrc;
            imageModal.style.display = 'block';
            console.log('Opening image modal:', imageSrc);
        } else {
            console.error('Modal elements not found:', { modalImage, imageModal });
        }
    }

    // Hàm cập nhật badge số tin nhắn chưa đọc
    function updateUnreadBadge(count) {
        const badge = document.getElementById('chatNotificationBadge');
        if (count > 0) {
            badge.textContent = count > 99 ? '99+' : count;
            badge.style.display = 'block';
        } else {
            badge.style.display = 'none';
        }
        unreadCount = count;
    }

    // Hàm lấy số tin nhắn chưa đọc
    function fetchUnreadCount() {
        fetch('/support/unread-count', {
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    updateUnreadBadge(data.unread_count);
                }
            })
            .catch(error => {
                console.error('Error fetching unread count:', error);
            });
    }

    // Khởi tạo: Lấy số tin nhắn chưa đọc khi load trang
    fetchUnreadCount();

    // Cập nhật số tin nhắn chưa đọc định kỳ (mỗi 30 giây)
    setInterval(fetchUnreadCount, 30000);

    console.log('Chat widget initialized successfully');
});
