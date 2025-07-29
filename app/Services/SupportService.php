<?php

namespace App\Services;

use App\Interfaces\Services\SupportServiceInterface;
use App\Interfaces\Repositories\SupportTicketRepositoryInterface;
use App\Models\SupportMessage;

class SupportService implements SupportServiceInterface
{
    protected $ticketRepo;

    public function __construct(SupportTicketRepositoryInterface $ticketRepo)
    {
        $this->ticketRepo = $ticketRepo;
    }

    public function getUserTickets($userId)
    {
        return $this->ticketRepo->getByUser($userId);
    }

    public function createTicket($userId, $subject)
    {
        return $this->ticketRepo->create([
            'user_id' => $userId,
            'subject' => $subject,
            'status' => 'open',
        ]);
    }

    public function getTicketWithMessages($ticketId)
    {
        return $this->ticketRepo->findWithMessages($ticketId);
    }

    public function sendMessage($ticketId, $senderId, $senderType, $message)
    {
        return SupportMessage::create([
            'ticket_id' => $ticketId,
            'sender_id' => $senderId,
            'sender_type' => $senderType,
            'message' => $message,
        ]);
    }

    public function getAllTickets()
    {
        return $this->ticketRepo->getAll();
    }
}
