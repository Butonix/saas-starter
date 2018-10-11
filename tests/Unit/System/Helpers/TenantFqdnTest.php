<?php

namespace Tests\Unit\System\Helpers;

use Tests\TestCase;

class TenantFqdnTest extends TestCase
{
    /** @test */
    function it_returns_a_valid_fqdn()
    {
        $this->assertEquals("foo." . config('app.fqdn'), tenant_fqdn('foo'));
    }
}
