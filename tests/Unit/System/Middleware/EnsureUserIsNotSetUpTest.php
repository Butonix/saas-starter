<?php

namespace Tests\Unit\System\Middleware;

use Tests\SystemTest;
use App\Models\System\User;
use App\Http\Middleware\EnsureUserIsNotSetUp;

class EnsureUserIsNotSetUpTest extends SystemTest
{    
    protected function handleMiddleware()
    {
        return (new EnsureUserIsNotSetUp)->handle(
            request()->create('/setup', 'GET'),
            function () {}
        );
    }

    /** @test */
    function not_set_up_users_are_not_redirected()
    {
        $this->signIn(factory(User::class)->states('verified')->create());

        $response = $this->handleMiddleware();

        $this->assertNull($response);
    }

    /** @test */
    function set_up_users_are_redirected()
    {
        $this->signIn(factory(User::class)->states(['verified', 'set-up'])->create());
        
        $response = $this->handleMiddleware();

        $this->assertEquals($response->getStatusCode(), 302);
        $this->assertEquals($response->getTargetUrl(), url('/home'));
    }
}
