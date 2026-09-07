<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Enums\UserRole;
use App\Models\Post;
use App\Models\User;
use App\Policies\PostPolicy;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PostPolicyTest extends TestCase
{
    use RefreshDatabase;

    private PostPolicy $policy;

    protected function setUp(): void
    {
        parent::setUp();
        $this->policy = new PostPolicy;
    }

    public function test_owner_can_update_own_post(): void
    {
        $user = User::factory()->create();
        $post = Post::factory()->for($user)->create();

        $this->assertTrue($this->policy->update($user, $post));
    }

    public function test_other_user_cannot_update_post(): void
    {
        $owner = User::factory()->create();
        $other = User::factory()->create();
        $post = Post::factory()->for($owner)->create();

        $this->assertFalse($this->policy->update($other, $post));
    }

    public function test_owner_can_delete_own_post(): void
    {
        $user = User::factory()->create();
        $post = Post::factory()->for($user)->create();

        $this->assertTrue($this->policy->delete($user, $post));
    }

    public function test_admin_can_delete_any_post(): void
    {
        $admin = User::factory()->create(['role' => UserRole::ADMIN]);
        $post = Post::factory()->create();

        $this->assertTrue($this->policy->delete($admin, $post));
    }

    public function test_regular_user_cannot_delete_others_post(): void
    {
        $user = User::factory()->create(['role' => UserRole::USER]);
        $post = Post::factory()->create();

        $this->assertFalse($this->policy->delete($user, $post));
    }
}
