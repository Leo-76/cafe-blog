<article style="background:#fff;border-radius:12px;overflow:hidden;box-shadow:0 1px 3px rgba(0,0,0,.07);display:flex;flex-direction:column;transition:box-shadow .2s;">

    <a href="{{ route('cafe-blog.posts.show', $post->slug) }}"
       style="display:block;aspect-ratio:16/9;overflow:hidden;background:#e7e5e4;">
        @if($post->cover_image)
            <img src="{{ Storage::disk(config('cafe-blog.storage_disk'))->url($post->cover_image) }}"
                 alt="{{ $post->title }}"
                 style="width:100%;height:100%;object-fit:cover;transition:transform .3s;">
        @else
            <div style="width:100%;height:100%;background:linear-gradient(135deg,#fef3c7,#e7e5e4);display:flex;align-items:center;justify-content:center;">
                <span style="font-size:2.5rem;color:#a8a29e;font-family:serif;">C</span>
            </div>
        @endif
    </a>

    <div style="padding:1.1rem 1.2rem;display:flex;flex-direction:column;flex:1;">
        @if($post->category)
            <a href="{{ route('cafe-blog.posts.index', ['category' => $post->category->slug]) }}"
               style="font-size:.7rem;font-weight:700;text-transform:uppercase;letter-spacing:.08em;color:#d97706;margin-bottom:.4rem;display:inline-block;">
                {{ $post->category->name }}
            </a>
        @endif

        <h3 style="font-size:1rem;font-weight:700;line-height:1.35;margin-bottom:.5rem;">
            <a href="{{ route('cafe-blog.posts.show', $post->slug) }}"
               style="color:#1c1917;transition:color .15s;"
               onmouseover="this.style.color='#d97706'" onmouseout="this.style.color='#1c1917'">
                {{ $post->title }}
            </a>
        </h3>

        @if($post->excerpt)
            <p style="font-size:.875rem;color:#78716c;line-height:1.55;flex:1;margin-bottom:.9rem;">
                {{ Str::limit($post->excerpt, 110) }}
            </p>
        @endif

        <div style="display:flex;justify-content:space-between;font-size:.75rem;color:#a8a29e;border-top:1px solid #f5f5f4;padding-top:.65rem;margin-top:auto;">
            <span>{{ $post->author->name }}</span>
            <span>{{ $post->published_at->format('d M Y') }}</span>
        </div>
    </div>

</article>
