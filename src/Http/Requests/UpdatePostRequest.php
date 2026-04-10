<?php

namespace VotrePseudo\CafeBlog\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdatePostRequest extends FormRequest
{
    public function authorize(): bool
    {
        $col = config('cafe-blog.admin_column', 'is_admin');
        return (bool) ($this->user()?->{$col} ?? false);
    }

    public function rules(): array
    {
        $postId = $this->route('post')?->id;

        return [
            'title'            => ['required', 'string', 'max:255'],
            'slug'             => ['nullable', 'string', 'max:255', Rule::unique('cafe_blog_posts', 'slug')->ignore($postId)],
            'excerpt'          => ['nullable', 'string', 'max:500'],
            'body'             => ['required', 'string'],
            'cover_image'      => ['nullable', 'image', 'max:2048'],
            'published_at'     => ['nullable', 'date'],
            'category_id'      => ['nullable', 'exists:cafe_blog_categories,id'],
            'tags'             => ['nullable', 'array'],
            'tags.*'           => ['exists:cafe_blog_tags,id'],
            'meta_title'       => ['nullable', 'string', 'max:255'],
            'meta_description' => ['nullable', 'string', 'max:500'],
        ];
    }
}
