<?php

namespace App\Exports;

use App\Models\WorkStatus;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class WorkstatusExport implements FromCollection, WithHeadings
{
    public function collection()
    {
        // return WorkStatus::all()
        // ->map(function($item) {
        //     return $item->toArray();
        // });
        return WorkStatus::with('attachments')->get()
        ->map(function($item){
            $data = $item->toArray();
           
            $fileUrls = $item->attachments
            ->map(fn($file) => asset('uploads/' . $file->file_name))
            ->implode(",");   
            $data['attachments'] = $fileUrls;
            return $data;
        });
    }

    
    public function headings(): array
    {
        // Take the first row and use its keys as headings
        // $first = WorkStatus::first();
        // return $first ? array_keys($first->toArray()) : [];

        $first = WorkStatus::with('attachments')->first();
            if (!$first) return[];

            $data = $first->toArray();
            $data['attachments'] = 'attachments';
            return array_keys($data);

    }
}
