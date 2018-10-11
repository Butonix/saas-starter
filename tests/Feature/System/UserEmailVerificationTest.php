<?php

namespace Tests\Feature\System;

use Tests\SystemTest;
use App\Models\System\User;
use Illuminate\Support\Facades\Notification;
use Illuminate\Auth\Notifications\VerifyEmail;

class UserEmailVerificationTest extends SystemTest
{
    public function setUp()
    {
        parent::setUp();

        $this->user = factory(User::class)->create();
    }

    /** @test */
    function guests_cannot_view_the_verification_notice()
    {
        $response = $this->get('/email/verify');

        $response->assertStatus(302);
        $response->assertRedirect('/login');
    }

    /** @test */
    function verified_logged_in_users_cannot_view_the_verification_notice()
    {
        $response = $this->signIn(
            factory(User::class)->states('verified')->create()
        )->get('/email/verify');

        $response->assertStatus(302);
        $response->assertRedirect('/home');
    }

    /** @test */
    function unverified_logged_in_users_can_view_the_verification_notice()
    {
        $response = $this->signIn($this->user)->get('/email/verify');

        $response->assertStatus(200);
        $response->assertViewIs('auth.verify');
    }

    protected function verificationRouteFor($user)
    {
        return url()->signedRoute('verification.verify', ['id' => $user->id]);
    }

    /** @test */
    function guests_cannot_view_the_verification_verify_route()
    {
        $response = $this->get($this->verificationRouteFor($this->user));
        
        $response->assertStatus(302);
        $response->assertRedirect('/login');
    }

    /** @test */
    function logged_in_users_cannot_verify_other_users()
    {
        $otherUser = factory(User::class)->create([
            'email' => 'jane@example.com',
            'email_verified_at' => null,
        ]);

        $response = $this->signIn($otherUser)->get($this->verificationRouteFor($this->user));
        
        $response->assertStatus(302);
        $response->assertRedirect('/home');
    }

    /** @test */
    function verified_logged_in_users_cannot_verify_again()
    {
        $verifiedUser = factory(User::class)->states('verified')->create();

        $response = $this->signIn($verifiedUser)->get($this->verificationRouteFor($verifiedUser));
        
        $response->assertStatus(302);
        $response->assertRedirect('/home');
    }

    /** @test */
    function a_403_is_thrown_when_the_signature_is_invalid()
    {
        $invalidVerificationRoute = route('verification.verify', ['id' => $this->user->id]) . '?signature=invalid';

        $response = $this->signIn($this->user)->get($invalidVerificationRoute);

        $response->assertStatus(403);

        $this->assertNull($this->user->fresh()->email_verified_at);
    }

    /** @test */
    function unverified_logged_in_users_can_be_verified()
    {
        $response = $this->signIn($this->user)->get($this->verificationRouteFor($this->user));

        $response->assertStatus(302);
        $response->assertRedirect('/home');

        $this->assertNotNull($this->user->fresh()->email_verified_at);
    }

    /** @test */
    function guests_cannot_resend_a_verification_email()
    {
        $response = $this->get('/email/resend');

        $response->assertStatus(302);
        $response->assertRedirect('/login');
    }

    /** @test */
    function verified_users_cannot_resend_a_verification_email()
    {
        $verifiedUser = factory(User::class)->states('verified')->create();

        $response = $this->signIn($verifiedUser)->get('/email/resend');

        $response->assertStatus(302);
        $response->assertRedirect('/home');

        Notification::assertNotSentTo($verifiedUser, VerifyEmail::class);
    }

    /** @test */
    function unverified_logged_in_users_can_resend_a_verification_email()
    {
        $response = $this->signIn($this->user)
            ->from('/email/verify')
            ->get('/email/resend');

        $response->assertStatus(302);
        $response->assertRedirect('/email/verify');

        Notification::assertSentTo($this->user, VerifyEmail::class);
    }
}
