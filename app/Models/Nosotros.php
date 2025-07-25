<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Nosotros extends Model
{
    protected $guarded = [];

    public function getImageAttribute($value)
    {
        return url("storage/" . $value);
    }

    public function getVideoAttribute($value)
    {
        return url("storage/" . $value);
    }
}
