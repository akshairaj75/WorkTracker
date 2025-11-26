<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Attachment extends Model
{
    protected $fillable = [
        'file_name',
        'work_status_id'
    ];

    public function workStatus()
    {
        return $this->belongsTo(WorkStatus::class);
    }

    protected static function booted()
    {
        static::deleting(function ($attachment) {

            // Build file path
            $path = public_path('uploads/' . $attachment->file_name);

            // Delete the file if exists
            if (file_exists($path)) {
                @unlink($path);
            }
        });
    }
}
