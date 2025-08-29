@extends('admin.layouts.admin-master')

@section('title', 'Chi tiết hỗ trợ')

@section('styles')
<style>
/* Admin Chat Styles */
:root {
    --primary-color: #ff6b35;
    --primary-hover: #e55a2b;
    --secondary-color: #fff8f0;
    --success-color: #28a745;
    --danger-color: #dc3545;
    --warning-color: #ffc107;
    --info-color: #ff6b35;
    --text-dark: #2c2416;
    --text-light: #6c5a3d;
    --border-color: #ffd4b8;
    --hover-color: #fff2e6;
    --bg-light: #fffaf5;
}

/* Đảm bảo modal ảnh hiển thị trên tất cả các element */
#imageModal {
    z-index: 999999 !important;
}

#imageModal * {
    z-index: inherit;
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
    background: linear-gradient(135deg, #ffffff 0%, #fff8f0 100%);
    font-family: 'Roboto', sans-serif;
    border-radius: 16px;
    overflow: hidden;
    box-shadow: 0 8px 32px rgba(255, 107, 53, 0.15);
    position: relative;
    margin: 20px;
    width: calc(100% - 40px);
    border: 1px solid #ffd4b8;
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
    background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-hover) 100%);
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-weight: bold;
    font-size: 14px;
    box-shadow: 0 2px 8px rgba(255, 107, 53, 0.3);
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
    padding: 12px 20px;
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
    background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-hover) 100%);
    color: white;
    box-shadow: 0 2px 8px rgba(255, 107, 53, 0.3);
}

.conversations-list {
    flex: 1;
    overflow-y: auto;
    height: 0;
}

.conversation-item {
    padding: 12px 20px;
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
    background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-hover) 100%);
    color: white;
    box-shadow: 0 2px 8px rgba(255, 107, 53, 0.3);
}

.conversation-item.active .conversation-meta {
    color: rgba(255, 255, 255, 0.8);
}

.user-avatar {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-hover) 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-weight: bold;
    font-size: 16px;
    flex-shrink: 0;
    box-shadow: 0 2px 8px rgba(255, 107, 53, 0.3);
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
    width: 90%;
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
    background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-hover) 100%);
    color: white;
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
    background: rgba(255, 255, 255, 0.2);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-weight: bold;
    font-size: 16px;
    border: 2px solid rgba(255, 255, 255, 0.3);
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

.profile-btn {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 6px 10px;
    border-radius: 16px;
    border: 1px solid var(--border-color);
    background: #fff;
    color: var(--text-dark);
    text-decoration: none;
    font-size: 12px;
}

.profile-btn:hover {
    background: var(--hover-color);
}

.chat-messages {
    flex: 1;
    padding: 20px;
    overflow-y: auto;
    background: linear-gradient(135deg, #ffffff 0%, #fff8f0 50%, #fff2e6 100%);
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
    background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-hover) 100%);
    color: white;
    border-bottom-right-radius: 8px;
    box-shadow: 0 4px 12px rgba(255, 107, 53, 0.3);
}

.message.received .message-bubble {
    background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);
    color: var(--text-dark);
    border-bottom-left-radius: 8px;
    border: 1px solid #e9ecef;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
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
    background: linear-gradient(135deg, #ffffff 0%, #fff8f0 100%);
    flex-shrink: 0;
}

.chat-input-wrapper {
    display: flex;
    align-items: center;
    gap: 8px;
    background: linear-gradient(135deg, #ffffff 0%, #fff8f0 100%);
    border-radius: 20px;
    padding: 6px 12px;
    border: 2px solid var(--border-color);
    transition: all 0.3s ease;
}

.chat-input-wrapper:focus-within {
    border-color: var(--primary-color);
    box-shadow: 0 0 0 3px rgba(255, 107, 53, 0.1);
    transform: translateY(-1px);
}

.chat-input {
    flex: 1;
    border: none;
    background: transparent;
    outline: none;
    font-size: 14px;
    resize: none;
    max-height: 60px;
    min-height: 16px;
    padding: 4px 0;
}

.chat-input::placeholder {
    color: var(--text-light);
}

.chat-attachments {
    display: flex;
    gap: 8px;
}

.attachment-btn {
    width: 26px;
    height: 26px;
    border: none;
    border-radius: 50%;
    background: transparent;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: background 0.2s;
    color: var(--text-light);
    font-size: 13px;
}

.attachment-btn:hover {
    background: rgba(0, 0, 0, 0.1);
}

.send-btn {
    width: 32px;
    height: 32px;
    border: none;
    border-radius: 50%;
    background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-hover) 100%);
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all 0.3s ease;
    font-size: 14px;
    box-shadow: 0 4px 12px rgba(255, 107, 53, 0.3);
}

