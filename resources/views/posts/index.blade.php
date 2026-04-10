@extends('cafe-blog::layouts.app')

@section('title', 'Articles — ' . config('cafe-blog.name'))

@section('content')

@push('styles')
<style>
.cb-grid { display:grid; grid-template-columns:repeat(auto-fill,minmax(290px,1fr)); gap:1.5rem; }
.cb-sidebar-box { background:#fff; border-radius:12px; box-shadow:0 1px 3px rgba(0,0,0,.07); padding:1.2rem; margin-bottom:1.25rem; }
.cb-sidebar-box h2 { font-size:.7rem; font-weight:700; text-transform:uppercase; letter-spacing:.08em; color:#78716c; margin-bottom:.9rem; }
.cb-tag { display:inline-block; font-size:.72rem; padding:.25rem .7rem; border-radius:999px; border:1px solid #e7e5e4; color:#78716c; margin:.2rem; transition:border-color .15s,color .15s; }
.cb-tag:hover,.cb-tag--active { border-color:#d97706; color:#d97706; }
.cb-search { display:flex; gap:.5rem; margin-bottom:2rem; }
.cb-search input { flex:1; border:1px solid #e7e5e4; border-radius:8px; padding:.55rem 1rem; font-size:.875rem; outline:none; transition:border-color .15s; }
.cb-search input:focus { border-color:#fbbf24; }
.cb-search button { background:#f59e0b; color:#1c1917; font-weight:700; border:none; border-radius:8px; padding:.55rem 1.1rem; font-size:.875rem; cursor:pointer; transition:background .15s; }
.cb-search button:hover { background:#fbbf24; }
.cb-hero { position:relative; border-radius:16px; overflow:hidden; background:#1c1917; color:#fff; margin-bottom:2.5rem; min-height:220px; display:flex; align-items:flex-end; }
.cb-hero img { position:absolute; inset:0; width:100%; height:100%; object-fit:cover; opacity:.45; }
.cb-hero__body { position:relative; padding:2rem; max-width:640px; }
.cb-hero__label { font-size:.7rem; font-weight:700; text-transform:uppercase; letter-spacing:.1em; color:#fbbf24; margin-bottom:.5rem; }
.cb-hero__title { font-size:1.5rem; font-weight:800; line-height:1.25; margin-bottom:.75rem; }
.cb-hero__excerpt { font-size:.9rem; color:#d4d4d4; margin-bottom:1rem; }
.cb-hero__btn { display:inline-block; background:#f59e0b; color:#1c1917; font-weight:700; padding:.5rem 1.1rem; border-radius:8px; font-size:.875rem; transition:background .15s; }
.cb-hero__btn:hover { background:#fbbf24; }
.cb-layout { display:flex; gap:2rem; align-items:flex-start; }
.cb-layout__main { flex:1; min-width:0; }
.cb-layout__sidebar { width:220px; flex-shrink:0; }
@media(max-width:700px){ .cb-layout { flex-direction:column; } .cb-layout__sidebar { width:100%; } }
.cb-cat-item { display:flex; justify-content:space-between; font-size:.875rem; color:#57534e; padding:.3rem 0; border-bottom:1px solid #f5f5f4; transition:color .15s; }
.cb-cat-item:hover { color:#d97706; }
.cb-cat-item--active { color:#d97706; font-weight:700; }
.cb-badge { background:#f5f5f4; color:#78716c; font-size:.72rem; padding:.15rem .5rem; border-radius:999px; }
</style>
@endpush

{{-- Hero --}}
@if($featured && !request('search') && !request('category') && !request('tag'))
<div class="cb-hero">
    @if($featured->cover_image)
        <img src="{{ Storage::disk(config('cafe-blog.storage_disk'))->url($featured->cover_image) }}" alt="{{ $featured->title }}">
    @endif
    <div class="cb-hero__body">
        <div class="cb-hero__label">Article a la une</div>
        <h1 class="cb-hero__title">{{ $featured->title }}</h1>
        @if($featured->excerpt)
            <p class="cb-hero__excerpt">{{ Str::limit($featured->excerpt, 130) }}</p>
        @endif
        <a href="{{ route('cafe-blog.posts.show', $featured->slug) }}" class="cb-hero__btn">Lire l'article</a>
    </div>
</div>
@endif

<div class="cb-layout">
    <div class="cb-layout__main">

        {{-- Recherche --}}
        <form action="{{ route('cafe-blog.posts.index') }}" method="GET" class="cb-search">
            <input type="text" name="search" value="{{ $search }}" placeholder="Rechercher un article...">
            <button type="submit">Rechercher</button>
            @if($search)
                <a href="{{ route('cafe-blog.posts.index') }}"
                   style="padding:.55rem 1rem;border:1px solid #e7e5e4;border-radius:8px;font-size:.875rem;color:#78716c;">
                    Effacer
                </a>
            @endif
        </form>

        @if($posts->isEmpty())
            <p style="text-align:center;color:#78716c;padding:3rem 0;">Aucun article trouve.</p>
        @else
            <div class="cb-grid">
                @foreach($posts as $post)
                    @include('cafe-blog::partials.post-card', ['post' => $post])
                @endforeach
            </div>
            <div style="margin-top:2rem;">{{ $posts->links() }}</div>
        @endif

    </div>

    <aside class="cb-layout__sidebar">

        <div class="cb-sidebar-box">
            <h2>Categories</h2>
            @foreach($categories as $cat)
                <a href="{{ route('cafe-blog.posts.index', ['category' => $cat->slug]) }}"
                   class="cb-cat-item {{ request('category') === $cat->slug ? 'cb-cat-item--active' : '' }}">
                    <span>{{ $cat->name }}</span>
                    <span class="cb-badge">{{ $cat->posts_count }}</span>
                </a>
            @endforeach
        </div>

        <div class="cb-sidebar-box">
            <h2>Tags</h2>
            <div>
                @foreach($tags as $tag)
                    <a href="{{ route('cafe-blog.posts.index', ['tag' => $tag->slug]) }}"
                       class="cb-tag {{ request('tag') === $tag->slug ? 'cb-tag--active' : '' }}">
                        {{ $tag->name }}
                    </a>
                @endforeach
            </div>
        </div>

    </aside>
</div>

@endsection
