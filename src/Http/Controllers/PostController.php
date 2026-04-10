<?php

namespace VotrePseudo\CafeBlog\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;
use VotrePseudo\CafeBlog\Models\Category;
use VotrePseudo\CafeBlog\Models\Post;
use VotrePseudo\CafeBlog\Models\Tag;

class PostController extends Controller
{
    public function index(Request $request): View
    {
        $perPage = config('cafe-blog.posts_per_page', 9);

        $query = Post::with(['author', 'category', 'tags'])
            ->published()
            ->latest('published_at');

        if ($search = $request->input('search')) {
            $query->search($search);
        }

        if ($cat = $request->input('category')) {
            $query->whereHas('category', fn ($q) => $q->where('slug', $cat));
        }

        if ($tag = $request->input('tag')) {
            $query->whereHas('tags', fn ($q) => $q->where('slug', $tag));
        }

        $posts      = $query->paginate($perPage)->withQueryString();
        $categories = Category::withCount(['posts' => fn ($q) => $q->published()])->get();
        $tags       = Tag::withCount('posts')->having('posts_count', '>', 0)->get();
        $featured   = Post::with(['author', 'category'])->published()->latest('published_at')->first();

        return view('cafe-blog::posts.index', compact('posts', 'categories', 'tags', 'featured', 'search'));
    }

    public function show(string $slug): View
    {
        $post = Post::with(['author', 'category', 'tags', 'approvedComments.author'])
            ->published()
            ->where('slug', $slug)
            ->firstOrFail();

        $related = Post::with(['author', 'category'])
            ->published()
            ->where('category_id', $post->category_id)
            ->where('id', '!=', $post->id)
            ->latest('published_at')
            ->limit(3)
            ->get();

        return view('cafe-blog::posts.show', compact('post', 'related'));
    }
}
