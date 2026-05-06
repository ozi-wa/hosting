<?php

namespace App\Http\Controllers;

use App\Models\BlogPost;
use App\Models\Category;
use App\Models\Product;
use Illuminate\View\View;

class PublicPageController extends Controller
{
    public function home(): View
    {
        return view('public.home', [
            'featuredProducts' => Product::with('category')->where('is_active', true)->where('is_featured', true)->orderBy('sort_order')->get(),
            'posts' => BlogPost::where('status', 'published')->latest('published_at')->limit(3)->get(),
        ]);
    }

    public function hosting(): View
    {
        return $this->categoryPage('web-hosting', 'Hosting Paketleri');
    }

    public function wordpress(): View
    {
        return $this->categoryPage('wordpress-hosting', 'WordPress Hosting');
    }

    public function corporate(): View
    {
        return $this->categoryPage('kurumsal-hosting', 'Kurumsal Hosting');
    }

    public function vps(): View
    {
        return $this->categoryPage('vps-vds', 'VPS / VDS Paketleri');
    }

    public function dedicated(): View
    {
        return $this->categoryPage('dedicated-server', 'Dedicated Sunucular');
    }

    public function radio(): View
    {
        return $this->categoryPage('radio-hosting', 'Radyo Yayın Hosting');
    }

    public function tv(): View
    {
        return $this->categoryPage('tv-hosting', 'TV Yayın Hosting');
    }

    public function about(): View
    {
        return view('public.about');
    }

    public function contact(): View
    {
        return view('public.contact');
    }

    public function faq(): View
    {
        return view('public.faq');
    }

    public function blog(): View
    {
        return view('public.blog', ['posts' => BlogPost::where('status', 'published')->latest('published_at')->paginate(9)]);
    }

    public function post(BlogPost $post): View
    {
        abort_unless($post->status === 'published', 404);

        return view('public.post', ['post' => $post]);
    }

    private function categoryPage(string $slug, string $title): View
    {
        $category = Category::where('slug', $slug)->first();

        return view('public.plans', [
            'title' => $title,
            'category' => $category,
            'products' => $category
                ? $category->products()->where('is_active', true)->orderBy('sort_order')->get()
                : collect(),
        ]);
    }
}
