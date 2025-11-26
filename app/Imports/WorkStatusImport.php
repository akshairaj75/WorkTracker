<?php

namespace App\Imports;

use App\Models\WorkStatus;
use App\Models\Attachment;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class WorkStatusImport implements ToModel, WithHeadingRow
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        // return new WorkStatus([      
            // 'user_id' => $row['user_id'],            
            // 'work_type' => $row['work_type'],            
            // 'description' => $row[ 'description'],            
            // 'start_time' => $row['start_time'],            
            // 'end_time' => $row['end_time'],            
            // 'days' => $row['days'],            
            // 'result' => $row['result'],            
            // 'date' => $row['date'],
            
        // ]);


         $workStatus = WorkStatus::updateOrCreate(
            ['id' => $row['id']], 
            [
                'user_id' => $row['user_id'],            
                'work_type' => $row['work_type'],            
                'description' => $row[ 'description'],            
                'start_time' => $row['start_time'],            
                'end_time' => $row['end_time'],            
                'days' => $row['days'],            
                'result' => $row['result'],            
                'date' => $row['date'],
            ]
        );

         if (!empty($row['attachments'])) {
            $attachments = preg_split("/\r\n|\n|,/", $row['attachments']);

            $attachments = explode(',', $row['attachments']);

            foreach ($attachments as $fileUrl) {
                $fileUrl = trim($fileUrl);
                if (!$fileUrl) {
                    continue;
                }
                $fileName = basename(parse_url($fileUrl, PHP_URL_PATH));
                Attachment::updateOrCreate(
                    [
                        'work_status_id' => $workStatus->id,
                        'file_name'      => $fileName,
                    ]
                );
            }
        }

        return $workStatus;



    }
}
