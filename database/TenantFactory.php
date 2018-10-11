<?php

use App\Models\System\User;
use App\Jobs\System\CreateTenant;

class TenantFactory
{
    public static function createFor(User $user, $subdomain = 'foo')
    {
        $job = new CreateTenant($user, tenant_fqdn($subdomain));
        $job->handle();
    }

    public static function create($subdomain)
    {
        self::createFor(
            factory(User::class)->states('verified')->create(),
            $subdomain
        );
    }
}
