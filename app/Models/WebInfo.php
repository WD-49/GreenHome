<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WebInfo extends Model
{
    protected $table = 'web_infos';

    protected $fillable = ['key', 'value'];

    public $timestamps = false;

    // Add any additional methods or relationships if needed
}
