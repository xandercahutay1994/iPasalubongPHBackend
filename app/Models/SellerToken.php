<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SellerToken extends Model
{
    protected $table = 'token';
    public $pK = 'id';
    public $timestamps = false;
}
