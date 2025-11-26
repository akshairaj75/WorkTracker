@extends('layouts.mainlayout')

@section('extra-css')
<link rel="stylesheet" href="{{ asset('css/index.css') }}">
@endsection


@section('content')
<h2 style="color: rgb(30, 30, 31)">Welcome <span style="color: var(--primary-color)"> {{ auth()->user()->username
        }}</span></h2>


@if (@session('success'))
    <div style="color: green" class="add-success">
        Creation success
    </div>
@endif


<div class="task-manage">
    @if ($user->is_admin)
    <div class="admin-dashboard" style="padding: 25px 30px;">
        <div class="admin-button">
            <a id="create" href="{{route('work.create')}}">Create new</a>
            <a id="create" href="{{route('work.adminManage')}}">Admin panel</a>
        </div>

        <div class="user-button">
            <div class="exports">
                <a id="create" href="{{route('work.exportPdf')}}">Export as .pdf</a>
                <a id="create" href="{{route('work.exportExcel')}}">Export Excel file</a>
            </div>
            <form class="import-form" action="{{ route('workstatus.import') }}" method="POST"
                enctype="multipart/form-data">
                @csrf
                <input type="file" name="file" required>
                <button type="submit">Import Excel</button>
            </form>
        </div>
    </div>
    @else
    <div class="admin-dashboard" style="padding: 15px 20px;">
        <h3 style="text-align: center">Dashboard</h3>
    </div>
    @endif

    <table>
        <tr>
            <th>ID</th>
            <th>Date</th>
            <th>Type</th>
            <th>Assigned user</th>
            <th>Description</th>
            <th>Status</th>
            <th>Attatchments</th>
            <th>    </th>
        </tr>
        @if ($datas->count() > 0)
        @foreach($datas as $index => $data)
        <tr>
            <td>{{$index +1}} </td>
            <td>{{$data->date}}</td>
            <td>{{$data->work_type}}</td>
            <td>{{$data->user->username}} </td>
            <td>{{$data->description}}</td>
            <td>{{$data->result }}</td>
            <td class="attachments-cell">
                <div class="attachments-scroll">

                    @foreach($data->attachments as $file)
                    @php
                    $path = asset('uploads/' . $file->file_name);
                    $ext = strtolower(pathinfo($file->file_name, PATHINFO_EXTENSION));
                    $isImage = in_array($ext, ['jpg','jpeg','png','gif','webp']);
                    @endphp

                    <div class="attachment-item" style="margin:5px;">
                        <a href="{{ $path }}" target="_blank" style="text-decoration:none;">
                            @if($isImage)
                            <img src="{{ $path }}" alt="image">
                            @elseif($ext === 'pdf')
                            <i class="fa regular fa-file-pdf" style="color:rgb(154, 35, 35);"></i>
                            @else
                            <i class="fa-regular fa-file" style=" color:rgb(27, 77, 28);"></i>
                            @endif
                        </a>
                    </div>
                    @endforeach

                </div>
            </td>
            @if ($user->is_admin)
            <td>
                <a id="update" href="{{route('work.edit',$data->id)}}">Update</a>
            </td>
            @else
            <td>
                <a id="update" href="{{route('work.edit',$data->id)}}">View</a>
            </td>
            @endif
        </tr>
        @endforeach
        @else
        <tr>
            <td colspan="9" style="text-align: center">
                <h3>No records to show</h3>
            </td>
        </tr>
        @endif
    </table>


    @if (!$user->is_admin)

    <div class="updation-forms">

        <h1>Updation area</h1>

        <div class="activity-progress-container" >
            <form action="{{route('work.progressArea')}}" class="activity-form-container" method="POST">
                @csrf
                @if ($activityData)
                <div class="activity-form" style="display: flex">
                    <div class="form-items">
                        <label for="act_date">Date :</label>
                        <input type="date" name="date" value="{{$activityData->date}}">
                    </div>

                    <div class="form-items">
                        <label for="from-time">From :</label>
                        <input type="time" name="from_time" value="{{$activityData->from_time}}">
                    </div>

                    <div class="form-items">
                        <label for="to-time">To :</label>
                        <input type="time" name="to_time" value="{{$activityData->to_time}}">
                    </div>
                </div>
                @else
                    <div class="activity-form" style="display: flex">
                        <div class="form-items">
                            <label for="act_date">Date :</label>
                            <input type="date" name="date" value="{{$dateToday}}" required>
                        </div>

                        <div class="form-items">
                            <label for="from-time">From :</label>
                            <input type="time" name="from_time" value="" required>
                        </div>

                        <div class="form-items">
                            <label for="to-time">To :</label>
                            <input type="time" name="to_time" value="" required>
                        </div>
                    </div>
                @endif
               
                
                <div class="activity-interactions" style="display: flex">
                    <div class="activity-text-area">
                            <textarea name="description" id="activity-text-area" rows="7" style="padding:20px;"
                            placeholder="Write updates here...">
                            </textarea>
                            <input type="submit">
                    </div>

                    <div class="progress-details" >
                        <h5 class="recentUpdates" >Recent updates : </h5>

                        @forelse($progressData ?? [] as $data)
                            <p><strong>{{$data->updated_at}} :</strong><br> <span>{{$data->description}}</span><hr></p>
                        @empty
                            <p>No progress...</p>
                        @endforelse
                    </div>
                </div>
            </form>
        </div>


        <form action="{{ route('work.updateAreaForm') }}" method="POST">
            @csrf
            <div class="user-text-area" id="progressArea" style="gap: 10px; margin-top: 10px;">

                <div class="search-box">
                    <span>Task progress:</span>
                    <select name="task_id" id="taskDropdown" style="width:300px;">
                        <option value=""></option>
                    </select>
                </div>

                <h5>Updates :</h5>

                <div class="progressbar" id="progressWrapper" style="gap:10px; display:none; flex-wrap:wrap;">

                    <textarea name="updates_area" id="textArea" rows="7" style="padding:20px; width:100%;"
                        placeholder="Write updates here..."></textarea>

                    <div class="task-details" id="taskDetails"
                        style="flex:1; border:1px solid #ccc; padding:15px; display:none;">
                        <h5>Task Details:</h5>
                        <p><strong>Description:</strong> <span id="detailDescription"></span></p>
                        <p><strong>Date:</strong> <span id="detailDate"></span></p>
                        <p><strong>Type:</strong> <span id="detailType"></span></p>
                        <p><strong>Status:</strong> <span id="detailStatus"></span></p>
                    </div>

                </div>

                <button id="saveText" type="submit">Save</button>
            </div>
        </form>
    </div>

    @endif

