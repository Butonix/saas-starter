<?php

namespace Tests\Unit\System\Jobs;

use Tests\SystemTest;
use App\Models\System\Website;
use App\Models\System\Hostname;
use App\Jobs\System\CreateTenant;
use App\Models\System\User as SystemUser;
use App\Models\Tenant\User as TenantUser;
use App\Notifications\System\TenantCreated;
use Illuminate\Support\Facades\Notification;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CreateTenantTest extends SystemTest
{
    /** @test */
    function it_creates_the_tenant_and_sends_a_notification_to_the_user()
    {
        $user = factory(SystemUser::class)->states('verified')->create();
        $token = app('auth.password.broker')->createToken($user);

        CreateTenant::dispatch($user, tenant_fqdn('foo'));

        $this->assertTrue($user->fresh()->set_up);

        $this->assertCount(1, Website::all());
        $this->assertCount(1, Hostname::all());
        $this->assertCount(1, TenantUser::all());

        tap(TenantUser::first(), function ($tenantUser) use ($user) {
            $this->assertEquals($user->name, $tenantUser->name);
            $this->assertEquals($user->email, $tenantUser->email);
        });

        Notification::assertSentTo($user, TenantCreated::class);
    }
}
