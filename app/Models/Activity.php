<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Activity extends Model
{
    protected $fillable = [
        'from_time',
        'to_time',
        'description',
        'date',
        'user_id',
    ];
    

    public function activity(){

        return $this->belongsTo(User::class);
    }


}
