<?php

namespace App\Jobs\System;

use App\Models\System\User;
use Illuminate\Bus\Queueable;
use App\Models\System\Website;
use App\Models\System\Hostname;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use App\Notifications\System\TenantCreated;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Hyn\Tenancy\Contracts\Repositories\WebsiteRepository;
use Hyn\Tenancy\Contracts\Repositories\HostnameRepository;

class CreateTenant implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $user;
    public $fqdn;

    public function __construct(User $user, $fqdn)
    {
        $this->user = $user;
        $this->fqdn = $fqdn;
    }

    public function handle()
    {
        $website = new Website;
        $website->user_id = $this->user->id;
        app(WebsiteRepository::class)->create($website);

        $hostname = new Hostname;
        $hostname->fqdn = $this->fqdn;
        app(HostnameRepository::class)->attach($hostname, $website);

        $this->user->notify(new TenantCreated(
            app('auth.password.broker')->createToken($this->user)
        ));
    }
}
