<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Reference extends Model
{
    protected $table = 'buyer_token';
    public $pK = 'buyer_token_id';
    public $timestamps = false;
}
