<?php

namespace App\Http\Controllers\System;

use Illuminate\Http\Request;
use App\Rules\ValidSubdomain;
use App\Rules\UniqueSubdomain;
use App\Jobs\System\CreateTenant;
use App\Http\Controllers\Controller;

class UsersController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'verified', 'new']);
    }

    public function showSetup()
    {
        return view('setup');
    }

    public function setup()
    {
        request()->validate([
            'subdomain' => ['required', new ValidSubdomain, new UniqueSubdomain],
        ]);

        CreateTenant::dispatch(
            auth()->user(),
            tenant_fqdn(request('subdomain'))
        );

        return redirect('/home');
    }
}
