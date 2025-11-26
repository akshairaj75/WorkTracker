<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Subtask extends Model
{
    protected $fillable = [
        'work_status_id',
        'sub_work_type',
        'sub_work_result',
        'sub_work_date',
        'sub_work_description',
        // 'nested_subtasks',
    ];

    public function workStatus()
    {
        return $this->belongsTo(WorkStatus::class);
    }

    public function nestedsubtask(){
        return $this->hasMany(Nestedsubtask::class, 'subtask_id');
    }

    // public function nested_subtask()
    // {
    //     return $this->hasMany(Nestedsubtask::class);
    // }
}
