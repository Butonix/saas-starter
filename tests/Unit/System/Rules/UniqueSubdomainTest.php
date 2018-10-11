<?php

namespace Tests\Unit\System\Rules;

use Tests\TestCase;
use App\Rules\UniqueSubdomain;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UniqueSubdomainTest extends TestCase
{
    use RefreshDatabase;

    public function setUp()
    {
        parent::setUp();

        $this->rule = new UniqueSubdomain;
    }

    /** @test */
    function new_subdomains_pass()
    {
        $this->assertTrue($this->rule->passes('subdomain', 'new'));
    }

    /** @test */
    function already_taken_subdomains_fail()
    {
        factory(config('tenancy.models.hostname'))->create([
            'fqdn' => tenant_fqdn('taken'),
        ]);

        $this->assertFalse($this->rule->passes('subdomain', 'taken'));
    }
}
