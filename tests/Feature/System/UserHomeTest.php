<?php

namespace Tests\Feature\System;

use Tests\SystemTest;
use App\Models\System\User;

class UserHomeTest extends SystemTest
{    
    /** @test */
    function guests_cannot_view_the_user_home_page()
    {
        $response = $this->get('/home');

        $response->assertStatus(302);
        $response->assertRedirect('/login');
    }

    /** @test */
    function unverified_users_cannot_view_their_home_page()
    {
        $response = $this->signIn()->get('/home');

        $response->assertStatus(302);
        $response->assertRedirect('/email/verify');
    }

    /** @test */
    function not_set_up_users_cannot_view_their_home_page()
    {
        $response = $this->signIn(
            factory(User::class)
                ->states(['verified'])
                ->create()
        )->get('/home');

        $response->assertStatus(302);
        $response->assertRedirect('/setup');
    }

    /** @test */
    function set_up_users_can_view_the_user_home_page()
    {
        $response = $this->signIn(
            factory(User::class)
                ->states(['verified', 'set-up'])
                ->create()
        )->get('/home');

        $response->assertStatus(200);
        $response->assertViewIs('home');
    }
}
