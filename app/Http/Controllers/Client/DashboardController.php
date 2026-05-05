<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Services\Whmcs\WhmcsProjectionService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(Request $request, WhmcsProjectionService $whmcs): View
    {
        $user = $request->user();
        $whmcs->syncClient($user);

        return view('client.dashboard', [
            'services' => $user->services()->with('product')->latest()->limit(5)->get(),
            'orders' => $user->orders()->with('product')->latest()->limit(5)->get(),
            'invoices' => $user->invoices()->latest()->limit(5)->get(),
            'tickets' => $user->tickets()->latest('last_reply_at')->limit(5)->get(),
        ]);
    }

    public function profile(Request $request): View
    {
        return view('client.profile', ['user' => $request->user()]);
    }

    public function updateProfile(Request $request): RedirectResponse
    {
        $request->user()->update($request->validate([
            'name' => ['required', 'string', 'max:120'],
            'company_name' => ['nullable', 'string', 'max:180'],
            'phone' => ['nullable', 'string', 'max:40'],
            'tax_number' => ['nullable', 'string', 'max:80'],
            'billing_address' => ['nullable', 'string', 'max:1000'],
        ]));

        return back()->with('status', 'Profil güncellendi.');
    }
}
