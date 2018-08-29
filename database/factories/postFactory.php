<?php
/*
|--------------------------------------------------------------------------
| Post Factory
|--------------------------------------------------------------------------
*/

$factory->define(App\Models\Post::class, function (Faker\Generator $faker) {
    return [
        'id' => '1',
		'name' => 'sit',
		'author' => 'nisi',
    ];
});
