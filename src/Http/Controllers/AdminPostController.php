<?php

namespace VotrePseudo\CafeBlog\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use VotrePseudo\CafeBlog\Http\Requests\StorePostRequest;
use VotrePseudo\CafeBlog\Http\Requests\UpdatePostRequest;
use VotrePseudo\CafeBlog\Models\Category;
use VotrePseudo\CafeBlog\Models\Post;
use VotrePseudo\CafeBlog\Models\Tag;

class AdminPostController extends Controller
{
    public function __construct()
    {
        $this->middleware(config('cafe-blog.admin_middleware'));
    }

    public function index(): View
    {
        $posts = Post::with(['author', 'category'])->latest()->paginate(15);

        return view('cafe-blog::admin.posts.index', compact('posts'));
    }

    public function create(): View
    {
        $categories = Category::orderBy('name')->get();
        $tags       = Tag::orderBy('name')->get();

        return view('cafe-blog::admin.posts.create', compact('categories', 'tags'));
    }

    public function store(StorePostRequest $request): RedirectResponse
    {
        $data            = $request->validated();
        $data['user_id'] = auth()->id();

        if ($request->hasFile('cover_image')) {
            $data['cover_image'] = $request->file('cover_image')
                ->store('cafe-blog/covers', config('cafe-blog.storage_disk'));
        }

        $post = Post::create($data);
        $post->tags()->sync($request->input('tags', []));

        return redirect()->route('cafe-blog.admin.posts.edit', $post)
            ->with('cafe_blog_success', __('cafe-blog::messages.post_created'));
    }

    public function edit(Post $post): View
    {
        $categories = Category::orderBy('name')->get();
        $tags       = Tag::orderBy('name')->get();

        return view('cafe-blog::admin.posts.edit', compact('post', 'categories', 'tags'));
    }

    public function update(UpdatePostRequest $request, Post $post): RedirectResponse
    {
        $data = $request->validated();

        if ($request->hasFile('cover_image')) {
            if ($post->cover_image) {
                Storage::disk(config('cafe-blog.storage_disk'))->delete($post->cover_image);
            }
            $data['cover_image'] = $request->file('cover_image')
                ->store('cafe-blog/covers', config('cafe-blog.storage_disk'));
        }

        $post->update($data);
        $post->tags()->sync($request->input('tags', []));

        return redirect()->route('cafe-blog.admin.posts.edit', $post)
            ->with('cafe_blog_success', __('cafe-blog::messages.post_updated'));
    }

    public function destroy(Post $post): RedirectResponse
    {
        if ($post->cover_image) {
            Storage::disk(config('cafe-blog.storage_disk'))->delete($post->cover_image);
        }

        $post->delete();

        return redirect()->route('cafe-blog.admin.posts.index')
            ->with('cafe_blog_success', __('cafe-blog::messages.post_deleted'));
    }
}
