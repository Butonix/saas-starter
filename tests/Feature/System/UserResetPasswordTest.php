<?php

namespace Tests\Feature\System;

use Tests\SystemTest;
use App\Models\System\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Event;
use Illuminate\Auth\Events\PasswordReset;

class UserResetPasswordTest extends SystemTest
{
    public function setUp()
    {
        parent::setUp();

        Event::fake();

        $this->user = factory(User::class)->create([
            'email' => 'john@example.com',
            'password' => bcrypt('my-old-password'),
        ]);
        $this->validToken = app('auth.password.broker')->createToken($this->user);
        $this->invalidToken = 'invalid-token';
    }

    /** @test */
    function guests_can_view_the_reset_password_form()
    {
        $response = $this->get("/password/reset/{$this->validToken}");

        $response->assertStatus(200);
        $response->assertViewIs('auth.passwords.reset');
        $response->assertViewHas('token', $this->validToken);
    }

    /** @test */
    function logged_in_users_cannot_view_the_reset_password_form()
    {
        $response = $this->signIn($this->user)->get("/password/reset/{$this->validToken}");

        $response->assertStatus(302);
        $response->assertRedirect('/home');
    }

    /** @test */
    function logged_out_users_can_reset_their_password_with_a_valid_token()
    {
        $response = $this->post('/password/reset', [
            'token' => $this->validToken,
            'email' => 'john@example.com',
            'password' => 'my-new-password',
            'password_confirmation' => 'my-new-password',
        ]);

        tap($this->user->fresh(), function ($user) use ($response) {
            $response->assertStatus(302);
            $response->assertRedirect('/home');

            $this->assertTrue(Hash::check('my-new-password', $user->password));
            $this->assertTrue(auth()->check());
            $this->assertTrue(auth()->user()->is($user));

            Event::assertDispatched(PasswordReset::class, function ($e) use ($user) {
                return $e->user->is($user);
            });
        });
    }

    protected function assertValidationError($response, $token, $key)
    {
        tap($this->user->fresh(), function ($user) use ($response, $token, $key) {
            $response->assertStatus(302);
            $response->assertRedirect("/password/reset/{$token}");
            $response->assertSessionHasErrors($key);

            $this->assertTrue(Hash::check('my-old-password', $user->password));
            $this->assertFalse(auth()->check());

            Event::assertNotDispatched(PasswordReset::class, function ($e) use ($user) {
                return $e->user->is($user);
            });
        });
    }

    /** @test */
    function token_must_be_valid()
    {
        $response = $this->from("/password/reset/{$this->invalidToken}")
            ->post('/password/reset', [
                'token' => $this->invalidToken,
                'email' => 'john@example.com',
                'password' => 'my-new-password',
                'password_confirmation' => 'my-new-password',
            ]);

        $this->assertValidationError($response, $this->invalidToken, 'email');
    }

    /** @test */
    function token_is_required()
    {
        $response = $this->from("/password/reset/{$this->validToken}")
            ->post('/password/reset', [
                'token' => null,
                'email' => 'john@example.com',
                'password' => 'my-new-password',
                'password_confirmation' => 'my-new-password',
            ]);

        $this->assertValidationError($response, $this->validToken, 'token');
    }

    /** @test */
    function email_is_required()
    {
        $response = $this->from("/password/reset/{$this->validToken}")
            ->post('/password/reset', [
                'token' => $this->validToken,
                'email' => null,
                'password' => 'my-new-password',
                'password_confirmation' => 'my-new-password',
            ]);

        $this->assertValidationError($response, $this->validToken, 'email');
    }

    /** @test */
    function email_must_be_valid()
    {
        $response = $this->from("/password/reset/{$this->validToken}")
            ->post('/password/reset', [
                'token' => $this->validToken,
                'email' => 'invalid',
                'password' => 'my-new-password',
                'password_confirmation' => 'my-new-password',
            ]);

        $this->assertValidationError($response, $this->validToken, 'email');
    }

    /** @test */
    function password_is_required()
    {
        $response = $this->from("/password/reset/{$this->validToken}")
            ->post('/password/reset', [
                'token' => $this->validToken,
                'email' => 'john@example.com',
                'password' => null,
                'password_confirmation' => null,
            ]);

        $this->assertValidationError($response, $this->validToken, 'password');
        $this->assertTrue(session()->hasOldInput('email'));
    }

    /** @test */
    function password_must_be_at_least_6_characters()
    {
        $response = $this->from("/password/reset/{$this->validToken}")
            ->post('/password/reset', [
                'token' => $this->validToken,
                'email' => 'john@example.com',
                'password' => 'short',
                'password_confirmation' => 'short',
            ]);

        $this->assertValidationError($response, $this->validToken, 'password');
        $this->assertTrue(session()->hasOldInput('email'));
    }

    /** @test */
    function password_must_be_confirmed()
    {
        $response = $this->from("/password/reset/{$this->validToken}")
            ->post('/password/reset', [
                'token' => $this->validToken,
                'email' => 'john@example.com',
                'password' => 'my-new-password',
                'password_confirmation' => null,
            ]);

        $this->assertValidationError($response, $this->validToken, 'password');
        $this->assertTrue(session()->hasOldInput('email'));
    }
}
