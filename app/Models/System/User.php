<?php

namespace App\Models\System;

use App\Models\Shared\User as SharedUser;
use Hyn\Tenancy\Traits\UsesSystemConnection;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Relations\HasOne;

class User extends SharedUser implements MustVerifyEmail
{
    use UsesSystemConnection;
    
    protected $casts = [
        'set_up' => 'boolean'
    ];

    public function website(): HasOne
    {
        return $this->hasOne(config('tenancy.models.website'));
    }
}
