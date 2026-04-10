@extends('cafe-blog::layouts.app')

@section('title', isset($post) ? 'Editer : ' . $post->title : 'Nouvel article')

@section('content')

@push('styles')
<style>
.cb-form-wrap { max-width:760px; margin:0 auto; }
.cb-form-wrap h1 { font-size:1.4rem; font-weight:800; margin-bottom:1.75rem; }
.cb-card { background:#fff; border-radius:12px; box-shadow:0 1px 3px rgba(0,0,0,.07); padding:2rem; }
.cb-field { margin-bottom:1.25rem; }
.cb-label { display:block; font-size:.8rem; font-weight:700; color:#57534e; margin-bottom:.4rem; }
.cb-input { width:100%; border:1px solid #e7e5e4; border-radius:8px; padding:.6rem .9rem; font-size:.875rem; outline:none; transition:border-color .15s; font-family:inherit; color:#292524; background:#fff; }
.cb-input:focus { border-color:#fbbf24; }
.cb-textarea { min-height:280px; resize:vertical; font-family:monospace; font-size:.82rem; }
.cb-grid-2 { display:grid; grid-template-columns:1fr 1fr; gap:1rem; }
.cb-hint { font-size:.72rem; color:#a8a29e; margin-top:.3rem; }
.cb-error-box { background:#fef2f2; border:1px solid #fecaca; color:#991b1b; border-radius:8px; padding:.75rem 1rem; font-size:.875rem; margin-bottom:1.5rem; }
.cb-error-box li { margin-left:1.1rem; margin-top:.2rem; }
.cb-btn { background:#f59e0b; color:#1c1917; font-weight:700; border:none; border-radius:8px; padding:.65rem 1.4rem; font-size:.875rem; cursor:pointer; transition:background .15s; }
.cb-btn:hover { background:#fbbf24; }
.cb-btn-cancel { display:inline-block; padding:.65rem 1.4rem; border:1px solid #e7e5e4; border-radius:8px; font-size:.875rem; color:#78716c; transition:background .15s; }
.cb-btn-cancel:hover { background:#f5f5f4; }
.cb-tags-wrap { display:flex; flex-wrap:wrap; gap:.5rem .9rem; }
.cb-tag-check { display:flex; align-items:center; gap:.4rem; font-size:.875rem; color:#57534e; cursor:pointer; }
.cb-details { border:1px solid #e7e5e4; border-radius:8px; padding:1rem 1.1rem; margin-bottom:1.25rem; }
.cb-details summary { cursor:pointer; font-size:.875rem; font-weight:700; color:#57534e; user-select:none; }
.cb-details-inner { margin-top:1rem; display:flex; flex-direction:column; gap:1rem; }
.cb-current-img { height:100px; border-radius:8px; object-fit:cover; margin-bottom:.5rem; }
</style>
@endpush

<div class="cb-form-wrap">
    <h1>{{ isset($post) ? 'Modifier l\'article' : 'Nouvel article' }}</h1>

    @if($errors->any())
        <div class="cb-error-box">
            <ul>@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
        </div>
    @endif

    <form action="{{ isset($post) ? route('cafe-blog.admin.posts.update', $post) : route('cafe-blog.admin.posts.store') }}"
          method="POST" enctype="multipart/form-data">
        @csrf
        @isset($post) @method('PUT') @endisset

        <div class="cb-card" style="display:flex;flex-direction:column;gap:0;">

            <div class="cb-field">
                <label class="cb-label">Titre *</label>
                <input type="text" name="title" value="{{ old('title', $post->title ?? '') }}" required class="cb-input">
            </div>

            <div class="cb-field">
                <label class="cb-label">Slug (URL)</label>
                <input type="text" name="slug" value="{{ old('slug', $post->slug ?? '') }}" class="cb-input" style="font-family:monospace;font-size:.82rem;">
                <p class="cb-hint">Laisser vide pour generer depuis le titre.</p>
            </div>

            <div class="cb-field">
                <label class="cb-label">Extrait</label>
                <textarea name="excerpt" rows="2" class="cb-input" style="min-height:60px;resize:vertical;">{{ old('excerpt', $post->excerpt ?? '') }}</textarea>
            </div>

            <div class="cb-field">
                <label class="cb-label">Contenu *</label>
                <textarea name="body" required class="cb-input cb-textarea">{{ old('body', $post->body ?? '') }}</textarea>
                <p class="cb-hint">HTML accepte.</p>
            </div>

            <div class="cb-grid-2" style="margin-bottom:1.25rem;">
                <div>
                    <label class="cb-label">Categorie</label>
                    <select name="category_id" class="cb-input">
                        <option value="">-- Aucune --</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}"
                                {{ old('category_id', $post->category_id ?? '') == $cat->id ? 'selected' : '' }}>
                                {{ $cat->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="cb-label">Date de publication</label>
                    <input type="datetime-local" name="published_at"
                           value="{{ old('published_at', isset($post) && $post->published_at ? $post->published_at->format('Y-m-d\TH:i') : '') }}"
                           class="cb-input">
                    <p class="cb-hint">Vide = brouillon.</p>
                </div>
            </div>

            <div class="cb-field">
                <label class="cb-label">Tags</label>
                <div class="cb-tags-wrap">
                    @foreach($tags as $tag)
                        <label class="cb-tag-check">
                            <input type="checkbox" name="tags[]" value="{{ $tag->id }}"
                                {{ collect(old('tags', isset($post) ? $post->tags->pluck('id')->toArray() : []))->contains($tag->id) ? 'checked' : '' }}>
                            {{ $tag->name }}
                        </label>
                    @endforeach
                </div>
            </div>

            <div class="cb-field">
                <label class="cb-label">Image de couverture</label>
                @if(isset($post) && $post->cover_image)
                    <img class="cb-current-img"
                         src="{{ Storage::disk(config('cafe-blog.storage_disk'))->url($post->cover_image) }}"
                         alt="Couverture actuelle">
                    <p class="cb-hint" style="margin-bottom:.4rem;">Telechargez une nouvelle image pour remplacer.</p>
                @endif
                <input type="file" name="cover_image" accept="image/*" class="cb-input" style="padding:.4rem;">
            </div>

            <details class="cb-details">
                <summary>Referencement (SEO)</summary>
                <div class="cb-details-inner">
                    <div>
                        <label class="cb-label">Titre SEO</label>
                        <input type="text" name="meta_title" value="{{ old('meta_title', $post->meta_title ?? '') }}" class="cb-input">
                    </div>
                    <div>
                        <label class="cb-label">Meta description</label>
                        <textarea name="meta_description" rows="2" class="cb-input" style="min-height:60px;resize:vertical;">{{ old('meta_description', $post->meta_description ?? '') }}</textarea>
                    </div>
                </div>
            </details>

            <div style="display:flex;gap:.75rem;align-items:center;padding-top:.5rem;">
                <button type="submit" class="cb-btn">
                    {{ isset($post) ? 'Mettre a jour' : 'Creer l\'article' }}
                </button>
                <a href="{{ route('cafe-blog.admin.posts.index') }}" class="cb-btn-cancel">Annuler</a>
            </div>

        </div>
    </form>
</div>

@endsection
