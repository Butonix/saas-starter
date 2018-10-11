<?php

namespace Tests\Unit\System;

use Tests\TestCase;
use App\Models\System\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class WebsiteTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    function a_website_belongs_to_a_user()
    {
        $website = factory(config('tenancy.models.website'))->create();
        
        $this->assertInstanceOf(User::class, $website->user);
    }
}
