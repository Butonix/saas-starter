<?php

use Faker\Generator as Faker;

$factory->define(config('tenancy.models.website'), function (Faker $faker) {
    return [
        'uuid' => $faker->uuid,
        'user_id' => function () {
            return factory(\App\Models\System\User::class)->create()->id;
        },
    ];
});
