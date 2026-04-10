<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $usersTable = config('cafe-blog.users_table', 'users');

        Schema::create('cafe_blog_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->timestamps();
        });

        Schema::create('cafe_blog_tags', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->timestamps();
        });

        Schema::create('cafe_blog_posts', function (Blueprint $table) use ($usersTable) {
            $table->id();
            $table->foreignId('user_id')->constrained($usersTable)->cascadeOnDelete();
            $table->foreignId('category_id')->nullable()->constrained('cafe_blog_categories')->nullOnDelete();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('excerpt')->nullable();
            $table->longText('body');
            $table->string('cover_image')->nullable();
            $table->timestamp('published_at')->nullable()->index();
            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();
            $table->timestamps();
        });

        Schema::create('cafe_blog_post_tag', function (Blueprint $table) {
            $table->foreignId('post_id')->constrained('cafe_blog_posts')->cascadeOnDelete();
            $table->foreignId('tag_id')->constrained('cafe_blog_tags')->cascadeOnDelete();
            $table->timestamps();
            $table->primary(['post_id', 'tag_id']);
        });

        Schema::create('cafe_blog_comments', function (Blueprint $table) use ($usersTable) {
            $table->id();
            $table->foreignId('post_id')->constrained('cafe_blog_posts')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained($usersTable)->nullOnDelete();
            $table->string('guest_name')->nullable();
            $table->string('guest_email')->nullable();
            $table->text('body');
            $table->boolean('approved')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cafe_blog_comments');
        Schema::dropIfExists('cafe_blog_post_tag');
        Schema::dropIfExists('cafe_blog_posts');
        Schema::dropIfExists('cafe_blog_tags');
        Schema::dropIfExists('cafe_blog_categories');
    }
};
