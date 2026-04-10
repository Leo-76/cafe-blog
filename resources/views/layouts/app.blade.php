<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="@yield('meta_description', config('cafe-blog.description'))">
    <title>@yield('title', config('cafe-blog.name'))</title>
    @stack('styles')
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: system-ui, sans-serif; background: #faf9f7; color: #292524; line-height: 1.65; font-size: 16px; }
        a { color: inherit; text-decoration: none; }
        img { max-width: 100%; display: block; }
        .cb-container { max-width: 1120px; margin: 0 auto; padding: 0 1.25rem; }
        /* Nav */
        .cb-nav { background: #1c1917; color: #e7e5e4; padding: .9rem 0; }
        .cb-nav__inner { display: flex; align-items: center; justify-content: space-between; gap: 1rem; }
        .cb-nav__brand { font-size: 1.1rem; font-weight: 700; letter-spacing: .02em; color: #e7e5e4; }
        .cb-nav__brand:hover { color: #fbbf24; }
        .cb-nav__links { display: flex; align-items: center; gap: 1.25rem; font-size: .875rem; }
        .cb-nav__links a { color: #a8a29e; transition: color .15s; }
        .cb-nav__links a:hover { color: #fbbf24; }
        .cb-nav__links form { display: inline; }
        .cb-nav__links button { background: none; border: none; cursor: pointer; color: #a8a29e; font-size: .875rem; transition: color .15s; }
        .cb-nav__links button:hover { color: #fbbf24; }
        /* Flash */
        .cb-flash { background: #dcfce7; border: 1px solid #bbf7d0; color: #166534; padding: .6rem 1rem; font-size: .875rem; text-align: center; }
        /* Main */
        .cb-main { padding: 2.5rem 0 4rem; }
        /* Footer */
        .cb-footer { background: #1c1917; color: #78716c; font-size: .8rem; text-align: center; padding: 1.25rem 0; margin-top: auto; }
    </style>
</head>
<body>

<header class="cb-nav">
    <div class="cb-container">
        <div class="cb-nav__inner">
            <a href="{{ route('cafe-blog.posts.index') }}" class="cb-nav__brand">
                {{ config('cafe-blog.name') }}
            </a>
            <nav class="cb-nav__links">
                <a href="{{ route('cafe-blog.posts.index') }}">Articles</a>
                @auth
                    @if(auth()->user()->{config('cafe-blog.admin_column', 'is_admin')} ?? false)
                        <a href="{{ route('cafe-blog.admin.posts.index') }}">Administration</a>
                    @endif
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit">Deconnexion</button>
                    </form>
                @else
                    <a href="{{ route('login') }}">Connexion</a>
                @endauth
            </nav>
        </div>
    </div>
</header>

@if(session('cafe_blog_success'))
    <div class="cb-flash">{{ session('cafe_blog_success') }}</div>
@endif

<main class="cb-main">
    <div class="cb-container">
        @yield('content')
    </div>
</main>

<footer class="cb-footer">
    <div class="cb-container">
        &copy; {{ date('Y') }} {{ config('cafe-blog.name') }} — Blog cafe
    </div>
</footer>

@stack('scripts')
</body>
</html>
