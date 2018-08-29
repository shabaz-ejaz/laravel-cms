<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    public $table = "posts";

    public $primaryKey = "id";

    public $timestamps = true;

    public $fillable = [
		'id',
		'name',
		'author',

    ];

    public static $rules = [
        // create rules
    ];

    
	public function tags() {
		return $this->belongsToMany(\App\Models\Tag::class);
	}
	
}
