@extends('cafe-blog::layouts.app')

@section('title', ($post->meta_title ?? $post->title) . ' — ' . config('cafe-blog.name'))
@section('meta_description', $post->meta_description ?? $post->excerpt)

@section('content')

@push('styles')
<style>
.cb-article { max-width:740px; margin:0 auto; }
.cb-breadcrumb { font-size:.8rem; color:#a8a29e; margin-bottom:1.5rem; }
.cb-breadcrumb a { color:#a8a29e; transition:color .15s; }
.cb-breadcrumb a:hover { color:#d97706; }
.cb-breadcrumb span { margin:0 .4rem; }
.cb-cover { width:100%; height:320px; object-fit:cover; border-radius:14px; margin-bottom:2rem; box-shadow:0 4px 20px rgba(0,0,0,.1); }
.cb-post-meta { display:flex; flex-wrap:wrap; align-items:center; gap:.75rem 1.25rem; font-size:.82rem; color:#78716c; margin-bottom:1.5rem; }
.cb-post-meta strong { color:#292524; }
.cb-post-cat { display:inline-block; font-size:.7rem; font-weight:700; text-transform:uppercase; letter-spacing:.08em; color:#d97706; margin-bottom:.6rem; }
.cb-post-title { font-size:2rem; font-weight:800; line-height:1.2; color:#1c1917; margin-bottom:1rem; }
.cb-tags { display:flex; flex-wrap:wrap; gap:.4rem; margin-top:.75rem; }
.cb-tag-sm { font-size:.72rem; padding:.2rem .65rem; border-radius:999px; border:1px solid #e7e5e4; color:#78716c; transition:border-color .15s,color .15s; }
.cb-tag-sm:hover { border-color:#d97706; color:#d97706; }
.cb-body { font-size:1rem; line-height:1.8; color:#292524; margin:2rem 0; }
.cb-body h2 { font-size:1.3rem; font-weight:700; margin:2rem 0 .75rem; }
.cb-body p { margin-bottom:1.1rem; }
.cb-author-box { display:flex; gap:1rem; align-items:flex-start; background:#faf9f7; border-radius:12px; padding:1.25rem; margin:2.5rem 0; border:1px solid #f5f5f4; }
.cb-author-avatar { width:52px; height:52px; border-radius:50%; background:#fef3c7; color:#92400e; font-size:1.4rem; font-weight:700; display:flex; align-items:center; justify-content:center; flex-shrink:0; overflow:hidden; }
.cb-author-avatar img { width:100%; height:100%; object-fit:cover; }
.cb-comment { background:#fff; border-radius:10px; padding:1rem 1.2rem; box-shadow:0 1px 3px rgba(0,0,0,.06); margin-bottom:.9rem; }
.cb-comment-header { display:flex; justify-content:space-between; align-items:center; margin-bottom:.5rem; }
.cb-comment-name { font-weight:700; font-size:.9rem; color:#292524; }
.cb-comment-date { font-size:.75rem; color:#a8a29e; }
.cb-comment-body { font-size:.875rem; color:#57534e; line-height:1.6; }
.cb-form-input { width:100%; border:1px solid #e7e5e4; border-radius:8px; padding:.6rem .9rem; font-size:.875rem; outline:none; transition:border-color .15s; font-family:inherit; }
.cb-form-input:focus { border-color:#fbbf24; }
.cb-form-textarea { min-height:110px; resize:vertical; }
.cb-btn { background:#f59e0b; color:#1c1917; font-weight:700; border:none; border-radius:8px; padding:.6rem 1.3rem; font-size:.875rem; cursor:pointer; transition:background .15s; }
.cb-btn:hover { background:#fbbf24; }
.cb-error-box { background:#fef2f2; border:1px solid #fecaca; color:#991b1b; border-radius:8px; padding:.75rem 1rem; font-size:.875rem; margin-bottom:1rem; }
.cb-error-box li { margin-left:1.1rem; margin-top:.2rem; }
.cb-related-grid { display:grid; grid-template-columns:repeat(auto-fill,minmax(200px,1fr)); gap:1.25rem; margin-top:1.25rem; }
</style>
@endpush

<div class="cb-article">

    {{-- Breadcrumb --}}
    <div class="cb-breadcrumb">
        <a href="{{ route('cafe-blog.posts.index') }}">Accueil</a>
        @if($post->category)
            <span>/</span>
            <a href="{{ route('cafe-blog.posts.index', ['category' => $post->category->slug]) }}">{{ $post->category->name }}</a>
        @endif
        <span>/</span>
        <span>{{ Str::limit($post->title, 45) }}</span>
    </div>

    {{-- Cover --}}
    @if($post->cover_image)
        <img class="cb-cover"
             src="{{ Storage::disk(config('cafe-blog.storage_disk'))->url($post->cover_image) }}"
             alt="{{ $post->title }}">
    @endif

    {{-- Header --}}
    @if($post->category)
        <a href="{{ route('cafe-blog.posts.index', ['category' => $post->category->slug]) }}" class="cb-post-cat">
            {{ $post->category->name }}
        </a>
    @endif

    <h1 class="cb-post-title">{{ $post->title }}</h1>

    <div class="cb-post-meta">
        <span>Par <strong>{{ $post->author->name }}</strong></span>
        <span>&bull;</span>
        <time datetime="{{ $post->published_at->toIso8601String() }}">
            {{ $post->published_at->format('d F Y') }}
        </time>
        <span>&bull;</span>
        <span>{{ $post->readingTime() }} min de lecture</span>
    </div>

    @if($post->tags->isNotEmpty())
        <div class="cb-tags">
            @foreach($post->tags as $tag)
                <a href="{{ route('cafe-blog.posts.index', ['tag' => $tag->slug]) }}" class="cb-tag-sm">
                    {{ $tag->name }}
                </a>
            @endforeach
        </div>
    @endif

    {{-- Body --}}
    <div class="cb-body">{!! $post->body !!}</div>

    {{-- Author --}}
    <div class="cb-author-box">
        <div class="cb-author-avatar">
            @if(!empty($post->author->avatar))
                <img src="{{ Storage::url($post->author->avatar) }}" alt="{{ $post->author->name }}">
            @else
                {{ strtoupper(substr($post->author->name, 0, 1)) }}
            @endif
        </div>
        <div>
            <div style="font-weight:700;color:#1c1917;margin-bottom:.25rem;">{{ $post->author->name }}</div>
            @if(!empty($post->author->bio))
                <div style="font-size:.875rem;color:#78716c;">{{ $post->author->bio }}</div>
            @endif
        </div>
    </div>

    {{-- Commentaires --}}
    <section id="commentaires" style="margin-top:2.5rem;">
        <h2 style="font-size:1.25rem;font-weight:700;margin-bottom:1.25rem;">
            Commentaires ({{ $post->approvedComments->count() }})
        </h2>

        @forelse($post->approvedComments as $comment)
            <div class="cb-comment">
                <div class="cb-comment-header">
                    <span class="cb-comment-name">{{ $comment->displayName() }}</span>
                    <span class="cb-comment-date">{{ $comment->created_at->diffForHumans() }}</span>
                </div>
                <p class="cb-comment-body">{{ $comment->body }}</p>
                @can('delete', $comment)
                    <form action="{{ route('cafe-blog.comments.destroy', $comment) }}" method="POST" style="margin-top:.5rem;">
                        @csrf @method('DELETE')
                        <button type="submit" style="background:none;border:none;cursor:pointer;font-size:.75rem;color:#ef4444;">
                            Supprimer
                        </button>
                    </form>
                @endcan
            </div>
        @empty
            <p style="color:#78716c;font-size:.875rem;margin-bottom:1.25rem;">Aucun commentaire pour l'instant.</p>
        @endforelse

        {{-- Formulaire --}}
        <div style="background:#fff;border-radius:12px;padding:1.5rem;box-shadow:0 1px 3px rgba(0,0,0,.07);margin-top:1.5rem;">
            <h3 style="font-size:1rem;font-weight:700;margin-bottom:1rem;">Laisser un commentaire</h3>

            @if($errors->any())
                <div class="cb-error-box">
                    <ul>@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
                </div>
            @endif

            <form action="{{ route('cafe-blog.comments.store', $post) }}" method="POST"
                  style="display:flex;flex-direction:column;gap:.9rem;">
                @csrf

                @guest
                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:.9rem;">
                        <div>
                            <label style="display:block;font-size:.8rem;font-weight:600;margin-bottom:.35rem;color:#57534e;">Nom *</label>
                            <input type="text" name="guest_name" value="{{ old('guest_name') }}" required class="cb-form-input">
                        </div>
                        <div>
                            <label style="display:block;font-size:.8rem;font-weight:600;margin-bottom:.35rem;color:#57534e;">Email *</label>
                            <input type="email" name="guest_email" value="{{ old('guest_email') }}" required class="cb-form-input">
                        </div>
                    </div>
                    <p style="font-size:.75rem;color:#a8a29e;">Votre email ne sera pas publie.
                        @if(config('cafe-blog.moderate_comments')) Votre commentaire sera soumis a moderation. @endif
                    </p>
                @endguest

                <div>
                    <label style="display:block;font-size:.8rem;font-weight:600;margin-bottom:.35rem;color:#57534e;">Commentaire *</label>
                    <textarea name="body" required class="cb-form-input cb-form-textarea">{{ old('body') }}</textarea>
                </div>

                <div>
                    <button type="submit" class="cb-btn">Publier le commentaire</button>
                </div>
            </form>
        </div>
    </section>

</div>

{{-- Articles similaires --}}
@if($related->isNotEmpty())
    <section style="max-width:740px;margin:3rem auto 0;">
        <h2 style="font-size:1.15rem;font-weight:700;margin-bottom:1.1rem;">Articles similaires</h2>
        <div class="cb-related-grid">
            @foreach($related as $relatedPost)
                @include('cafe-blog::partials.post-card', ['post' => $relatedPost])
            @endforeach
        </div>
    </section>
@endif

@endsection