.send-btn:hover {
    background: linear-gradient(135deg, var(--primary-hover) 0%, #d44a1f 100%);
    transform: scale(1.1) translateY(-2px);
    box-shadow: 0 6px 20px rgba(255, 107, 53, 0.4);
}

.send-btn:disabled {
    background: var(--text-light);
    cursor: not-allowed;
    transform: none;
}

/* File attachment preview */
.file-preview {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 8px 12px;
    background: #f9f6f1;
    border: 1px solid #d4c4a8;
    border-radius: 8px;
    margin: 8px 0;
    max-width: 100%;
}

.file-preview-icon {
    width: 20px;
    height: 20px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--primary-color);
}

.file-preview-name {
    flex: 1;
    font-size: 12px;
    color: var(--primary-color);
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

.file-preview-remove {
    width: 20px;
    height: 20px;
    border: none;
    background: #fff2f2;
    color: var(--danger-color);
    border-radius: 50%;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 12px;
    transition: all 0.2s;
}

.file-preview-remove:hover {
    background: #ffe6e6;
    transform: scale(1.1);
}

/* Customer Info */
.customer-info {
    width: 40%;
    border-left: 1px solid var(--border-color);
    background: linear-gradient(135deg, #ffffff 0%, #fff8f0 100%);
    display: flex;
    flex-direction: column;
    height: 100vh;
    min-height: 100vh;
    overflow-y: auto; /* Cho phép lướt lên/xuống */
    min-height: 0; /* Đảm bảo flex container cho phép scroll nội bộ */
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
    background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-hover) 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-weight: bold;
    font-size: 24px;
    margin: 0 auto 16px;
    border: 4px solid rgba(255, 107, 53, 0.2);
    box-shadow: 0 8px 24px rgba(255, 107, 53, 0.3);
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
    min-height: 400px; /* Tăng chiều cao tối thiểu để hiển thị nhiều nội dung hơn */
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

/* Image gallery (right panel) */
.image-gallery {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(120px, 1fr));
    gap: 12px;
    max-height: 600px; /* Tăng chiều cao để hiển thị nhiều ảnh hơn */
    overflow-y: auto;
    padding: 8px;
}

.image-item {
    display: flex;
    flex-direction: column;
    flex-shrink: 0; /* Không cho phép co lại */
    position: relative;
}

.image-bubble {
    max-width: 100%;
    background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);
    border-radius: 16px;
    padding: 0;
    cursor: pointer; /* Thêm con trỏ pointer */
    transition: all 0.3s ease;
    border: 2px solid transparent;
    overflow: hidden;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
}

.image-bubble:hover {
    transform: scale(1.05);
    border-color: var(--primary-color);
    box-shadow: 0 8px 24px rgba(255, 107, 53, 0.4);
}

.image-bubble img {
    width: 100%;
    height: 120px; /* Chiều cao cố định cho grid layout */
    object-fit: cover; /* Cắt ảnh để vừa khung */
    display: block;
    border-radius: 8px;
    transition: all 0.3s ease;
}

.image-bubble:hover img {
    transform: scale(1.1);
}

.image-meta {
    font-size: 10px;
    color: var(--text-light);
    margin-top: 4px;
    text-align: center;
    line-height: 1.2;
}

/* Modal popup cho ảnh */
.image-modal {
    display: none;
    position: fixed;
    z-index: 99999 !important; /* Tăng z-index để đảm bảo hiển thị trên cùng */
    left: 0;
    top: 0;
    width: 100vw; /* Sử dụng viewport width */
    height: 100vh; /* Sử dụng viewport height */
    background-color: rgba(0, 0, 0, 0.9) !important; /* Tăng độ đậm */
    backdrop-filter: blur(5px);
    -webkit-backdrop-filter: blur(5px); /* Hỗ trợ Safari */
    /* Đảm bảo hiển thị trên tất cả các element */
    pointer-events: auto !important;
    visibility: visible !important;
    opacity: 1 !important;
}

/* Khi modal hiển thị */
.image-modal.show,
.image-modal[style*="display: block"] {
    display: flex !important;
    align-items: center;
    justify-content: center;
}

/* Đảm bảo modal hiển thị trên tất cả các element khác */
.image-modal,
.image-modal * {
    box-sizing: border-box;
}

/* Đảm bảo modal hiển thị đúng trên mobile */
@media (max-width: 768px) {
    .image-modal {
        padding: 20px;
    }

    .image-modal-content {
        max-width: 95vw;
        max-height: 95vh;
    }

    .image-modal-close {
        top: 10px;
        right: 20px;
        font-size: 30px;
    }
}

