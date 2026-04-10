<?php

namespace VotrePseudo\CafeBlog\Providers;

use Illuminate\Support\ServiceProvider;
use VotrePseudo\CafeBlog\Models\Comment;
use VotrePseudo\CafeBlog\Policies\CommentPolicy;
use Illuminate\Support\Facades\Gate;

class CafeBlogServiceProvider extends ServiceProvider
{
    /**
     * Chemin racine du package.
     */
    private string $packagePath;

    public function __construct($app)
    {
        parent::__construct($app);
        $this->packagePath = dirname(__DIR__, 2);
    }

    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->mergeConfigFrom(
            $this->packagePath . '/config/cafe-blog.php',
            'cafe-blog'
        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // -----------------------------------------------------------------
        // Routes
        // -----------------------------------------------------------------
        $this->loadRoutesFrom($this->packagePath . '/routes/web.php');

        // -----------------------------------------------------------------
        // Migrations
        // -----------------------------------------------------------------
        $this->loadMigrationsFrom($this->packagePath . '/database/migrations');

        // -----------------------------------------------------------------
        // Vues (namespace: cafe-blog)
        // -----------------------------------------------------------------
        $this->loadViewsFrom($this->packagePath . '/resources/views', 'cafe-blog');

        // -----------------------------------------------------------------
        // Traductions
        // -----------------------------------------------------------------
        $this->loadTranslationsFrom($this->packagePath . '/resources/lang', 'cafe-blog');

        // -----------------------------------------------------------------
        // Policy
        // -----------------------------------------------------------------
        Gate::policy(Comment::class, CommentPolicy::class);

        // -----------------------------------------------------------------
        // Publishables (vendor:publish)
        // -----------------------------------------------------------------
        if ($this->app->runningInConsole()) {
            $this->publishes([
                $this->packagePath . '/config/cafe-blog.php' => config_path('cafe-blog.php'),
            ], 'cafe-blog-config');

            $this->publishes([
                $this->packagePath . '/database/migrations' => database_path('migrations'),
            ], 'cafe-blog-migrations');

            $this->publishes([
                $this->packagePath . '/resources/views' => resource_path('views/vendor/cafe-blog'),
            ], 'cafe-blog-views');

            $this->publishes([
                $this->packagePath . '/resources/lang' => lang_path('vendor/cafe-blog'),
            ], 'cafe-blog-lang');

            // Publie tout en une commande
            $this->publishes([
                $this->packagePath . '/config/cafe-blog.php' => config_path('cafe-blog.php'),
                $this->packagePath . '/database/migrations' => database_path('migrations'),
                $this->packagePath . '/resources/views'     => resource_path('views/vendor/cafe-blog'),
                $this->packagePath . '/resources/lang'      => lang_path('vendor/cafe-blog'),
            ], 'cafe-blog');
        }
    }
}
