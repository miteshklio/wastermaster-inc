<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class City extends Model
{
    protected $table = 'cities';

    public $fillable = [
        'name', 'state_id'
    ];

    public $timestamps = false;
}
