<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Nestedsubtask extends Model
{
    protected $table = 'nested_subtasks';
    protected $fillable = [
        'subtask_id',
        'sub_work_type',
        'sub_work_result',
        'sub_work_description',
        'sub_work_date',
    ];

    public function subtask()
    {
        return $this->belongsTo(Subtask::class);
    }
}
