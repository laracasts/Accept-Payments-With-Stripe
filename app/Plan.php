<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Plan extends Model
{
    /**
     * Database columns that require casting.
     * 
     * @var array
     */
    protected $casts = [
        'price' => 'integer'
    ];

    /**
     * Fields to guard.
     * 
     * @var array
     */
    protected $guarded = [];
}
