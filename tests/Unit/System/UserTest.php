<?php

namespace Tests\Unit\System;

use Tests\TestCase;
use App\Models\System\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    public function setUp()
    {
        parent::setUp();

        $this->user = factory(User::class)->create();

        factory(config('tenancy.models.website'))->create([
            'user_id' => $this->user->id,
        ]);
    }

    /** @test */
    function a_user_has_one_website()
    {
        $this->assertInstanceOf(config('tenancy.models.website'), $this->user->website);
    }

    /** @test */
    function it_casts_set_up_to_boolean()
    {
        $this->assertInternalType('boolean', $this->user->set_up);
    }
}
