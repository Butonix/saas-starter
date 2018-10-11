<?php

namespace Tests\Unit\System\Middleware;

use TenantFactory;
use Tests\SystemTest;
use App\Jobs\CreateTenant;
use App\Models\System\User;
use Hyn\Tenancy\Environment;
use App\Models\System\Website;
use App\Http\Middleware\EnsureTenantExists;

class EnsureTenantExistsTest extends SystemTest
{
    /** @test */
    function it_redirects_to_the_home_page_if_the_tenant_does_not_exist()
    {
        $response = (new EnsureTenantExists)->handle(
            request()->create(tenant_fqdn('foo'), 'GET'),
            function () {}
        );

        $this->assertEquals($response->getStatusCode(), 302);
        $this->assertEquals($response->getTargetUrl(), config('app.url'));
    }

    /** @test */
    function it_handles_the_request_if_the_root_page_is_requested()
    {
        $response = (new EnsureTenantExists)->handle(
            request()->create('/', 'GET'),
            function () {}
        );

        $this->assertEquals($response->getTargetUrl(), config('app.url'));
    }

    /** @test */
    function it_handles_the_request_if_the_tenant_exists()
    {
        TenantFactory::create('unique');

        $response = (new EnsureTenantExists)->handle(
            request()->create(tenant_fqdn('unique'), 'GET'),
            function () {}
        );

        $this->assertNull($response);
    }
}
