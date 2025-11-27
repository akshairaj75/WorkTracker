<?php

namespace App\Http\Controllers;

use App\Exports\WorkstatusExport;
use App\Imports\WorkStatusImport;
use App\Models\Activity;
use Illuminate\Http\Request;
use App\Models\WorkStatus;
use App\Models\User;
use App\Models\Attachment;
use App\Models\Subtask;
use Barryvdh\DomPDF\Facade\Pdf;
use Barryvdh\DomPDF\PDF as DomPDFPDF;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\File;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;

class WorkstatusController extends Controller
{
    //INDEX PAGE

    public function index(Request $request)
    {
        $user = Auth::User();
        $dateToday = Carbon::now()->format('Y-m-d');
        if ($user->is_admin) {
            $output = '';
            $datas = WorkStatus::all();
            return view('workstatus.index', compact('datas', 'user'));
        } else {
            $datas = WorkStatus::where('user_id', $user->id)->get();
            $progressData = Activity::where('user_id', $user->id)->get();
            $activityData = Activity::where('user_id', $user->id)->latest()->first();
            // dd($progressData);

            return view('workstatus.index', compact('datas', 'user', 'activityData', 'progressData', 'dateToday'));
        }
    }


    //USER BASED ACTIVITY PROGRESS
    public function progressArea(Request $request)
    {
        $user = Auth::user();
        $data = $request->validate([
            'date' => 'required',
            'from_time' => 'required',
            'to_time' => 'required',
            'description' => 'required',

        ]);
        $data['user_id'] = $user->id;
        $progressData = Activity::create($data);
        return redirect(route('work.index'))->with('success', 'Progress updated successfully');
    }

    public function getProgressArea()
    {
        $user = Auth::user();
        $datas = Activity::where('user_id', $user->id)->get();
        return response()->json($datas);
    }

    //TASK RELATED UPDATES; SEACH AND SELECTIONSCarbon::now()->format('Y-m-d');

    public function searchTasks(Request $request)
    {
        $user = Auth::user();
        $query = $request->input('query');
        $tasks = WorkStatus::where('user_id', $user->id)
            ->when($query, function ($q) use ($query) {
                $q->where('description', 'LIKE', "%{$query}%");
            })
            ->get(['id', 'description']); // only fetch what we need

        return response()->json($tasks);
    }



