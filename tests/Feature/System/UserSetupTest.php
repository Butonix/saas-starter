<?php

namespace Tests\Feature\System;

use TenantFactory;
use Tests\SystemTest;
use App\Models\System\User;
use App\Models\System\Website;
use App\Models\System\Hostname;
use App\Jobs\System\CreateTenant;
use Illuminate\Support\Facades\Queue;

class UserSetupTest extends SystemTest
{
    public function setUp()
    {
        parent::setUp();

        Queue::fake();
    }

    /** @test */
    function guests_cannot_view_the_setup_page()
    {
        $response = $this->get('/setup');

        $response->assertStatus(302);
        $response->assertRedirect('/login');
    }

    /** @test */
    function unverified_users_cannot_view_the_setup_page()
    {
        $response = $this->signIn()->get('/setup');

        $response->assertStatus(302);
        $response->assertRedirect('/email/verify');
    }

    /** @test */
    function set_up_users_cannot_view_the_setup_page()
    {
        $response = $this->signIn(
            factory(User::class)
                ->states(['verified', 'set-up'])
                ->create()
        )->get('/setup');

        $response->assertStatus(302);
        $response->assertRedirect('/home');
    }

    /** @test */
    function new_verified_users_can_view_the_setup_page()
    {
        $response = $this->signIn(
            factory(User::class)->states('verified')->create()
        )->get('/setup');

        $response->assertStatus(200);
        $response->assertViewIs('setup');
    }

    protected function validParams($overrides = [])
    {
        return array_merge([
            'subdomain' => 'foo',
        ], $overrides);
    }

    /** @test */
    function guests_cannot_set_up_users()
    {
        $response = $this->post('/setup', $this->validParams());

        $response->assertStatus(302);
        $response->assertRedirect('/login');
        $this->assertTenantCount(0);
    }

    /** @test */
    function unverified_users_cannot_set_up_their_account()
    {
        $response = $this->signIn()->post('/setup', $this->validParams());

        $this->assertCount(1, User::all());

        $response->assertStatus(302);
        $response->assertRedirect('/email/verify');
        $this->assertTenantCount(0);
    }

    /** @test */
    function set_up_users_cannot_set_up_their_account_again()
    {
        $response = $this->signIn(
            factory(User::class)->states(['verified', 'set-up'])->create()
        )->post('/setup', $this->validParams());
        
        $this->assertCount(1, User::all());

        $response->assertStatus(302);
        $response->assertRedirect('/home');
    }

    protected function createTenant($overrides = [])
    {
        return [
            $user = factory(User::class)
                ->states('verified')
                ->create(),
            $this->signIn($user)
                ->from('/setup')
                ->post('/setup', $this->validParams($overrides)),
        ];
    }

    /** @test */
    function setting_up_users()
    {
        list($user, $response) = $this->createTenant([
            'subdomain' => 'bar'
        ]);

        tap($user->fresh(), function ($user) use ($response) {
            $response->assertStatus(302);
            $response->assertRedirect('/home');
            
            $this->assertTrue(auth()->check());
            $this->assertTrue(auth()->user()->is($user));

            Queue::assertPushed(CreateTenant::class, function ($job) use ($user) {
                return $job->user->is($user)
                    && $job->fqdn === tenant_fqdn('bar');
            });
        });
    }

    protected function assertSubdomainValidationError($response, $user)
    {
        $response->assertStatus(302);
        $response->assertRedirect('/setup');
        $response->assertSessionHasErrors('subdomain');

        $this->assertFalse($user->set_up);

        Queue::assertNotPushed(CreateTenant::class, function ($job) use ($user) {
            return $job->user->is($user);
        });
    }

    protected function assertTenantCount($count)
    {
        $this->assertCount($count, Website::all());
        $this->assertCount($count, Hostname::all());
    }

    /** @test */
    function subdomain_is_required()
    {
        list($user, $response) = $this->createTenant([
            'subdomain' => null,
        ]);

        $this->assertSubdomainValidationError($response, $user->fresh());
        $this->assertTenantCount(0);
    }

    /** @test */
    function subdomain_must_be_valid()
    {
        list($user, $response) = $this->createTenant([
            'subdomain' => 'ftp://.invalid?',
        ]);

        $this->assertSubdomainValidationError($response, $user->fresh());
        $this->assertTenantCount(0);
    }

    /** @test */
    function subdomain_must_be_unique()
    {
        TenantFactory::create('doe');

        $this->assertTenantCount(1);

        list($user, $response) = $this->createTenant([
            'subdomain' => 'doe',
        ]);

        $this->assertSubdomainValidationError($response, $user->fresh());
        $this->assertTenantCount(1);
    }
}