.image-modal-content {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    max-width: 90vw; /* Sử dụng viewport width */
    max-height: 90vh; /* Sử dụng viewport height */
    text-align: center;
    z-index: 100000 !important; /* Đảm bảo content hiển thị trên cùng */
    /* Đảm bảo hiển thị đúng */
    pointer-events: auto !important;
    visibility: visible !important;
    opacity: 1 !important;
}

.image-modal img {
    max-width: 100%;
    max-height: 100%;
    object-fit: contain;
    border-radius: 8px;
    box-shadow: 0 8px 32px rgba(0, 0, 0, 0.5);
    display: block; /* Đảm bảo ảnh hiển thị */
    /* Đảm bảo ảnh hiển thị đúng */
    pointer-events: auto !important;
    visibility: visible !important;
    opacity: 1 !important;
    /* Đảm bảo ảnh không bị cắt */
    width: auto;
    height: auto;
}

.image-modal-close {
    position: fixed; /* Thay đổi từ absolute sang fixed */
    top: 20px;
    right: 30px;
    color: white !important;
    font-size: 40px;
    font-weight: bold;
    cursor: pointer;
    z-index: 100001 !important; /* Cao nhất */
    text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.8); /* Thêm shadow để dễ nhìn */
    transition: all 0.2s ease;
    /* Đảm bảo nút hiển thị đúng */
    pointer-events: auto !important;
    visibility: visible !important;
    opacity: 1 !important;
    /* Đảm bảo nút có thể click được */
    user-select: none;
    -webkit-user-select: none;
    -moz-user-select: none;
    -ms-user-select: none;
}

.image-modal-close:hover {
    color: #ff6b6b !important;
    transform: scale(1.1);
}

/* Điều chỉnh kích thước ảnh trong chat */
.message-bubble img {
    max-width: 200px; /* Giảm kích thước ảnh trong chat */
    max-height: 150px;
    object-fit: cover;
    border-radius: 8px;
    cursor: pointer;
    transition: all 0.2s ease;
    border: 2px solid transparent;
}

.message-bubble img:hover {
    transform: scale(1.05);
    border-color: var(--primary-color);
    box-shadow: 0 4px 12px rgba(141, 112, 59, 0.3);
}

/* Removed internal notes styles */

