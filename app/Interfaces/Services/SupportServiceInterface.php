<?php

namespace App\Interfaces\Services;

interface SupportServiceInterface
{
    /**
     * Lấy danh sách ticket của user
     */
    public function getUserTickets($userId);

    /**
     * Tạo ticket mới
     */
    public function createTicket($userId, $subject);

    /**
     * Lấy chi tiết ticket và lịch sử chat
     */
    public function getTicketWithMessages($ticketId);

    /**
     * Gửi tin nhắn mới vào ticket
     */
    public function sendMessage($ticketId, $senderId, $senderType, $message);

    /**
     * Lấy danh sách tất cả ticket (cho admin)
     */
    public function getAllTickets();
}
