@extends('cafe-blog::layouts.app')

@section('title', 'Administration — Articles')

@section('content')

@push('styles')
<style>
.cb-admin-header { display:flex; justify-content:space-between; align-items:center; margin-bottom:1.75rem; }
.cb-admin-header h1 { font-size:1.4rem; font-weight:800; }
.cb-btn-primary { background:#f59e0b; color:#1c1917; font-weight:700; border:none; border-radius:8px; padding:.55rem 1.1rem; font-size:.875rem; cursor:pointer; transition:background .15s; display:inline-block; }
.cb-btn-primary:hover { background:#fbbf24; }
.cb-table-wrap { background:#fff; border-radius:12px; box-shadow:0 1px 3px rgba(0,0,0,.07); overflow:hidden; }
table { width:100%; border-collapse:collapse; font-size:.875rem; }
thead { background:#faf9f7; }
th { text-align:left; padding:.8rem 1.1rem; font-weight:700; color:#78716c; font-size:.78rem; text-transform:uppercase; letter-spacing:.05em; border-bottom:1px solid #f5f5f4; }
td { padding:.8rem 1.1rem; border-bottom:1px solid #f5f5f4; color:#292524; vertical-align:middle; }
tr:last-child td { border-bottom:none; }
tr:hover td { background:#faf9f7; }
.cb-badge-pub { background:#dcfce7; color:#166534; font-size:.7rem; padding:.2rem .55rem; border-radius:999px; font-weight:700; }
.cb-badge-draft { background:#f5f5f4; color:#78716c; font-size:.7rem; padding:.2rem .55rem; border-radius:999px; font-weight:700; }
.cb-action-link { font-size:.8rem; color:#3b82f6; transition:color .15s; margin-right:.6rem; }
.cb-action-link:hover { color:#1d4ed8; text-decoration:underline; }
.cb-action-del { font-size:.8rem; color:#ef4444; background:none; border:none; cursor:pointer; transition:color .15s; }
.cb-action-del:hover { color:#b91c1c; text-decoration:underline; }
</style>
@endpush

<div class="cb-admin-header">
    <h1>Gestion des articles</h1>
    <a href="{{ route('cafe-blog.admin.posts.create') }}" class="cb-btn-primary">Nouvel article</a>
</div>

<div class="cb-table-wrap">
    <table>
        <thead>
            <tr>
                <th>Titre</th>
                <th>Categorie</th>
                <th>Statut</th>
                <th>Date</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($posts as $post)
                <tr>
                    <td style="font-weight:600;max-width:280px;">{{ Str::limit($post->title, 55) }}</td>
                    <td style="color:#78716c;">{{ $post->category?->name ?? '—' }}</td>
                    <td>
                        @if($post->isPublished())
                            <span class="cb-badge-pub">Publie</span>
                        @else
                            <span class="cb-badge-draft">Brouillon</span>
                        @endif
                    </td>
                    <td style="color:#78716c;">{{ $post->published_at?->format('d/m/Y') ?? '—' }}</td>
                    <td style="white-space:nowrap;">
                        <a href="{{ route('cafe-blog.posts.show', $post->slug) }}" class="cb-action-link">Voir</a>
                        <a href="{{ route('cafe-blog.admin.posts.edit', $post) }}" class="cb-action-link">Editer</a>
                        <form action="{{ route('cafe-blog.admin.posts.destroy', $post) }}" method="POST"
                              style="display:inline;" onsubmit="return confirm('Supprimer cet article ?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="cb-action-del">Supprimer</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" style="text-align:center;color:#a8a29e;padding:2.5rem;">
                        Aucun article pour le moment.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div style="margin-top:1.25rem;">{{ $posts->links() }}</div>

@endsection