/* Responsive */
@media (max-width: 1200px) {
    .chat-sidebar {
        width: 25%;
    }
    .chat-window {
        width: 50%;
    }
    .customer-info {
        width: 25%;
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

/* Emoji picker styles */
.emoji-btn {
    width: 32px;
    height: 32px;
    border: none;
    border-radius: 50%;
    background: transparent;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all 0.2s;
    color: var(--text-light);
    font-size: 16px;
}

.emoji-btn:hover {
    background: rgba(0, 0, 0, 0.1);
    color: var(--primary-color);
    transform: scale(1.1);
}

.emoji-picker {
    position: absolute;
    bottom: 100%;
    left: 0;
    width: 320px;
    background: white;
    border: 1px solid var(--border-color);
    border-radius: 12px;
    box-shadow: 0 8px 32px rgba(0, 0, 0, 0.15);
    z-index: 1000;
    margin-bottom: 10px;
    overflow: hidden;
}

.emoji-categories {
    display: flex;
    border-bottom: 1px solid var(--border-color);
    background: var(--secondary-color);
}

.emoji-category {
    flex: 1;
    padding: 12px;
    border: none;
    background: transparent;
    cursor: pointer;
    transition: all 0.2s;
    color: var(--text-light);
    font-size: 14px;
}

.emoji-category:hover {
    background: rgba(0, 0, 0, 0.05);
    color: var(--primary-color);
}

.emoji-category.active {
    background: var(--primary-color);
    color: white;
}

.emoji-grid {
    display: grid;
    grid-template-columns: repeat(8, 1fr);
    gap: 4px;
    padding: 16px;
    max-height: 200px;
    overflow-y: auto;
}

.emoji-item {
    width: 32px;
    height: 32px;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    border-radius: 6px;
    transition: all 0.2s;
    font-size: 20px;
    user-select: none;
}

.emoji-item:hover {
    background: var(--hover-color);
    transform: scale(1.2);
}

/* Custom scrollbar */
.chat-messages,
.conversations-list,
.chat-history,
.customer-info {
    scrollbar-width: auto; /* Firefox: dày hơn mặc định */
    scrollbar-color: rgba(0, 0, 0, 0.3) transparent;
}

.chat-messages::-webkit-scrollbar,
.conversations-list::-webkit-scrollbar,
.chat-history::-webkit-scrollbar,
.customer-info::-webkit-scrollbar {
    width: 14px; /* Tăng độ dày thanh cuộn để dễ kéo hơn */
}

.chat-messages::-webkit-scrollbar-track,
.conversations-list::-webkit-scrollbar-track,
.chat-history::-webkit-scrollbar-track,
.customer-info::-webkit-scrollbar-track {
    background: transparent;
}

.chat-messages::-webkit-scrollbar-thumb,
.conversations-list::-webkit-scrollbar-thumb,
.chat-history::-webkit-scrollbar-thumb,
.customer-info::-webkit-scrollbar-thumb {
    background: rgba(0, 0, 0, 0.35);
    border-radius: 6px;
}

.chat-messages::-webkit-scrollbar-thumb:hover,
.conversations-list::-webkit-scrollbar-thumb:hover,
.chat-history::-webkit-scrollbar-thumb:hover,
.customer-info::-webkit-scrollbar-thumb:hover {
    background: rgba(0, 0, 0, 0.5);
}
</style>
@endsection

@section('content')
@php
    $user = $conversation['user'] ?? null;
    $userId = $user->id ?? null;
    $totalBookings = 0;
    $hasAnyBooking = false;
    $activeBooking = null; // đang nhận phòng / đã xác nhận chưa trả
    $latestBooking = null;
    $latestRoomName = null;
    if ($userId) {
        $totalBookings = \App\Models\Booking::where('user_id', $userId)->count();
        $hasAnyBooking = $totalBookings > 0;
        $activeBooking = \App\Models\Booking::where('user_id', $userId)
            ->whereIn('status', ['confirmed', 'checked_in'])
            ->latest('check_in_date')
            ->with('room')
            ->first();
        $latestBooking = \App\Models\Booking::where('user_id', $userId)
            ->latest('created_at')
            ->with('room')
            ->first();
        $latestRoomName = $latestBooking && $latestBooking->room ? ($latestBooking->room->name ?? ('Phòng #' . $latestBooking->room->id)) : null;
    }
@endphp
<div class="admin-chat-container">
    <!-- Chat Window -->
    <div class="chat-window">
        <div class="chat-header">
            <div class="chat-user-info">
                <div class="chat-user-avatar">{{ substr($conversation['user']->name ?? 'U', 0, 1) }}</div>
                <div>
                    <h5>{{ $conversation['user']->name ?? 'Khách hàng' }}</h5>
                    <div class="chat-user-status">
                        <div class="online-indicator"></div>
                        <span>Online</span>
                    </div>
                </div>
            </div>
            <div class="chat-actions">

                @if(!empty($conversation['user']->id))
                <a href="{{ route('admin.users.show', $conversation['user']->id) }}" class="profile-btn" title="Xem hồ sơ khách hàng">
                    <i class="fas fa-user"></i>
                </a>
                @endif
                <a href="{{ route('admin.support.index') }}" class="chat-action-btn" title="Quay lại danh sách" style="text-decoration: none; color: inherit;">
                    <i class="fas fa-times"></i>
                </a>
            </div>
        </div>

        <div class="chat-messages" id="chatMessages">
            @foreach($messages as $msg)
                <div class="message {{ $msg->sender_type == 'admin' ? 'sent' : 'received' }}" data-message-id="{{ $msg->id }}">
                    @if(!empty(trim((string)$msg->message)))
                        <div class="message-bubble">{{ $msg->message }}</div>
                    @endif
                    @if(!empty($msg->attachment_path) && \Illuminate\Support\Str::startsWith((string)$msg->attachment_type, 'image'))
                        <div class="message-bubble" onclick="openImageModal('{{ asset('storage/'.$msg->attachment_path) }}')" style="cursor: pointer;">
                            <img src="{{ asset('storage/'.$msg->attachment_path) }}" alt="attachment" style="max-width:200px; max-height:150px; border-radius:8px;" />
                        </div>
                    @elseif(!empty($msg->attachment_path))
                        <div class="message-bubble">
                            <a href="{{ asset('storage/'.$msg->attachment_path) }}" target="_blank" rel="noopener">{{ $msg->attachment_name ?? 'Tệp đính kèm' }}</a>
                        </div>
                    @endif
                    <div class="message-time">{{ $msg->created_at->format('H:i') }}</div>
                </div>
            @endforeach
        </div>

        <div class="chat-input-container">
            <form id="chatForm" enctype="multipart/form-data">
                @csrf
                <div class="chat-input-wrapper">
                    <textarea id="chatInput" name="message" class="chat-input" placeholder="Nhập tin nhắn..."></textarea>
                    <div class="chat-attachments">
                        <label for="attachmentInput" class="attachment-btn" title="Đính kèm ảnh/tệp" style="cursor: pointer;">
                            <i class="fas fa-paperclip"></i>
                        </label>
                        <input type="file" id="attachmentInput" name="attachment" accept="image/*,application/pdf,application/zip,text/plain" style="display:none" />
                    </div>
                    <button type="submit" id="sendBtn" class="send-btn" title="Gửi tin nhắn">
                        <i class="fas fa-paper-plane"></i>
                    </button>
                </div>

                <!-- Emoji picker -->
                <div id="emojiPicker" class="emoji-picker" style="display: none;">
                    <div class="emoji-categories">
                        <button type="button" class="emoji-category active" data-category="smileys">
                            <i class="fas fa-smile"></i>
                        </button>
                        <button type="button" class="emoji-category" data-category="gestures">
                            <i class="fas fa-hand-paper"></i>
                        </button>
                        <button type="button" class="emoji-category" data-category="objects">
                            <i class="fas fa-star"></i>
                        </button>
                        <button type="button" class="emoji-category" data-category="symbols">
                            <i class="fas fa-heart"></i>
                        </button>
                    </div>
                    <div class="emoji-grid" id="emojiGrid">
                        <!-- Emojis will be populated by JavaScript -->
                    </div>
                </div>
                <!-- File preview area -->
                <div id="filePreview" class="file-preview" style="display: none;">
                    <div class="file-preview-icon">
                        <i class="fas fa-file"></i>
                    </div>
                    <div class="file-preview-name" id="fileName"></div>
                    <button type="button" class="file-preview-remove" id="removeFile" title="Xóa tệp">
                        <i class="fas fa-times"></i>
                    </button>
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
        </div>

        <div class="customer-details">
            <div class="detail-item">
                <span class="detail-label">Chủ đề:</span>
                <span class="detail-value">{{ $conversation['subject'] }}</span>
            </div>
            <div class="detail-item">
                <span class="detail-label">Tổng số đơn đặt:</span>
                <span class="detail-value">{{ $totalBookings }}</span>
            </div>
            <div class="detail-item">
                <span class="detail-label">Đã từng đặt phòng:</span>
                <span class="detail-value">{{ $hasAnyBooking ? 'Có' : 'Chưa' }}</span>
            </div>
            <div class="detail-item">
                <span class="detail-label">Đang ở/Đã xác nhận:</span>
                <span class="detail-value">{{ $activeBooking ? ($activeBooking->room ? ($activeBooking->room->name ?? ('Phòng #'.$activeBooking->room->id)) : 'Có') : 'Không' }}</span>
            </div>
            <div class="detail-item">
                <span class="detail-label">Phòng gần nhất:</span>
                <span class="detail-value">{{ $latestRoomName ?? 'N/A' }}</span>
            </div>
            @if(!empty($conversation['user']->id))
            <div class="detail-item">
                <span class="detail-label">Hồ sơ:</span>
                <span class="detail-value"><a href="{{ route('admin.users.show', $conversation['user']->id) }}" style="text-decoration: none;">Xem chi tiết</a></span>
            </div>
            @endif
        </div>

        <div class="chat-history">
            <div class="history-title">Ảnh đã gửi</div>
            <div class="image-gallery">
                @php
                    $images = collect($messages ?? [])->filter(function($m){
                        return !empty($m->attachment_path) && Str::startsWith($m->attachment_type, 'image');
                    });
                @endphp
                @foreach($images as $img)
                    <div class="image-item">
                        <div class="image-bubble" onclick="openImageModal('{{ asset('storage/'.$img->attachment_path) }}')">
                            <img src="{{ asset('storage/'.$img->attachment_path) }}" alt="attachment" />
                        </div>
                        <div class="image-meta">
                            {{ $img->created_at->format('d/m/Y H:i') }} • {{ $img->sender_type === 'admin' ? 'Admin' : 'Khách' }}
                        </div>
                    </div>
                @endforeach
                @if(($images ?? collect())->isEmpty())
                    <div class="image-meta">Chưa có ảnh nào.</div>
                @endif
            </div>
        </div>


    </div>
</div>

<!-- Modal popup cho ảnh -->
<div id="imageModal" class="image-modal">
    <span class="image-modal-close">&times;</span>
    <div class="image-modal-content">
        <img id="modalImage" src="" alt="Ảnh lớn" />
    </div>
</div>

<input type="hidden" id="conversationId" value="{{ $conversation['id'] }}">
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Tránh khởi tạo script 2 lần nếu view/script bị include lặp
    if (window.__adminSupportChatInitialized) {
        return;
    }
    window.__adminSupportChatInitialized = true;
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

    // Modal elements
    const imageModal = document.getElementById('imageModal');
    const modalImage = document.getElementById('modalImage');
    const modalClose = document.querySelector('.image-modal-close');

    // File preview elements
    const filePreview = document.getElementById('filePreview');
    const fileName = document.getElementById('fileName');
    const removeFile = document.getElementById('removeFile');
    const attachmentInput = document.getElementById('attachmentInput');

    // Emoji picker elements
    const emojiBtn = document.getElementById('emojiBtn');
    const emojiPicker = document.getElementById('emojiPicker');
    const emojiGrid = document.getElementById('emojiGrid');
    const emojiCategories = document.querySelectorAll('.emoji-category');

    // Realtime chat variables
    let isRealtimeEnabled = false;
    let lastMessageId = 0;
    let isSending = false;
    let didShowConnectInfo = false;

    // Polling config & control
    let pollIntervalMs = 1500; // nhanh hơn: 1.5s
    let isFetchingMessages = false;
    let fetchAbortController = null;

    // Lưu trữ tin nhắn gần đây để tránh trùng lặp
    let recentMessages = [];
    const MAX_RECENT_MESSAGES = 10;

    // Khởi tạo lastMessageId từ tin nhắn cuối cùng
    const messageNodes = document.querySelectorAll('.message[data-message-id]');
    if (messageNodes && messageNodes.length) {
        try {
            lastMessageId = Math.max(
                ...Array.from(messageNodes).map(n => parseInt(n.getAttribute('data-message-id')) || 0)
            );
        } catch (e) {
            const lastNode = messageNodes[messageNodes.length - 1];
            lastMessageId = parseInt(lastNode.getAttribute('data-message-id')) || 0;
        }
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

    // Cuộn xuống cuối khi khởi tạo giao diện
    function scrollToBottom() {
        if (chatMessages) {
            chatMessages.scrollTop = chatMessages.scrollHeight;
        }
    }

    // Bắt đầu realtime khi trang load (chỉ hiển thị thông báo 1 lần)
    startRealtimeChat();
    // Đảm bảo ở cuối ngay khi vào
    scrollToBottom();
    // Và sau một nhịp nhỏ để chắc chắn (khi font/icon render xong)
    setTimeout(scrollToBottom, 100);

    // Modal event listeners
    modalClose.addEventListener('click', function() {
        imageModal.style.display = 'none';
    });

    // Đóng modal khi click bên ngoài
    imageModal.addEventListener('click', function(e) {
        if (e.target === imageModal) {
            imageModal.style.display = 'none';
        }
    });

    // Đóng modal bằng ESC key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && imageModal.style.display === 'block') {
            imageModal.style.display = 'none';
        }
    });

    // File input event listeners
    attachmentInput.addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            showFilePreview(file);
            // Bỏ required cho message input nếu có file
            chatInput.removeAttribute('required');
        }
    });

    // Remove file button
    removeFile.addEventListener('click', function() {
        clearFilePreview();
        // Khôi phục required cho message input
        chatInput.setAttribute('required', 'required');
    });

    // if (!didShowConnectInfo) {
    //     showAdminChatInfo('Đã kết nối với cuộc trò chuyện!');
    //     didShowConnectInfo = true;
    // }

    // Auto-resize textarea
    chatInput.addEventListener('input', function() {
        this.style.height = 'auto';
        this.style.height = Math.min(this.scrollHeight, 100) + 'px';
    });

    // Send message (single path, để sendMessage tự quản lý isSending)
    chatForm.addEventListener('submit', function(e) {
        e.preventDefault();
        const message = chatInput.value.trim();
        if (isSending) return;
        const fileInput = document.getElementById('attachmentInput');
        const hasFile = fileInput && fileInput.files && fileInput.files[0];
        if (!message && !hasFile) {
            showAdminChatError('Vui lòng nhập tin nhắn hoặc chọn tệp đính kèm');
            return;
        }
        sendMessage(message);
    });

    // Send message on Enter (use submit to avoid duplicate)
    chatInput.addEventListener('keydown', function(e) {
        if (e.key === 'Enter' && !e.shiftKey) {
            e.preventDefault();
            if (!isSending) {
                if (typeof chatForm.requestSubmit === 'function') {
                    chatForm.requestSubmit();
                } else {
                    chatForm.dispatchEvent(new Event('submit', { cancelable: true }));
                }
            }
        }
    });

    // Paste image handler - cho phép copy & paste ảnh trực tiếp
    chatInput.addEventListener('paste', function(e) {
        const items = (e.clipboardData || e.originalEvent.clipboardData).items;

        for (let item of items) {
            if (item.type.indexOf('image') !== -1) {
                e.preventDefault();

                const file = item.getAsFile();
                if (file) {
                    // Tạo FileList object để gán vào input
                    const dataTransfer = new DataTransfer();
                    dataTransfer.items.add(file);
                    attachmentInput.files = dataTransfer.files;

                    // Hiển thị preview
                    showFilePreview(file);

                    // Bỏ required cho message input
                    chatInput.removeAttribute('required');

                    showAdminChatSuccess('Đã paste ảnh! Bạn có thể gửi ngay hoặc thêm tin nhắn.');
                }
                break;
            }
        }
    });

    function sendMessage(message) {
        if(isSending) return;

        isSending = true;
        sendBtn.disabled = true;
        sendBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';

        const formData = new FormData();
        formData.append('_token', '{{ csrf_token() }}');
        if (message) formData.append('message', message);
        const fileInput = document.getElementById('attachmentInput');
        if (fileInput && fileInput.files && fileInput.files[0]) {
            formData.append('attachment', fileInput.files[0]);
        }

        fetch(`/admin/support/conversation/${conversationId}/message`, {
            method: 'POST',
            headers: {
                'Accept': 'application/json'
            },
            body: formData
        })
        .then(res => res.json())
        .then(data => {
            if(data.success) {
                if (data.message_id) {
                    lastMessageId = Math.max(lastMessageId, data.message_id);
                }
                addMessageToUI(message, 'admin', data.message_id, null, data.attachment);
                chatInput.value = '';
                chatInput.style.height = 'auto';
                if (fileInput) fileInput.value = '';
                // Xóa file preview và khôi phục required
                clearFilePreview();
                chatInput.setAttribute('required', 'required');
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

    function addMessageToUI(message, senderType, messageId = null, createdAt = null, attachment = null) {
        const messageDiv = document.createElement('div');
        messageDiv.className = `message ${senderType === 'admin' ? 'sent' : 'received'}`;
        if(messageId) {
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
        if (attachment && attachment.url) {
            if (attachment.type && attachment.type.startsWith('image/')) {
                // Hiển thị ảnh
                const imageBubble = document.createElement('div');
                imageBubble.className = 'message-bubble';
                imageBubble.style.cursor = 'pointer';
                imageBubble.onclick = () => {
                    console.log('Image clicked in chat:', attachment.url);
                    openImageModal(attachment.url);
                };

                const img = document.createElement('img');
                img.src = attachment.url;
                img.alt = attachment.name || 'attachment';
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
        if(createdAt) {
            messageTime.textContent = new Date(createdAt).toLocaleTimeString('vi-VN', {hour: '2-digit', minute:'2-digit'});
        } else {
            messageTime.textContent = new Date().toLocaleTimeString('vi-VN', {hour: '2-digit', minute:'2-digit'});
        }

        messageDiv.appendChild(messageTime);
        chatMessages.appendChild(messageDiv);
        chatMessages.scrollTop = chatMessages.scrollHeight;

        // Lưu tin nhắn vào recentMessages để tránh trùng lặp (chỉ khi có text)
        if (message && message.trim()) {
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
    }

    function addAttachmentToUI(attachment, senderType, messageId = null, createdAt = null) {
        if(!attachment || !attachment.url) return;

        const messageDiv = document.createElement('div');
        messageDiv.className = `message ${senderType === 'admin' ? 'sent' : 'received'}`;
        if(messageId) {
            messageDiv.setAttribute('data-message-id', messageId);
        }

        const bubble = document.createElement('div');
        bubble.className = 'message-bubble';

        if (attachment.type && attachment.type.startsWith('image')) {
            const img = document.createElement('img');
            img.src = attachment.url;
            img.alt = attachment.name || 'attachment';
            img.style.maxWidth = '200px';
            img.style.maxHeight = '150px';
            img.style.borderRadius = '8px';
            img.style.cursor = 'pointer';

            // Thêm click event để mở modal
            img.addEventListener('click', function() {
                modalImage.src = attachment.url;
                imageModal.style.display = 'block';
            });

            bubble.appendChild(img);
        } else {
            const a = document.createElement('a');
            a.href = attachment.url;
            a.target = '_blank';
            a.textContent = attachment.name || 'Tải tệp đính kèm';
            bubble.appendChild(a);
        }

        const messageTime = document.createElement('div');
        messageTime.className = 'message-time';
        if(createdAt) {
            messageTime.textContent = new Date(createdAt).toLocaleTimeString('vi-VN', {hour: '2-digit', minute:'2-digit'});
        } else {
            messageTime.textContent = new Date().toLocaleTimeString('vi-VN', {hour: '2-digit', minute:'2-digit'});
        }

        messageDiv.appendChild(bubble);
        messageDiv.appendChild(messageTime);
        chatMessages.appendChild(messageDiv);
        chatMessages.scrollTop = chatMessages.scrollHeight;
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
        if(isFetchingMessages) return; // tránh chồng request

        isFetchingMessages = true;
        if (fetchAbortController) {
            try { fetchAbortController.abort(); } catch (_) {}
        }
        fetchAbortController = new AbortController();

        fetch(`/admin/support/conversation/${conversationId}/messages?last_id=${lastMessageId}`, {
            signal: fetchAbortController.signal
        })
        .then(res => {
            if (!res.ok) {
                throw new Error(`HTTP error! status: ${res.status}`);
            }
            return res.json();
        })
        .then(data => {
            if(data.success && data.messages && data.messages.length > 0) {
                console.log('Received new messages:', data.messages.length);
                data.messages.forEach(msg => {
                    const existingMessage = document.querySelector(`[data-message-id="${msg.id}"]`);
                    if(!existingMessage && msg.id > lastMessageId) {
                        const duplicateContent = checkDuplicateMessage(msg.message, msg.sender_type);
                        if(!duplicateContent) {
                            addMessageToUI(msg.message, msg.sender_type, msg.id, msg.created_at, msg.attachment);
                            lastMessageId = Math.max(lastMessageId, msg.id);

                            // Thông báo khi nhận tin nhắn mới từ user
                            if(msg.sender_type === 'user') {
                                // Phát âm thanh thông báo (to hơn)
                                try {
                                    const audioContext = new (window.AudioContext || window.webkitAudioContext)();
                                    const oscillator = audioContext.createOscillator();
                                    const gainNode = audioContext.createGain();

                                    oscillator.connect(gainNode);
                                    gainNode.connect(audioContext.destination);

                                    // Tạo âm sắc nổi bật hơn cho admin (square wave)
                                    oscillator.frequency.setValueAtTime(900, audioContext.currentTime);
                                    oscillator.type = 'square';

                                    // Tăng âm lượng và kéo dài hơn 1 chút
                                    gainNode.gain.setValueAtTime(0, audioContext.currentTime);
                                    gainNode.gain.linearRampToValueAtTime(0.6, audioContext.currentTime + 0.08);
                                    gainNode.gain.linearRampToValueAtTime(0, audioContext.currentTime + 0.6);

                                    oscillator.start(audioContext.currentTime);
                                    oscillator.stop(audioContext.currentTime + 0.6);
                                } catch (e) {
                                    console.log('Admin notification sound failed:', e);
                                }

                                showAdminChatInfo('Có tin nhắn mới từ khách hàng!');
                            }
                        }
                    }
                });
            }
        })
        .catch(error => {
            if (error && error.name === 'AbortError') {
                // bị hủy do vòng lặp mới, bỏ qua
                return;
            }
            console.error('Realtime error:', error);

            // Thử lại sau 5 giây nếu có lỗi
            if(isRealtimeEnabled) {
                setTimeout(checkNewMessages, 5000);
            }
        })
        .finally(() => {
            isFetchingMessages = false;
            if(isRealtimeEnabled) {
                setTimeout(checkNewMessages, pollIntervalMs);
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

    // Hàm mở modal ảnh từ view
    window.openImageModal = function(imageSrc) {
        if(modalImage && imageModal) {
            modalImage.src = imageSrc;
            imageModal.style.display = 'block';
            // Thêm class để đảm bảo CSS hoạt động đúng
            imageModal.classList.add('show');
            // Đảm bảo modal hiển thị trên cùng
            imageModal.style.zIndex = '99999';
            console.log('Opening image modal:', imageSrc);
        } else {
            console.error('Modal elements not found');
        }
    };

    // Đóng modal khi click vào nút close hoặc background
    if(modalClose) {
        modalClose.onclick = function() {
            closeImageModal();
        };
    }

    if(imageModal) {
        imageModal.onclick = function(e) {
            if (e.target === imageModal) {
                closeImageModal();
            }
        };
    }

    // Đóng modal bằng phím ESC
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && imageModal && imageModal.style.display === 'block') {
            closeImageModal();
        }
    });

    // Hàm đóng modal ảnh
    function closeImageModal() {
        if(imageModal) {
            imageModal.style.display = 'none';
            imageModal.classList.remove('show');
            console.log('Image modal closed');
        }
    }

    // Hàm hiển thị file preview
    function showFilePreview(file) {
        fileName.textContent = file.name;

        // Thay đổi icon tùy theo loại file
        const iconElement = filePreview.querySelector('.file-preview-icon i');
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

        filePreview.style.display = 'flex';
    }

    // Hàm xóa file preview
    function clearFilePreview() {
        attachmentInput.value = '';
        filePreview.style.display = 'none';
        fileName.textContent = '';
    }
});
</script>
@endsection
