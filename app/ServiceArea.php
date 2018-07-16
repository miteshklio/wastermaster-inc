<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * Represents a single Service Area for haulers.
 * A Service Area is a loose term that is not specific to
 * any cities/towns/etc, but still represents the use case.
 *
 * @package App
 */
class ServiceArea extends Model
{
    protected $table = 'service_area';

    public $fillable = ['name', 'display_name'];

    public $timestamps = false;
}