<script>
    $(document).ready(function () {
        
        //DROPDOWN
        $('#taskDropdown').select2({
            placeholder: '--Select--',
            allowClear: true,
            width: 'resolve',
            ajax: {
                url: "{{ route('work.search') }}",
                dataType: 'json',
                
                data: function (params) {
                    return { query: params.term };
                },
                processResults: function (data) {
                    return {
                        results:
                            data.map(task => ({
                                id: task.id,
                                text: task.description,
                            }))
                    };
                },
                cache: true
            }
        });


        //DROPDOWN LIST
        $('#taskDropdown').val(null).trigger('change');

        $('#taskDropdown').on('select2:open', function() {
            if (!$('#taskDropdown').val()) {
                $('#taskDropdown').val(null).trigger('change');
            }
        });

        $('#taskDropdown').on('select2:select', function (e) {
            let data = e.params.data;
            $('#textArea').val('Loading...');
            $('#taskDetails').hide();

            //ON SELECTION; FETCH DATA
            $.ajax({
                url: "/work/get-task-details/" + data.id,
                type: "GET",
                success: function (response) {
                    $('#textArea').val(response.updates_area ?? '');
                    $('#detailDescription').text(response.description ?? '');
                    $('#detailDate').text(response.date ?? '');
                    $('#detailType').text(response.work_type ?? '');
                    $('#detailStatus').text(response.result ?? '');
                    $('#progressWrapper').fadeIn();
                    $('#taskDetails').show();
                },
                error: function () {
                    // $('#textArea').val('');
                    $('#taskDetails').hide();
                    $('#progressWrapper').fadeOut();
                }
            });
        });


        //ON CLEAR
        $('#taskDropdown').on('select2:clear', function () {
            $('#progressText').text('Select a task...');
            $('#textArea').val('');
            $('#taskDetails').hide();
            $('#progressWrapper').fadeOut(); // hide wrapper

        });

        //PROGRESS AREA
        // $('#activity-text-area').val('not ready')
        $.ajax({
            url: "/work/get-progress/" ,
            type: "GET",
            success:function(response){


            let item = response[response.length - 1]; // last (latest) item

            let output = 
                // "Date: " + item.date + " " +
                // "From: " + item.from_time + " " +
                // "To: " + item.to_time + "\n" +
                item.description;
                $('#activity-text-area').text(output)
            }
        })           
    });

</script>

@endsection