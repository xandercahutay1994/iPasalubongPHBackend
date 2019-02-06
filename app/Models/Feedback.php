<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Feedback extends Model
{
    protected $table = 'feedback_rating';
    public $pK = 'feedback_rating_id';
    public $timestamps = false;
}
