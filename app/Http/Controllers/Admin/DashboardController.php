<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BlogPost;
use App\Models\Product;
use App\Models\User;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        return view('admin.dashboard', [
            'usersCount' => User::count(),
            'productsCount' => Product::where('is_active', true)->count(),
            'blogPostsCount' => BlogPost::count(),
        ]);
    }
}
