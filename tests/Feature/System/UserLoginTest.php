<?php

namespace Tests\Feature\System;

use Tests\SystemTest;
use App\Models\System\User;

class UserLoginTest extends SystemTest
{
    /** @test */
    function guests_can_view_the_login_form()
    {
        $response = $this->get('/login');

        $response->assertStatus(200);
        $response->assertViewIs('auth.login');
    }

    /** @test */
    function logged_in_users_cannot_view_the_login_form()
    {
        $response = $this->signIn()->get('/login');

        $response->assertStatus(302);
        $response->assertRedirect('/home');
    }

    /** @test */
    function logging_in_with_valid_credentials()
    {
        $user = factory(User::class)->create([
            'email' => 'john@example.com',
            'password' => bcrypt('secret'),
        ]);

        $response = $this->post('/login', [
            'email' => 'john@example.com',
            'password' => 'secret',
        ]);

        $this->assertTrue(auth()->check());
        $this->assertTrue(auth()->user()->is($user));

        $response->assertStatus(302);
        $response->assertRedirect('/home');
    }

    /** @test */
    function users_get_remembered_if_wished()
    {
        $user = factory(User::class)->create([
            'email' => 'john@example.com',
            'password' => bcrypt('secret'),
            'remember_token' => null
        ]);

        $response = $this->post('/login', [
            'email' => 'john@example.com',
            'password' => 'secret',
            'remember' => true,
        ]);

        tap($user->fresh(), function ($user) use ($response) {
            $this->assertTrue(auth()->check());
            $this->assertTrue(auth()->user()->is($user));

            $response->assertStatus(302);
            $response->assertRedirect('/home');
            $response->assertCookie(auth()->guard()->getRecallerName(), vsprintf('%s|%s|%s', [
                $user->id,
                $user->getRememberToken(),
                $user->password,
            ]));
        });
    }

    protected function assertValidationError($response, $key)
    {
        $this->assertFalse(auth()->check());
        $response->assertStatus(302);
        $response->assertRedirect('/login');
        $response->assertSessionHasErrors($key);
    }

    /** @test */
    function logging_in_with_invalid_credentials()
    {
        $user = factory(User::class)->create([
            'email' => 'john@example.com',
            'password' => bcrypt('secret'),
        ]);

        $response = $this->from('/login')
            ->post('/login', [
                'email' => 'jane@example.com',
                'password' => 'incorrect-password',
            ]);

        $this->assertValidationError($response, 'email');
        $this->assertTrue(session()->hasOldInput('email'));
        $this->assertFalse(session()->hasOldInput('password'));
    }

    /** @test */
    function email_is_required()
    {
        $response = $this->from('/login')
            ->post('/login', [
                'password' => 'password',
            ]);

        $this->assertValidationError($response, 'email');
        $this->assertFalse(session()->hasOldInput('password'));
    }

    /** @test */
    function email_must_be_a_string()
    {
        $response = $this->from('/login')
            ->post('/login', [
                'email' => 1,
                'password' => 'password',
            ]);

        $this->assertValidationError($response, 'email');
        $this->assertTrue(session()->hasOldInput('email'));
        $this->assertFalse(session()->hasOldInput('password'));
    }

    /** @test */
    function password_is_required()
    {
        $response = $this->from('/login')
            ->post('/login', [
                'email' => 'john@example.com',
            ]);

        $this->assertValidationError($response, 'password');
        $this->assertTrue(session()->hasOldInput('email'));
    }

    /** @test */
    function password_must_be_a_string()
    {
        $response = $this->from('/login')
            ->post('/login', [
                'email' => 'john@example.com',
                'password' => 1,
            ]);

        $this->assertValidationError($response, 'password');
        $this->assertTrue(session()->hasOldInput('email'));
        $this->assertFalse(session()->hasOldInput('password'));
    }
}
