<?php

namespace Tests;

use App\Models\System\User;
use App\Models\System\Website;
use App\Models\System\Hostname;
use Hyn\Tenancy\Database\Connection;
use Illuminate\Support\Facades\Notification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Hyn\Tenancy\Contracts\Repositories\WebsiteRepository;
use Hyn\Tenancy\Contracts\Repositories\HostnameRepository;

class SystemTest extends TestCase
{
    use RefreshDatabase;

    public function setUp()
    {
        parent::setUp();

        $this->artisan('migrate:fresh');

        Notification::fake();
    }

    public function tearDown()
    {
        $this->artisan('migrate:fresh');

        parent::tearDown();
    }

    protected function signIn($user = null)
    {
        return $this->actingAs(
            $user ?: factory(User::class)->create()
        );
    }
}
