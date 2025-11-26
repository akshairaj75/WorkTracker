<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WorkStatus extends Model
{
    protected $fillable = [
        'work_type',
        'description',
        'start_time',
        'end_time',
        'result',
        'date',
        'user_id',
        'days',
        'updates_area',
        'end_date'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function attachments(){
        return $this->hasMany(Attachment::class);
    }
    

    protected static function booted(){
        static::deleting(function ($work) {
            foreach ($work->attachments as $attachment) {
                $attachment->delete(); // triggers Attachment deletion + file removal
            }
        });
    }

    public function subtasks(){
        return $this->hasMany(Subtask::class);
    }
}



