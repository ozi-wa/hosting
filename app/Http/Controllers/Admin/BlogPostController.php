<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreBlogPostRequest;
use App\Models\BlogPost;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class BlogPostController extends Controller
{
    public function index(): View
    {
        return view('admin.blog-posts.index', ['posts' => BlogPost::latest()->paginate(20)]);
    }

    public function create(): View
    {
        return view('admin.blog-posts.form', ['post' => new BlogPost]);
    }

    public function store(StoreBlogPostRequest $request): RedirectResponse
    {
        BlogPost::create($this->payload($request->validated()) + ['user_id' => $request->user()->id]);

        return redirect()->route('admin.blog-posts.index')->with('status', 'Yazı oluşturuldu.');
    }

    public function edit(BlogPost $blogPost): View
    {
        return view('admin.blog-posts.form', ['post' => $blogPost]);
    }

    public function update(StoreBlogPostRequest $request, BlogPost $blogPost): RedirectResponse
    {
        $blogPost->update($this->payload($request->validated()));

        return redirect()->route('admin.blog-posts.index')->with('status', 'Yazı güncellendi.');
    }

    public function destroy(BlogPost $blogPost): RedirectResponse
    {
        $blogPost->delete();

        return back()->with('status', 'Yazı silindi.');
    }

    private function payload(array $data): array
    {
        $data['tags'] = array_values(array_filter(array_map('trim', explode(',', $data['tags'] ?? ''))));
        $data['published_at'] = $data['status'] === 'published' ? now() : null;

        return $data;
    }
}
