<?php

use Faker\Generator as Faker;

$factory->define(config('tenancy.models.hostname'), function (Faker $faker) {
    return [
        'fqdn' => "{$faker->domainWord}." . config('app.fqdn'),
        'website_id' => function () {
            return factory(config('tenancy.models.website'))->create()->id;
        },
    ];
});
