<?php

namespace VotrePseudo\CafeBlog\Policies;

use Illuminate\Contracts\Auth\Authenticatable;
use VotrePseudo\CafeBlog\Models\Comment;

class CommentPolicy
{
    public function moderate(Authenticatable $user, Comment $comment): bool
    {
        $col = config('cafe-blog.admin_column', 'is_admin');
        return (bool) ($user->{$col} ?? false);
    }

    public function delete(Authenticatable $user, Comment $comment): bool
    {
        $col = config('cafe-blog.admin_column', 'is_admin');
        return ($user->{$col} ?? false) || $user->getAuthIdentifier() === $comment->user_id;
    }
}