    public function updateAreaForm(Request $request)
    {
        $user = Auth::user();
        $request->validate([
            'task_id' => 'required|exists:work_statuses,id',
            'updates_area' => 'nullable|string',
        ]);
        $work = WorkStatus::findOrFail($request->task_id);

        if (!$user->is_admin && $work->user_id !== $user->id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        $work->updates_area = $request->updates_area;
        $work->save();

        return redirect()->route('work.index')->with('success', 'Work update saved successfully!');
    }


    public function getTaskDetails($id)
    {
        $task = WorkStatus::with('user')->find($id);

        if (!$task) {
            return response()->json(['error' => 'Task not found'], 404);
        }
        return response()->json([
            'id' => $task->id,
            'description' => $task->description,
            'updates_area' => $task->updates_area,
            'date' => $task->date,
            'work_type' => $task->work_type,
            'result' => $task->result,
        ]);
    }

    //CREATE STORE

    public function create()
    {
        $user = Auth::user();
        if ($user->is_admin) {
            $users = User::all();
            return view('workstatus.create', compact('users', 'user'));
        }
        return response('No access ..! <br>Only admin has access');
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        $data = $request->validate([
            'work_type'   => 'required',
            'description' => 'required',
            'result'  => 'required',
            'days'  => 'required',
            'date'  => 'required',
            'type_of_duration'  => 'required',
            'user_id' => 'nullable|exists:users,id', // for admin
            'attachments' => 'nullable|array',
            'attachments.*' => 'nullable|file|max:2048',

             // top-level subtasks
            'subtasks' => 'nullable|array',
            'subtasks.*.sub_work_type' => 'required_with:subtasks|string',
            'subtasks.*.sub_work_result' => 'required_with:subtasks|string',
            'subtasks.*.sub_work_description' => 'nullable|string',
            'subtasks.*.sub_work_date' => 'nullable|date',

            // nested subtasks under each top-level subtask
            'subtasks.*.nested' => 'nullable|array',
            'subtasks.*.nested.*.sub_work_type' => 'required_with:subtasks.*.nested|string',
            'subtasks.*.nested.*.sub_work_result' => 'required_with:subtasks.*.nested|string',
            'subtasks.*.nested.*.sub_work_description' => 'nullable|string',
            'subtasks.*.nested.*.sub_work_date' => 'nullable|date',

        ]);

        // ensure correct user_id before creating the WorkStatus
        if (!$user->is_admin) {
            $data['user_id'] = $user->id;
        } else {
            $data['user_id'] = $data['user_id'] ?? null;
        }


        $data['days'] .= " " . $data['type_of_duration'];

        $subtasksInput = $data['subtasks'] ?? [];
        unset($data['subtasks'], $data['type_of_duration']);

        $workstatus = WorkStatus::create($data);

        if (!empty($subtasksInput) && is_array($subtasksInput)) {
            foreach ($subtasksInput as $top) {
                $topData = [
                    'sub_work_type' => $top['sub_work_type'] ?? null,
                    'sub_work_result' => $top['sub_work_result'] ?? null,
                    'sub_work_description' => $top['sub_work_description'] ?? null,
                    'sub_work_date' => $top['sub_work_date'] ?? null,
                ];
                $parent = $workstatus->subtasks()->create($topData);

                if (!empty($top['nested']) && is_array($top['nested'])) {
                    foreach ($top['nested'] as $nested) {
                        $nestedData = [
                            'sub_work_type' => $nested['sub_work_type'] ?? null,
                            'sub_work_result' => $nested['sub_work_result'] ?? null,
                            'sub_work_description' => $nested['sub_work_description'] ?? null,
                            'sub_work_date' => $nested['sub_work_date'] ?? null,
                        ];
                        $parent->nestedsubtask()->create($nestedData);
                    }
                }
            }
        }

        // attachments
        if ($request->hasFile('attachments')) {
            $files = $request->file('attachments');
            foreach ($files as $file) {
                $newname = time() . '.' . uniqid() . '.' . $file->getClientOriginalExtension();
                $file->move(public_path('uploads'), $newname);
                $workstatus->attachments()->create(['file_name' => $newname]);
            }
        }

        if (!$user->is_admin) {
            $data['user_id'] = Auth::id();
        }
        return redirect()->route('work.index')->with('success', 'Work status created successfully!');;
    }


    // EDIT UPDATE  
    public function edit($id)
    {
        $user = Auth::user();
        $data = WorkStatus::findorfail($id);
        $is_editable = $user->is_admin ? '' : 'readonly';

        $activityStatuses = WorkStatus::with('subtasks.nestedsubtask')
        ->where('user_id', $user->id)->whereIn('result', ['pending', 'stuck'])->get();
        $now = Carbon::now();

        foreach ($activityStatuses as $activity) {

            $start = $activity->start_time ? Carbon::parse($activity->start_time) : $now;
            $end   = $activity->end_time   ? Carbon::parse($activity->end_time)   : $now;
            $startDate = Carbon::parse($activity->date);

            list($duration, $unit) = explode(" ", $activity->days);
            $duration = (int) $duration;

            if ($unit == 'hours') {
                $interval = ($start)->diff($end);

                $seconds = $start->diffInSeconds($end, false);
                $time_taken = floor(abs($seconds) / 3600);

                $activity->color = ($time_taken >= $duration) ? 'red' : 'rgb(72, 72, 75)';
                $activity->time_taken = $time_taken . " " . $unit;
            }
            //checking for start_date and end_date

            elseif ($unit == 'days') {
                $endDate = ($activity->end_date == null) ? $now : $activity->end_date;

                $time_taken = floor(($startDate)->diffInDays($endDate));

                $activity->color = ($time_taken > $duration) ? 'red' : 'rgb(72, 72, 75)';

                $activity->time_taken = $time_taken . " " . $unit;
            }
            
        }

        // dd($activityStatuses[8]->subtasks[0]->nestedsubtask[0]->sub_work_description);

        return view('workstatus.edit', compact('activityStatuses', 'user', 'data', 'now', 'is_editable'));
    }


    public function updateEdit(Request $request, $id){
        $data = WorkStatus::findorfail($id);
        $user = Auth::user();

        if ($user->is_admin) {
            $updatedata = $request->validate([
                'work_type'   => 'required',
                'description' => 'required',
                'result'      => 'required',
                'date'        => 'required',
                'end_date'    => 'nullable',
                'days'        => 'required',
                'type_of_duration' => 'required',
                'user_id'     => 'nullable|exists:users,id',
                'attachments' => 'nullable|array',
                'attachments.*' => 'nullable|file|max:2048'
            ]);

            $updatedata['days'] = ($updatedata['days'] ?? '') . ' ' . ($updatedata['type_of_duration'] ?? '');
        } else {
            $updatedata = $request->validate([
                'result'  => 'required',
                'start_time' => 'nullable',
                'end_time' => 'nullable',
                'end_date'   => 'nullable',
                'attachments' => 'nullable|array',
                'attachments.*' => 'nullable|file|max:2048'
            ]);
        }

        // handle attachments
        if ($request->hasFile('attachments')) {
            $files = $request->file('attachments');
            foreach ($files as $file) {
                $newname = time() . '.' . uniqid() . '.' . $file->getClientOriginalExtension();
                $file->move(public_path('uploads'), $newname);
                $data->attachments()->create(['file_name' => $newname]);
            }
        }

        if (isset($updatedata['result']) && $updatedata['result'] == 'solved') {
            $updatedata['end_date'] = now()->toDateString();
        } else {
            $updatedata['end_date'] = $updatedata['end_date'] ?? null;
        }

        // update work status
        $data->update($updatedata);

        // update subtasks and nested subtasks (only existing ones)
        if ($request->has('subtasks') && is_array($request->subtasks)) {
            foreach ($request->subtasks as $subtaskData) {
                if (empty($subtaskData['id'])) {
                    continue; // ignore creates on edit page
                }

                $subtask = Subtask::find($subtaskData['id']);
                if (! $subtask) {
                    continue;
                }

                $subtaskUpdate = [];
                if (isset($subtaskData['sub_work_result'])) {
                    $subtaskUpdate['sub_work_result'] = $subtaskData['sub_work_result'];
                }
                // optional description update (readonly in view, but include if present)
                if (isset($subtaskData['sub_work_description'])) {
                    $subtaskUpdate['sub_work_description'] = $subtaskData['sub_work_description'];
                }
                if (!empty($subtaskUpdate)) {
                    $subtask->update($subtaskUpdate);
                }

                // nested existing updates
                if (!empty($subtaskData['nested']) && is_array($subtaskData['nested'])) {
                    
                    foreach ($subtaskData['nested'] as $nestedData) {
                        if (empty($nestedData['id'])) {
                            continue; // ignore new nested items on edit
                        }
                        
                        $nested = $subtask->nestedsubtask()->find($nestedData['id']);
                        if (! $nested) continue;
 
                        $nestedUpdate = [];
                        if (isset($nestedData['sub_work_result'])) {
                            $nestedUpdate['sub_work_result'] = $nestedData['sub_work_result'];
                        }
                        if (isset($nestedData['sub_work_description'])) {
                            $nestedUpdate['sub_work_description'] = $nestedData['sub_work_description'];
                        }
                        if (isset($nestedData['sub_work_type'])) {
                            $nestedUpdate['sub_work_type'] = $nestedData['sub_work_type'];
                        }
                        if (isset($nestedData['sub_work_date'])) {
                            $nestedUpdate['sub_work_date'] = $nestedData['sub_work_date'];
                        }
                        if (!empty($nestedUpdate)) {
                            $nested->update($nestedUpdate);
                        }
                    }
                }
            }
        }
        return redirect()->route('work.index')->with('success', 'Work status updated successfully');
    }


    // public function attachmentDestroy($id){

    //     $attachment = Attachment::findOrFail($id);

    //     $filePath = public_path('uploads/' . $attachment->file_name);
    //     if (File::exists($filePath)) {
    //         File::delete($filePath);
    //     }
    //     $attachment->delete();

    //     return redirect()->back()->with('success', 'Attachment deleted successfully.');
    // }


    //ADMIN CONTROLLS
    public function adminManage(Request $request)
    {
        $currentuser = Auth::user();

        if (!$currentuser->is_admin) {
            abort(403, 'Only admin has access.');
        }
        $users = User::all();
        $now = Carbon::now();

        $query = WorkStatus::with('user');

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }
        if ($request->filled('work_type')) {
            $query->where('work_type', $request->work_type);
        }
        if ($request->filled('result')) {
            $query->where('result', $request->result);
        }

        $datas = $query->get();

        foreach ($datas as $data) {

            //checking for start_time and end_time
            $start = $data->start_time ? Carbon::parse($data->start_time) : $now;
            $end   = $data->end_time   ? Carbon::parse($data->end_time)   : $now;

            $startDate = Carbon::parse($data->date);

            list($duration, $unit) = explode(" ", $data->days);
            $duration = (int) $duration;
            
            if($data->updates_area == null ){
                $data->updates_area = 'No recent Updates';
            }
            if ($unit == 'hours') {
                $interval = ($start)->diff($end);

                $seconds = $start->diffInSeconds($end, false);
                $time_taken = floor(abs($seconds) / 3600);

                $color = ($time_taken >= $duration) ? 'red' : 'rgb(72, 72, 75)';
                if ($data->result == 'solved') {
                    $color = ($time_taken < $duration) ? 'rgba(58, 142, 83, 1)' : 'rgba(239, 187, 66, 1)';
                    $color = 'rgba(58, 142, 83, 1)';
                }
                $data->color = $color;
                $data->time_taken = $time_taken . " " . $unit;
            }
            //checking for start_date and end_date

            elseif ($unit == 'days') {
                $endDate = ($data->end_date == null) ? $now : $data->end_date;

                $time_taken = floor(($startDate)->diffInDays($endDate));

                $color = ($time_taken > $duration) ? 'red' : 'rgb(72, 72, 75)';

                if ($data->result == 'solved') {
                    $color = ($time_taken <= $duration) ? 'rgba(48, 121, 70, 1)' : 'rgba(230, 164, 12, 1)';
                }
                $data->color = $color;
                $data->time_taken = $time_taken . " " . $unit;
            }           
        }
        return view('auth.adminPanel', compact('datas', 'users', 'now'));
    }

    //EXPORT AND IMPORTS
    public function exportPdf()
    {
        $user = Auth::user();
        if ($user->is_admin) {
            $workDatas = WorkStatus::all();
            $pdf = Pdf::loadView('workstatus.pdfDatas', compact('workDatas'));
            return $pdf->download('pdf-file');
        }
        return redirect()->route('work.index');
    }

    //Export excel
    public function exportExcel()
    {
        $user = Auth::user();
        if ($user->is_admin) {
            return Excel::download(new WorkstatusExport, 'workDatas.xlsx');
        }
        return redirect()->route('work.index');
    }

    //Import excel
    public function importExcel(Request $request)
    {
        $user = Auth::user();
        if ($user->is_admin) {
            $request->validate([
                'file' => 'required|mimes:xlsx,xls,csv'
            ]);
            Excel::import(new WorkStatusImport, $request->file('file'));
            return redirect()->route('work.index')->with('success', 'Work statuses imported successfully!');
        }
        return redirect()->route('work.index');
    }
}
