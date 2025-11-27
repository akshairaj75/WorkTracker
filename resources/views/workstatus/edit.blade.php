@extends('layouts.mainlayout')

@section('extra-css')
<link rel="stylesheet" href="{{ asset('css/edit.css') }}">
@endsection

@section('content')
<div class="edit-container">
    <div class="dataform">
        @if (!$user->is_admin)
            <div class="data-table">
                <div class="work-status-title">
                    <h3>Work Statuses : {{$user->username}} </h3><br>
                </div>
                <table>
                    <thead>
                        <tr>
                            <th>Task ID</th>
                            <th>Work Type</th>
                            <th>Description</th>
                            <th>Date</th>
                            <th>Status</th>
                            <th>Assigned days</th>
                            <th>Time taken</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if (!empty($activityStatuses) && $activityStatuses->count() > 0)
                            @foreach($activityStatuses as $activity)
                                <tr onclick="openModal(
                                        '{{ $activity->id }}',
                                        '{{ $activity->user->username }}',
                                        '{{ $activity->work_type }}',
                                        '{{ $activity->description }}',
                                        '{{ $activity->date }}',
                                        '{{ $activity->result }}',
                                        '{{ $activity->color }}',)" style="cursor:pointer;">

                                        <td style="color: {{$activity->color}}">{{ $activity->id }}</td>
                                        <td style="color: {{$activity->color}}">{{ $activity->work_type }}</td>
                                        <td style="color: {{$activity->color}}">{{ $activity->description }}</td>
                                        <td style="color: {{$activity->color}}">{{ $activity->date }}</td>
                                        <td style="color: {{$activity->color}}">{{ $activity->result }}</td>
                                        <td style="color: {{$activity->color}}">{{ $activity->days }}</td>
                                        <td style="color: {{$activity->color}}">{{ $activity->time_taken }} </td>
                                </tr>
                            @endforeach

                            <div class="modal" id="rowModal" style="display:none;">
                                <div class="modal-box">
                                    <div class="ribbon" id="m_ribbon"></div>
                                    <span class="close-btn" onclick="closeModal()">&times;</span>
                                    <div class="detail-card">
                                        <h3>Task Details</h3>
                                        <div class="card-grid">
                                            <div class="card-item">
                                                <span>Id:</span>
                                                <p id="m_id"></p>
                                            </div>
                                            <div class="card-item">
                                                <span>User:</span>
                                                <p id="m_user"></p>
                                            </div>
                                            <div class="card-item">
                                                <span>Work Type:</span>
                                                <p id="m_work"></p>
                                            </div>
                                            <div class="card-item">
                                                <span>Date:</span>
                                                <p id="m_date"></p>
                                            </div>
                                            <div class="card-item wide">
                                                <span>Description:</span>
                                                <p id="m_desc"></p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @else
                            <tr>
                                <td colspan="7" style="text-align: center">
                                    <h3 style="font-weight: 600; font-size: small">No pending records to show</h3>
                                </td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        @endif

        <form action="{{ route('work.updateEdit', $data->id) }}" enctype="multipart/form-data" method="post" id="editStatusForm">
            @method('put')
            @csrf

            {{-- ensure admin-required values post even if readonly --}}
            @php
                // if days stored like "3 days" we split; fallback to 'days'
                $parts = is_string($data->days) ? explode(' ', trim($data->days), 2) : [];
                $type_of_duration = $parts[1] ?? 'days';
                $days_only = $parts[0] ?? $data->days;
            @endphp
            <input type="hidden" name="days" value="{{ $days_only }}">
            <input type="hidden" name="type_of_duration" value="{{ $type_of_duration }}">

            <div class="updates-title" style="justify-self: center">
                <h1>Status Update <span style="color: rgb(54, 52, 52)">{{ $data->id }}</span></h1>
            </div>

            <div class="form-container">
                <div class="form-section-container">
                    <div class="form-section">
                        <div class="form-items">
                            <label for="work_type">Work type :</label>
                            @if ($user->is_admin)
                                <select name="work_type" id="work_type" >
                                    <option value="task" @selected($data->work_type == 'task')>Task</option>
                                    <option value="testing" @selected($data->work_type == 'testing')>Testing</option>
                                    <option value="error" @selected($data->work_type == 'error')>Error</option>
                                    <option value="learning" @selected($data->work_type == 'learning')>Learning</option>
                                </select>
                            @else
                                <input type="text" name="work_type" id="work_type" value="{{ $data->work_type }}" {{$is_editable}}>
                            @endif
                        </div>

                        <div class="form-items">
                            <label for="description">Description :</label>
                            <input type="text" name="description" id="description" value="{{ $data->description }}" {{$is_editable}}>
                        </div>

                        <div class="form-items">
                            <label for="date">Date :</label>
                            <input type="date" name="date" id="date" value="{{ $data->date }}" {{$is_editable}}>
                        </div>

                        <div class="form-items" style="display: flex; flex-direction: column;">
                            <label for="days">Duration :</label>
                            <div class="duration" style="display: flex; gap:10px;">
                                <input type="text" value="{{ $data->days }}" readonly>
                            </div>
                        </div>
                    </div>

                    <div class="form-section">
                        <div class="form-items">
                            <label for="start_time">Start time :</label>
                            <input type="time" name="start_time" id="start_time" value="{{ $data->start_time }}" >
                        </div>

                        <div class="form-items">
                            <label for="end_time">End Time :</label>
                            <input type="time" id="end_time" name="end_time" value="{{ $data->end_time }}" >
                        </div>

                        <div class="form-items">
                            <label for="result">Overall Status :</label>
                            <select name="result" id="result">
                                <option value="stuck" @selected($data->result == 'stuck')>Stuck</option>
                                <option value="solved" @selected($data->result == 'solved')>Solved</option>
                                <option value="pending" @selected($data->result == 'pending')>Pending</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="file-section">
                    <div class="form-items">
                        <label for="attachments">Add attachment (optional):</label>
                        <input type="file" name="attachments[]" id="attachments" multiple>
                    </div>

                    <h4>Current files :</h4>
                    <div class="files_container">
                        @if ($data->attachments && $data->attachments->count() > 0)
                            @foreach($data->attachments as $file)
                                @php
                                    $path = asset('uploads/' . $file->file_name);
                                    $ext = strtolower(pathinfo($file->file_name, PATHINFO_EXTENSION));
                                    $isImage = in_array($ext, ['jpg','jpeg','png','gif','webp']);
                                @endphp

                                <div class="attachment-item" style="margin:5px;">
                                    <a href="{{ $path }}" target="_blank" style="text-decoration:none;">
                                        @if($isImage)
                                            <img src="{{ $path }}" alt="image" style="max-width:80px; max-height:80px;">
                                        @elseif($ext === 'pdf')
                                            <i class="fa regular fa-file-pdf" style="color:rgb(154, 35, 35);"></i>
                                        @else
                                            <i class="fa-regular fa-file" style=" color:rgb(27, 77, 28);"></i>
                                        @endif
                                    </a>
                                </div>
                            @endforeach
                        @else
                            No files.
                        @endif
                    </div>
                </div>

                                    
                <div class="task_section">
                    <h2>Subtasks :</h2>
                    <div id="subtaskContainer">
                        @foreach ($data->subtasks as $sIndex => $subtask)
                        <div class="subtask-box">
                            <div class="subtask-header" onclick="toggleDropdown(this)">
                                Subtask {{ $sIndex + 1 }} - {{ $subtask->sub_work_type }}
                                <span class="arrow">▼</span>
                            </div>
                            <div class="subtask-body">
                                <input type="hidden" name="subtasks[{{ $sIndex }}][id]" value="{{ $subtask->id }}">
                                <div class="form-items">
                                    <label>Work Type:</label>
                                    <select name="subtasks[{{ $sIndex }}][sub_work_type]" {{$is_editable}} >
                                        <option value="task" @selected($subtask->sub_work_type == 'task')>Task</option>
                                        <option value="testing" @selected($subtask->sub_work_type == 'testing')>Testing</option>
                                        <option value="error" @selected($subtask->sub_work_type == 'error')>Error</option>
                                        <option value="learning" @selected($subtask->sub_work_type == 'learning')>Learning</option>
                                    </select>
                                </div>

                                <div class="form-items">
                                    <label>Description:</label>
                                    <textarea name="subtasks[{{ $sIndex }}][sub_work_description]" {{$is_editable}}>{{ $subtask->sub_work_description }}</textarea>
                                </div>
                                <div class="form-items">
                                    <label>Date:</label>
                                    <input type="date" value="{{ $subtask->sub_work_date }}" name="subtasks[{{ $sIndex }}][sub_work_date]" {{$is_editable}}>
                                </div>
                                <div class="form-items">
                                    <label>Result:</label>
                                <select name="subtasks[{{ $sIndex }}][sub_work_result]" id="result" >
                                    <option value="stuck" @selected($subtask->sub_work_result == 'stuck')>Stuck</option>
                                    <option value="solved" @selected($subtask->sub_work_result == 'solved')>Solved</option>
                                    <option value="pending" @selected($subtask->sub_work_result == 'pending')>Pending</option>
                                </select>
                                </div>

                                <!-- Nested subtasks -->
                                @if($subtask->nestedsubtask->isNotEmpty())
                                <div class="nested-list">
                                    @foreach($subtask->nestedsubtask as $nIndex => $nested)
                                    <div class="nested-box">
                                        <div class="nested-header" onclick="toggleDropdown(this)">
                                            Additional {{ $nIndex + 1 }} - {{ $nested->sub_work_type }}
                                            <span class="arrow">▼</span>
                                        </div>
                                        <div class="nested-body">
                                            <input type="hidden" name="subtasks[{{ $sIndex }}][nested][{{ $nIndex }}][id]" value="{{ $nested->id }}" {{$is_editable}}>
                                            <div class="form-items">
                                                <label>Nested Type:</label>
                                                <select name="subtasks[{{ $sIndex }}][nested][{{ $nIndex }}][sub_work_type]"  {{$is_editable}}>
                                                    <option value="task" @selected($nested->sub_work_type == 'task')>Task</option>
                                                    <option value="testing" @selected($nested->sub_work_type == 'testing')>Testing</option>
                                                    <option value="error" @selected($nested->sub_work_type  == 'error')>Error</option>
                                                    <option value="learning" @selected($nested->sub_work_type  == 'learning')>Learning</option>
                                                </select>
                                            </div>
                                            <div class="form-items">
                                                <label>Nested Description:</label> 
                                                <textarea name="subtasks[{{ $sIndex }}][nested][{{ $nIndex }}][sub_work_description]" {{$is_editable}}>{{ $nested->sub_work_description }}</textarea>
                                            </div>

                                            <div class="form-items">
                                                <label>Date:</label>
                                                <input type="date" value="{{ $nested->sub_work_date }}" name="subtasks[{{ $sIndex }}][nested][{{ $nIndex }}][sub_work_date]" {{$is_editable}}>
                                            </div>
                                            <div class="form-items">
                                                <label>Result:</label>
                                                <select name="subtasks[{{ $sIndex }}][nested][{{ $nIndex }}][sub_work_result]" id="result">
                                                    <option value="stuck" @selected($nested->sub_work_result == 'stuck')>Stuck</option>
                                                    <option value="solved" @selected($nested->sub_work_result == 'solved')>Solved</option>
                                                    <option value="pending" @selected($nested->sub_work_result == 'pending')>Pending</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                                @endif

                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
            <div style="margin-top: 10px">
                <button type="submit" id="saveBtn">Save changes</button> 
            </div>
        </form>
    </div>
