<?php

namespace VotrePseudo\CafeBlog\Tests\Unit;

use VotrePseudo\CafeBlog\Models\Post;
use VotrePseudo\CafeBlog\Tests\TestCase;

class PostModelTest extends TestCase
{
    public function test_reading_time_calculated_from_body(): void
    {
        $words = str_repeat('mot ', 400);
        $post  = new Post(['body' => $words]);

        $this->assertEquals(2, $post->readingTime());
    }

    public function test_slug_auto_generated_from_title(): void
    {
        $post = Post::factory()->create([
            'title'    => 'Le meilleur espresso du monde',
            'slug'     => '',
            'user_id'  => 1,
        ]);

        $this->assertStringContainsString('meilleur', $post->slug);
    }

    public function test_is_published_false_for_null(): void
    {
        $post = new Post(['published_at' => null]);
        $this->assertFalse($post->isPublished());
    }

    public function test_is_published_false_for_future_date(): void
    {
        $post = new Post(['published_at' => now()->addDay()]);
        $this->assertFalse($post->isPublished());
    }

    public function test_is_published_true_for_past_date(): void
    {
        $post = new Post(['published_at' => now()->subDay()]);
        $this->assertTrue($post->isPublished());
    }

    public function test_scope_published_excludes_drafts(): void
    {
        Post::factory()->create(['published_at' => now()->subDay(), 'user_id' => 1]);
        Post::factory()->create(['published_at' => null, 'user_id' => 1]);
        Post::factory()->create(['published_at' => now()->addDay(), 'user_id' => 1]);

        $this->assertEquals(1, Post::published()->count());
    }
}
