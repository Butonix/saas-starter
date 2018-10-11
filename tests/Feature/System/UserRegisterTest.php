<?php

namespace Tests\Feature\System;

use Tests\SystemTest;
use App\Models\System\User;
use Illuminate\Support\Facades\Notification;
use Illuminate\Auth\Notifications\VerifyEmail;

class UserRegisterTest extends SystemTest
{
    /** @test */
    function guests_can_view_the_register_form()
    {
        $response = $this->get('/register');

        $response->assertStatus(200);
        $response->assertViewIs('auth.register');
    }

    /** @test */
    function logged_in_users_cannot_view_the_register_form()
    {
        $response = $this->signIn()->get('/register');

        $response->assertStatus(302);
        $response->assertRedirect('home');
    }

    protected function validParams($overrides = [])
    {
        return array_merge([
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            'terms' => true,
        ], $overrides);
    }

    /** @test */
    function creating_users()
    {
        $response = $this->post('/register', $this->validParams());

        tap(User::first(), function ($user) use ($response) {
            $response->assertStatus(302);
            $response->assertRedirect('/email/verify');

            $this->assertCount(1, User::all());
            $this->assertTrue(auth()->check());
            $this->assertTrue(auth()->user()->is($user));
            $this->assertEquals('John Doe', $user->name);
            $this->assertEquals('john@example.com', $user->email);

            Notification::assertSentTo($user, VerifyEmail::class);
        });
    }

    protected function assertValidationError($response, $key)
    {
        $response->assertStatus(302);
        $response->assertSessionHasErrors($key);
        $this->assertCount(0, User::all());
    }

    /** @test */
    function name_is_required()
    {
        $response = $this->post('/register', $this->validParams([
            'name' => null
        ]));

        $this->assertValidationError($response, 'name');
    }

    /** @test */
    function name_must_be_a_string()
    {
        $response = $this->post('/register', $this->validParams([
            'name' => 1
        ]));

        $this->assertValidationError($response, 'name');
    }

    /** @test */
    function name_cannot_be_longer_than_255_characters()
    {
        $response = $this->post('/register', $this->validParams([
            'name' => str_repeat('a', 256)
        ]));

        $this->assertValidationError($response, 'name');
    }

    /** @test */
    function email_is_required()
    {
        $response = $this->post('/register', $this->validParams([
            'email' => null
        ]));

        $this->assertValidationError($response, 'email');
    }

    /** @test */
    function email_must_be_a_string()
    {
        $response = $this->post('/register', $this->validParams([
            'email' => 1
        ]));

        $this->assertValidationError($response, 'email');
    }

    /** @test */
    function email_cannot_be_longer_than_255_characters()
    {
        $response = $this->post('/register', $this->validParams([
            'email' => str_repeat('a', 256)
        ]));

        $this->assertValidationError($response, 'email');
    }

    /** @test */
    function email_must_be_valid()
    {
        $response = $this->post('/register', $this->validParams([
            'email' => 'invalid'
        ]));

        $this->assertValidationError($response, 'email');
    }

    /** @test */
    function email_must_be_unique()
    {
        factory(User::class)->create([
            'email' => 'john@example.com'
        ]);

        $this->assertCount(1, User::all());

        $response = $this->post('/register', $this->validParams([
            'email' => 'john@example.com'
        ]));

        $response->assertStatus(302);
        $response->assertSessionHasErrors('email');
        $this->assertCount(1, User::all());
    }

    /** @test */
    function password_is_required()
    {
        $response = $this->post('/register', $this->validParams([
            'password' => null
        ]));

        $this->assertValidationError($response, 'password');
    }

    /** @test */
    function password_must_be_a_string()
    {
        $response = $this->post('/register', $this->validParams([
            'password' => 1
        ]));

        $this->assertValidationError($response, 'password');
    }

    /** @test */
    function password_must_have_at_least_6_characters()
    {
        $response = $this->post('/register', $this->validParams([
            'password' => str_repeat('a', 5)
        ]));

        $this->assertValidationError($response, 'password');
    }

    /** @test */
    function password_cannot_be_longer_than_255_characters()
    {
        $response = $this->post('/register', $this->validParams([
            'password' => str_repeat('a', 256)
        ]));

        $this->assertValidationError($response, 'password');
    }

    /** @test */
    function password_must_be_confirmed()
    {
        $response = $this->post('/register', $this->validParams([
            'password_confirmation' => null
        ]));

        $this->assertValidationError($response, 'password');
    }

    /** @test */
    function terms_is_required()
    {
        $response = $this->post('/register', $this->validParams([
            'terms' => null
        ]));

        $this->assertValidationError($response, 'terms');
    }

    /** @test */
    function terms_must_be_accepted()
    {
        $response = $this->post('/register', $this->validParams([
            'terms' => false
        ]));

        $this->assertValidationError($response, 'terms');
    }
}
