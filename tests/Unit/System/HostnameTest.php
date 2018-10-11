<?php

namespace Tests\Unit\System;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class HostnameTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    function it_belongs_to_a_website()
    {
        $hostname = factory(config('tenancy.models.hostname'))->create();

        $this->assertInstanceOf(config('tenancy.models.website'), $hostname->website);
    }
}
