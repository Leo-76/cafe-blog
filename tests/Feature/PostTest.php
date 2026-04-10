<?php

namespace VotrePseudo\CafeBlog\Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use VotrePseudo\CafeBlog\Models\Category;
use VotrePseudo\CafeBlog\Models\Post;
use VotrePseudo\CafeBlog\Models\Tag;
use VotrePseudo\CafeBlog\Tests\TestCase;

class PostTest extends TestCase
{
    use RefreshDatabase;

    // -----------------------------------------------------------------------
    // Listing public
    // -----------------------------------------------------------------------

    public function test_index_returns_only_published_posts(): void
    {
        $published = Post::factory()->create(['published_at' => now()->subDay(), 'user_id' => 1]);
        $draft     = Post::factory()->create(['published_at' => null, 'user_id' => 1]);

        $response = $this->get(route('cafe-blog.posts.index'));

        $response->assertOk();
        $response->assertSee($published->title);
        $response->assertDontSee($draft->title);
    }

    public function test_index_can_be_searched(): void
    {
        $match = Post::factory()->create([
            'title'        => 'Guide espresso avance',
            'published_at' => now()->subDay(),
            'user_id'      => 1,
        ]);
        $other = Post::factory()->create([
            'title'        => 'Histoire du cafe en Ethiopie',
            'published_at' => now()->subDay(),
            'user_id'      => 1,
        ]);

        $response = $this->get(route('cafe-blog.posts.index', ['search' => 'espresso']));

        $response->assertOk();
        $response->assertSee($match->title);
        $response->assertDontSee($other->title);
    }

    public function test_index_filters_by_category(): void
    {
        $cat   = Category::factory()->create();
        $match = Post::factory()->create(['category_id' => $cat->id, 'published_at' => now()->subDay(), 'user_id' => 1]);
        $other = Post::factory()->create(['published_at' => now()->subDay(), 'user_id' => 1]);

        $response = $this->get(route('cafe-blog.posts.index', ['category' => $cat->slug]));

        $response->assertOk();
        $response->assertSee($match->title);
        $response->assertDontSee($other->title);
    }

    public function test_index_filters_by_tag(): void
    {
        $tag   = Tag::factory()->create();
        $match = Post::factory()->create(['published_at' => now()->subDay(), 'user_id' => 1]);
        $match->tags()->attach($tag);
        $other = Post::factory()->create(['published_at' => now()->subDay(), 'user_id' => 1]);

        $response = $this->get(route('cafe-blog.posts.index', ['tag' => $tag->slug]));

        $response->assertOk();
        $response->assertSee($match->title);
        $response->assertDontSee($other->title);
    }

    // -----------------------------------------------------------------------
    // Post individuel
    // -----------------------------------------------------------------------

    public function test_show_returns_published_post(): void
    {
        $post = Post::factory()->create(['published_at' => now()->subDay(), 'user_id' => 1]);

        $this->get(route('cafe-blog.posts.show', $post->slug))->assertOk();
    }

    public function test_show_returns_404_for_draft(): void
    {
        $draft = Post::factory()->create(['published_at' => null, 'user_id' => 1]);

        $this->get(route('cafe-blog.posts.show', $draft->slug))->assertNotFound();
    }

    // -----------------------------------------------------------------------
    // Commentaires
    // -----------------------------------------------------------------------

    public function test_guest_can_submit_comment(): void
    {
        $post = Post::factory()->create(['published_at' => now()->subDay(), 'user_id' => 1]);

        $response = $this->post(route('cafe-blog.comments.store', $post), [
            'guest_name'  => 'Jean Dupont',
            'guest_email' => 'jean@example.com',
            'body'        => 'Super article sur le cafe !',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('cafe_blog_comments', [
            'post_id'    => $post->id,
            'guest_name' => 'Jean Dupont',
            'approved'   => false, // moderation activee
        ]);
    }

    public function test_comment_requires_body(): void
    {
        $post = Post::factory()->create(['published_at' => now()->subDay(), 'user_id' => 1]);

        $this->post(route('cafe-blog.comments.store', $post), [
            'guest_name'  => 'Jean',
            'guest_email' => 'jean@example.com',
            'body'        => '',
        ])->assertSessionHasErrors('body');
    }

    // -----------------------------------------------------------------------
    // Admin
    // -----------------------------------------------------------------------

    public function test_admin_index_requires_auth(): void
    {
        $this->get(route('cafe-blog.admin.posts.index'))->assertRedirect();
    }
}
