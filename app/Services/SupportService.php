<?php

namespace App\Services;

use App\Models\Ticket;
use App\Models\User;
use App\Services\Whmcs\WhmcsGateway;
use Illuminate\Support\Facades\DB;

class SupportService
{
    public function __construct(private readonly WhmcsGateway $whmcs) {}

    public function openTicket(User $user, array $data): Ticket
    {
        return DB::transaction(function () use ($user, $data): Ticket {
            $whmcsTicket = $this->whmcs->enabled() ? $this->whmcs->openTicket($user, $data) : [];

            $ticket = Ticket::create([
                'number' => 'TCK-'.now()->format('Ymd').'-'.str_pad((string) random_int(1, 999999), 6, '0', STR_PAD_LEFT),
                'whmcs_ticket_id' => $whmcsTicket['id'] ?? $whmcsTicket['ticketid'] ?? null,
                'whmcs_tid' => $whmcsTicket['tid'] ?? null,
                'user_id' => $user->id,
                'service_id' => $data['service_id'] ?? null,
                'subject' => $data['subject'],
                'department' => $data['department'] ?? 'support',
                'priority' => $data['priority'] ?? 'normal',
                'status' => 'open',
                'last_reply_at' => now(),
            ]);

            $ticket->messages()->create([
                'user_id' => $user->id,
                'message' => $data['message'],
                'is_staff_reply' => $user->isAdmin(),
            ]);

            return $ticket->load('messages.user');
        });
    }

    public function reply(Ticket $ticket, User $user, string $message): Ticket
    {
        $whmcsReply = $this->whmcs->enabled() && $ticket->whmcs_ticket_id
            ? $this->whmcs->replyToTicket($ticket, $user, $message)
            : [];

        $ticket->messages()->create([
            'user_id' => $user->id,
            'message' => $message,
            'attachments' => $whmcsReply ? ['whmcs' => $whmcsReply] : null,
            'is_staff_reply' => $user->isAdmin(),
        ]);

        $ticket->update([
            'status' => $user->isAdmin() ? 'answered' : 'open',
            'last_reply_at' => now(),
        ]);

        return $ticket->refresh();
    }
}
