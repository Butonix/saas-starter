<?php

namespace App\Rules;

use App\Models\System\Hostname;
use Illuminate\Contracts\Validation\Rule;

class UniqueSubdomain implements Rule
{
    public function passes($attribute, $value)
    {
        if (Hostname::whereFqdn(tenant_fqdn($value))->exists()) {
            return false;
        }

        return true;
    }

    public function message()
    {
        return __('The :domain has already been taken.');
    }
}
