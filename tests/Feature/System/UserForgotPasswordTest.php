<?php

namespace Tests\Feature\System;

use Tests\SystemTest;
use App\Models\System\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Auth\Notifications\ResetPassword;

class UserForgotPasswordTest extends SystemTest
{
    /** @test */
    function guests_can_view_the_password_reset_form()
    {
        $response = $this->get('/password/reset');

        $response->assertStatus(200);
        $response->assertViewIs('auth.passwords.email');
    }

    /** @test */
    function logged_in_users_cannot_view_the_password_reset_form()
    {
        $response = $this->signIn()->get('/password/reset');

        $response->assertStatus(302);
        $response->assertRedirect('/home');
    }

    /** @test */
    function users_receive_a_password_reset_notification_via_email()
    {
        $user = factory(User::class)->create([
            'email' => 'john@example.com'
        ]);

        $response = $this->post('/password/email', [
            'email' => 'john@example.com',
        ]);

        $resets = DB::table('password_resets')->get();
        $this->assertCount(1, $resets);

        Notification::assertSentTo($user, ResetPassword::class, function ($notification) use ($resets) {
            return Hash::check($notification->token, $resets->first()->token);
        });
    }

    protected function assertValidationError($response, $key)
    {
        $response->assertStatus(302);
        $response->assertRedirect('/password/reset');
        $response->assertSessionHasErrors($key);
    }

    /** @test */
    function unregistered_users_do_not_receive_a_password_reset_notification_via_email()
    {
        $response = $this->from('/password/reset')
            ->post('/password/email', [
                'email' => 'someone@example.com',
            ]);

        $this->assertValidationError($response, 'email');
        $this->assertTrue(session()->hasOldInput('email'));

        Notification::assertNotSentTo(factory(User::class)->make([
            'email' => 'someone@example.com',
        ]), ResetPassword::class);
    }

    /** @test */
    function email_is_required()
    {
        $response = $this->from('/password/reset')
            ->post('/password/email', []);

        $this->assertValidationError($response, 'email');
    }

    /** @test */
    function email_must_be_valid()
    {
        $response = $this->from('/password/reset')
            ->post('/password/email', [
                'email' => 'invalid',
            ]);

        $this->assertValidationError($response, 'email');
        $this->assertTrue(session()->hasOldInput('email'));
    }
}
