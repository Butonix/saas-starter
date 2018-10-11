<?php

namespace App\Listeners;

use App\Models\Tenant\User;
use Hyn\Tenancy\Environment;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Hyn\Tenancy\Events\Websites\Created as WebsiteCreated;

class FinishUserSetup
{
    public function handle(WebsiteCreated $event)
    {
        app(Environment::class)->tenant($event->website);

        User::create([
            'name' => $event->website->user->name,
            'email' => $event->website->user->email,
            'password' => bcrypt(str_random(10)),
        ]);
        
        $event->website->user->forceFill([
            'set_up' => true
        ])->save();
    }
}
