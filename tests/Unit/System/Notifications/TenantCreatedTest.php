<?php

namespace Tests\Unit\System\Notifications;

use TenantFactory;
use Tests\SystemTest;
use App\Models\System\User;
use App\Notifications\System\TenantCreated;

class TenantCreatedTest extends SystemTest
{
    /** @test */
    function it_notifies_the_user_via_email()
    {
        $user = factory(User::class)->create();
        $token = app('auth.password.broker')->createToken($user);
        TenantFactory::createFor($user, 'foo');

        $data = (new TenantCreated($token))->toMail($user);

        $this->assertEquals("https://{$user->website->hostnames->first()->fqdn}/password/reset/{$token}", $data->actionUrl);
    }
}
