<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Copy extends Model
{
    protected $table = 'copy_delivery';
    public $pK = 'copy_delivery_id';
    public $timestamps = false;
}
