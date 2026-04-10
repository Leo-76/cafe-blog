<?php

use Illuminate\Support\Facades\Route;
use VotrePseudo\CafeBlog\Http\Controllers\AdminPostController;
use VotrePseudo\CafeBlog\Http\Controllers\CommentController;
use VotrePseudo\CafeBlog\Http\Controllers\PostController;

$prefix     = config('cafe-blog.route_prefix', 'blog');
$middleware = config('cafe-blog.middleware', ['web']);
$adminMw    = config('cafe-blog.admin_middleware', ['web', 'auth']);

// ---------------------------------------------------------------------------
// Routes publiques
// ---------------------------------------------------------------------------
Route::middleware($middleware)->prefix($prefix)->name('cafe-blog.')->group(function () {
    Route::get('/',              [PostController::class, 'index'])->name('posts.index');
    Route::get('/{slug}',        [PostController::class, 'show'])->name('posts.show');
    Route::post('/{post}/comments', [CommentController::class, 'store'])->name('comments.store');
});

// ---------------------------------------------------------------------------
// Routes authentifiees
// ---------------------------------------------------------------------------
Route::middleware(['web', 'auth'])->prefix($prefix)->name('cafe-blog.')->group(function () {
    Route::delete('/comments/{comment}',         [CommentController::class, 'destroy'])->name('comments.destroy');
    Route::patch('/comments/{comment}/approve',  [CommentController::class, 'approve'])->name('comments.approve');
});

// ---------------------------------------------------------------------------
// Routes admin
// ---------------------------------------------------------------------------
Route::middleware($adminMw)->prefix($prefix . '/admin')->name('cafe-blog.admin.')->group(function () {
    Route::resource('posts', AdminPostController::class);
});
