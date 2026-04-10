# Cafe Blog — Laravel Package

A plug-and-play Laravel blog package dedicated to coffee culture. Install it in any Laravel 11 application and get a fully working blog in minutes.

[![Tests](https://github.com/votre-pseudo/cafe-blog/actions/workflows/tests.yml/badge.svg)](https://github.com/votre-pseudo/cafe-blog/actions)
[![Latest Version on Packagist](https://img.shields.io/packagist/v/votre-pseudo/cafe-blog.svg)](https://packagist.org/packages/votre-pseudo/cafe-blog)
[![License](https://img.shields.io/github/license/votre-pseudo/cafe-blog)](LICENSE)

## Features

- Article listing with pagination, search, category and tag filters
- Article detail page with reading time, author bio and related posts
- Comment system with optional moderation
- Full admin CRUD (create, edit, delete, publish)
- Configurable route prefix, middleware, storage disk, and user model
- Publishable views — override any Blade template in your application
- Publishable config and migrations
- Translations (French and English included)
- All tables prefixed with `cafe_blog_` to avoid conflicts
- Zero JavaScript dependency — works with any frontend stack

## Requirements

- PHP >= 8.2
- Laravel >= 11.0

## Installation

### 1. Install via Composer

```bash
composer require votre-pseudo/cafe-blog
```

The service provider is auto-discovered by Laravel. No manual registration needed.

### 2. Publish and run the migrations

```bash
php artisan vendor:publish --tag=cafe-blog-migrations
php artisan migrate
```

### 3. Publish the config (optional but recommended)

```bash
php artisan vendor:publish --tag=cafe-blog-config
```

This creates `config/cafe-blog.php` in your application.

### 4. Add `is_admin` to your users table (if not already present)

```bash
php artisan make:migration add_is_admin_to_users_table
```

```php
Schema::table('users', function (Blueprint $table) {
    $table->boolean('is_admin')->default(false)->after('email');
});
```

### 5. Visit your blog

Start your server and visit:

```
http://localhost:8000/blog
```

## Configuration

After publishing, edit `config/cafe-blog.php`:

```php
return [
    'name'              => 'La Cafeotheque',
    'description'       => 'Un blog sur le cafe.',
    'route_prefix'      => 'blog',          // URL: /blog, /blog/{slug}
    'middleware'        => ['web'],
    'admin_middleware'  => ['web', 'auth'],
    'posts_per_page'    => 9,
    'user_model'        => \App\Models\User::class,
    'users_table'       => 'users',
    'admin_column'      => 'is_admin',      // column that marks admins
    'storage_disk'      => 'public',
    'moderate_comments' => true,
    'seed_demo_data'    => false,
];
```

All values can also be set via `.env`:

```env
CAFE_BLOG_NAME="Mon Blog Cafe"
CAFE_BLOG_PREFIX=articles
CAFE_BLOG_MODERATE=false
CAFE_BLOG_PER_PAGE=12
```

## Seeder de demonstration

To seed the database with sample coffee articles:

```bash
php artisan db:seed --class="VotrePseudo\CafeBlog\Database\Seeders\CafeBlogSeeder"
```

Or call it from your own `DatabaseSeeder`:

```php
use VotrePseudo\CafeBlog\Database\Seeders\CafeBlogSeeder;

public function run(): void
{
    $this->call(CafeBlogSeeder::class);
}
```

## Customising views

Publish the views to override any template:

```bash
php artisan vendor:publish --tag=cafe-blog-views
```

Files are copied to `resources/views/vendor/cafe-blog/`. Laravel automatically uses your overrides in place of the package views.

## Available routes

| Method | URI | Name | Description |
|--------|-----|------|-------------|
| GET | `/blog` | `cafe-blog.posts.index` | Article listing |
| GET | `/blog/{slug}` | `cafe-blog.posts.show` | Article detail |
| POST | `/blog/{post}/comments` | `cafe-blog.comments.store` | Post a comment |
| DELETE | `/blog/comments/{comment}` | `cafe-blog.comments.destroy` | Delete a comment |
| PATCH | `/blog/comments/{comment}/approve` | `cafe-blog.comments.approve` | Approve a comment |
| GET | `/blog/admin/posts` | `cafe-blog.admin.posts.index` | Admin listing |
| GET | `/blog/admin/posts/create` | `cafe-blog.admin.posts.create` | Admin create form |
| POST | `/blog/admin/posts` | `cafe-blog.admin.posts.store` | Store new post |
| GET | `/blog/admin/posts/{post}/edit` | `cafe-blog.admin.posts.edit` | Admin edit form |
| PUT | `/blog/admin/posts/{post}` | `cafe-blog.admin.posts.update` | Update a post |
| DELETE | `/blog/admin/posts/{post}` | `cafe-blog.admin.posts.destroy` | Delete a post |

The `/blog` prefix changes according to `cafe-blog.route_prefix` in your config.

## Using models in your application

```php
use VotrePseudo\CafeBlog\Models\Post;
use VotrePseudo\CafeBlog\Models\Category;
use VotrePseudo\CafeBlog\Models\Tag;

// Latest published posts
$posts = Post::published()->latest('published_at')->take(5)->get();

// Posts by category
$posts = Post::published()
    ->whereHas('category', fn ($q) => $q->where('slug', 'origines'))
    ->get();

// Full-text search
$posts = Post::published()->search('ethiopie')->get();
```

## Running tests

```bash
composer test
```

Or directly:

```bash
vendor/bin/phpunit
```

## Upgrading

When a new version introduces migrations, re-publish and migrate:

```bash
php artisan vendor:publish --tag=cafe-blog-migrations --force
php artisan migrate
```

## Contributing

Pull requests are welcome. Please read [CONTRIBUTING.md](CONTRIBUTING.md) first and make sure all tests pass.

## License

MIT — see [LICENSE](LICENSE).
