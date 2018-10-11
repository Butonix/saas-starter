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

        $this->cleanUpTenancy();

        Notification::fake();
    }

    public function tearDown()
    {
        $this->cleanUpTenancy();

        parent::tearDown();
    }

    protected function cleanUpTenancy()
    {
        config(['database.default' => 'system']);

        Hostname::all()->each(function ($hostname) {
            app(HostnameRepository::class)->delete($hostname);
        });

        Website::all()->each(function ($website) {
            app(WebsiteRepository::class)->delete($website);
        });

        app(Connection::class)->system()->rollback();

        $this->artisan('migrate:fresh');
    }

    protected function signIn($user = null)
    {
        return $this->actingAs(
            $user ?: factory(User::class)->create()
        );
    }
}
