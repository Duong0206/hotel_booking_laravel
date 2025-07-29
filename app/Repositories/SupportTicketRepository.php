<?php

namespace App\Repositories;

use App\Interfaces\Repositories\SupportTicketRepositoryInterface;
use App\Models\SupportTicket;

class SupportTicketRepository implements SupportTicketRepositoryInterface
{
    public function getByUser($userId)
    {
        return SupportTicket::where('user_id', $userId)->orderByDesc('created_at')->get();
    }

    public function create(array $data)
    {
        return SupportTicket::create($data);
    }

    public function findWithMessages($ticketId)
    {
        return SupportTicket::with(['messages' => function($q) {
            $q->orderBy('created_at');
        }])->findOrFail($ticketId);
    }

    public function getAll()
    {
        return SupportTicket::orderByDesc('created_at')->get();
    }
}
