<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTicketRequest;
use App\Models\Ticket;
use App\Services\SupportService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TicketController extends Controller
{
    public function index(Request $request): View
    {
        return view('client.tickets.index', ['tickets' => $request->user()->tickets()->latest('last_reply_at')->paginate(15)]);
    }

    public function create(Request $request): View
    {
        return view('client.tickets.create', ['services' => $request->user()->services()->with('product')->get()]);
    }

    public function store(StoreTicketRequest $request, SupportService $support): RedirectResponse
    {
        $ticket = $support->openTicket($request->user(), $request->validated());

        return redirect()->route('client.tickets.show', $ticket)->with('status', 'Destek talebi açıldı.');
    }

    public function show(Request $request, Ticket $ticket): View
    {
        abort_unless($ticket->user_id === $request->user()->id, 403);

        return view('client.tickets.show', ['ticket' => $ticket->load(['messages.user', 'service.product'])]);
    }

    public function reply(Request $request, Ticket $ticket, SupportService $support): RedirectResponse
    {
        abort_unless($ticket->user_id === $request->user()->id, 403);
        $data = $request->validate(['message' => ['required', 'string', 'min:10']]);
        $support->reply($ticket, $request->user(), $data['message']);

        return back()->with('status', 'Yanıt gönderildi.');
    }
}
