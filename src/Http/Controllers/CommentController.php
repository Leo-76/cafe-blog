<?php

namespace VotrePseudo\CafeBlog\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use VotrePseudo\CafeBlog\Models\Comment;
use VotrePseudo\CafeBlog\Models\Post;

class CommentController extends Controller
{
    public function store(Request $request, Post $post): RedirectResponse
    {
        $rules = ['body' => ['required', 'string', 'min:5', 'max:2000']];

        if (! auth()->check()) {
            $rules['guest_name']  = ['required', 'string', 'max:100'];
            $rules['guest_email'] = ['required', 'email', 'max:200'];
        }

        $data = $request->validate($rules);

        $autoApprove = auth()->check() || ! config('cafe-blog.moderate_comments', true);

        $post->comments()->create([
            'body'        => $data['body'],
            'user_id'     => auth()->id(),
            'guest_name'  => $data['guest_name'] ?? null,
            'guest_email' => $data['guest_email'] ?? null,
            'approved'    => $autoApprove,
        ]);

        $message = $autoApprove
            ? __('cafe-blog::messages.comment_published')
            : __('cafe-blog::messages.comment_pending');

        return back()->with('cafe_blog_success', $message);
    }

    public function approve(Comment $comment): RedirectResponse
    {
        $this->authorize('moderate', $comment);
        $comment->update(['approved' => true]);

        return back()->with('cafe_blog_success', __('cafe-blog::messages.comment_approved'));
    }

    public function destroy(Comment $comment): RedirectResponse
    {
        $this->authorize('delete', $comment);
        $comment->delete();

        return back()->with('cafe_blog_success', __('cafe-blog::messages.comment_deleted'));
    }
}
