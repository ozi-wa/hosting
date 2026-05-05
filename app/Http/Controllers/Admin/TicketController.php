<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use App\Services\SupportService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TicketController extends Controller
{
    public function index(): View
    {
        return view('admin.tickets.index', ['tickets' => Ticket::with('user')->latest('last_reply_at')->paginate(20)]);
    }

    public function show(Ticket $ticket): View
    {
        return view('admin.tickets.show', ['ticket' => $ticket->load(['user', 'messages.user', 'service.product'])]);
    }

    public function reply(Request $request, Ticket $ticket, SupportService $support): RedirectResponse
    {
        $data = $request->validate(['message' => ['required', 'string', 'min:10']]);
        $support->reply($ticket, $request->user(), $data['message']);

        return back()->with('status', 'Yanıt gönderildi.');
    }

    public function close(Ticket $ticket): RedirectResponse
    {
        $ticket->update(['status' => 'closed', 'closed_at' => now()]);

        return back()->with('status', 'Destek talebi kapatıldı.');
    }
}