</div>

<script>


function openModal(id, user, work, desc, date, result, color ) {
    document.getElementById('m_id').textContent = id;
    document.getElementById('m_user').textContent = user;
    document.getElementById('m_work').textContent = work;
    document.getElementById('m_desc').textContent = desc;
    document.getElementById('m_date').textContent = date;
    

    const ribbon = document.getElementById('m_ribbon');
    ribbon.textContent = result;
    if (ribbon) ribbon.style.background = color || '#666';
    document.getElementById('rowModal').style.display = "flex";
}

function closeModal() {
    document.getElementById('rowModal').style.display = "none";
}

window.onclick = function(e) {
    if (e.target.id === "rowModal") {
        closeModal();
    }
};

document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('editStatusForm');
    const saveBtn = document.getElementById('saveBtn');
    if (!form) return;

    // disable save until a change is made (optional)
    // saveBtn.disabled = true;

    // all selects that are editable
    const editableSelects = Array.from(form.querySelectorAll('select[name^="subtasks"], select[name="result"], select[name="work_type"]'));

    editableSelects.forEach(sel => {
        sel.addEventListener('change', function () {
            const box = sel.closest('.subtask-box') || sel.closest('.nested-box');
            if (box) box.classList.add('changed');
            saveBtn.disabled = false;
        });
    });

    form.addEventListener('submit', function (e) {
        if (saveBtn.disabled)  {
            e.preventDefault();
            return;
        }
        if (!confirm('Save updated statuses?')) {
            e.preventDefault();
        }
    });
});




function toggleDropdown(header) {
    const body = header.nextElementSibling;
    if (!body) return;

    const isOpen = body.style.display === "block";

    if (isOpen) {
        body.style.display = "none";
    } else {
        body.style.display = "block";
    }

    // toggle active class for arrow rotation
    header.classList.toggle('active', !isOpen);
}


</script>
@endsection