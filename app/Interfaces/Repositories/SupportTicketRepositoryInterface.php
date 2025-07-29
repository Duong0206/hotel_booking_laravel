<?php

namespace App\Interfaces\Repositories;

interface SupportTicketRepositoryInterface
{
    public function getByUser($userId);
    public function create(array $data);
    public function findWithMessages($ticketId);
    public function getAll();
}
