<?php

use App\Models\System\User;
use Faker\Generator as Faker;

$factory->define(User::class, function (Faker $faker) {
    return [
        'name' => $faker->name,
        'email' => $faker->unique()->safeEmail,
        'email_verified_at' => null,
        'password' => '$2y$10$TKh8H1.PfQx37YgCzwiKb.KjNyWgaHb9cbcoQgdIVFlYg7B77UdFm', // secret
        'remember_token' => str_random(10),
        'set_up' => false,
    ];
});

$factory->state(User::class, 'verified', function () {
    return [
        'email_verified_at' => now(),
    ];
});

$factory->state(User::class, 'set-up', function () {
    return [
        'set_up' => true,
    ];
});
