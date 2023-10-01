<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UserModelTest extends TestCase
{
    use RefreshDatabase;

    public function test_moderator_method()
    {
        $user = User::factory()->moderator()->create();

        $this->assertTrue($user->isModerator());
    }

    public function test_moderator_method_for_user()
    {
        $user = User::factory()->create();

        $this->assertFalse($user->isModerator());
    }
}
