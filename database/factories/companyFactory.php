<?php
/*
|--------------------------------------------------------------------------
| Company Factory
|--------------------------------------------------------------------------
*/

$factory->define(App\Models\Company::class, function (Faker\Generator $faker) {
    return [
        'id' => '1',
		'name' => 'doloremque',
		'description' => '1',
		'industry' => '1',
		'subscription-tier' => '1',
		'number_of_staff' => '1',
		'active' => '1',
    ];
});
