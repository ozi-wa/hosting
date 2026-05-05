<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Product;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        return view('admin.dashboard', [
            'revenue' => Payment::where('status', 'paid')->sum('amount'),
            'usersCount' => User::count(),
            'ordersCount' => Order::count(),
            'openTickets' => Ticket::whereIn('status', ['open', 'answered'])->count(),
            'recentOrders' => Order::with(['user', 'product'])->latest()->limit(8)->get(),
            'unpaidInvoices' => Invoice::with('user')->where('status', 'unpaid')->latest()->limit(8)->get(),
            'productsCount' => Product::count(),
        ]);
    }
}
