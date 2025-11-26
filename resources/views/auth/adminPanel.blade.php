@extends('layouts.mainlayout')


@section('extra-css')
<link rel="stylesheet" href="{{ asset('css/adminPanel.css') }}">
@endsection


@section('content')

    <H1>Admin panel</H1><br>


    <div class="work-status">
        <div class="work-status-title">
            <h3>Work Statuses : {{$now}}</h3>            
            <div class="form">
                <form >
                    <select name="user_id" onchange="this.form.submit()">
                        <option value="">All users</option>
                            @foreach ($users as $u)
                                <option value="{{ $u->id }}" {{ request('user_id') == $u->id ? 'selected' : '' }}>{{ $u->username}}</option>
                            @endforeach    
                    </select>

                    <select name="work_type" onchange="this.form.submit()">
                        <option value="">All tasks</option>
                        <option value="task" {{ request('work_type') == 'task' ? 'selected' : '' }}>Task</option>
                        <option value="testing" {{ request('work_type') == 'testing' ? 'selected' : '' }}>Testing</option>
                        <option value="learning" {{ request('work_type') == 'learning' ? 'selected' : '' }}>Learning</option>
                        <option value="error" {{ request('work_type') == 'error' ? 'selected' : '' }}>Error</option>
                    </select>

                    <select name="result" onchange="this.form.submit()">
                        <option value="">All status</option>
                        <option value="pending" {{ request('result') == 'pending' ? 'selected' : '' }}>pending</option>
                        <option value="solved" {{ request('result') == 'solved' ? 'selected' : '' }}>solved</option>
                        <option value="stuck" {{ request('result') == 'stuck' ? 'selected' : '' }}>stuck</option>
                    </select>
                </form>

            </div>
        </div> 
        <div class="data-table">
            <table>
                <thead>
                    <tr>
                        <th>Id</th>
                        <th>User</th>
                        <th>Work Type</th>
                        <th>Description</th>
                        <th>Date</th>
                        <th>Result</th>
                        <th>Duration</th>
                        <th>Time taken</th>
                    </tr>
                </thead>                
                <tbody>
                    @if ($datas->count() > 0)
                        @foreach($datas as $data)
                            <tr onclick="openModal(
                                '{{ $data->id }}',    
                                '{{ $data->user->username }}',
                                '{{ $data->work_type }}',
                                '{{ $data->description }}',
                                '{{ $data->date }}',
                                '{{ $data->end_date }}',
                                '{{ $data->start_time }}',
                                '{{ $data->end_time }}',
                                '{{ $data->days }}',
                                '{{ $data->time_taken }}',
                                '{{ $data->result  }}',
                                '{{ $data->updates_area }}',
                                '{{ $data->color }}')" style="cursor:pointer;">
                                <td style="color: {{$data->color}}">{{ $data->id ?? 'N/A' }}</td>
                                <td style="color: {{$data->color}}">{{ $data->user->username ?? 'N/A' }}</td>
                                <td style="color: {{$data->color}}">{{ $data->work_type }}</td>
                                <td style="color: {{$data->color}}">{{ $data->description }}</td>
                                <td style="color: {{$data->color}}">{{ $data->date }}</td>                            
                                <td style="color: {{$data->color}}">{{ $data->result }}</td> 
                                <td style="color: {{$data->color}}">{{ $data->days }} </td>
                                <td style="color: {{$data->color}}">{{ $data->time_taken }} </td>                               
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="5" style="text-align: center">
                                <h3>No records to show</h3>
                            </td>
                        </tr>
                    @endif
                </tbody>
            </table>  
        </div> 
        <div class="modal" id="rowModal">
    <div class="modal-box">
        <div class="ribbon" id="m_ribbon"></div>

        <span class="close-btn" onclick="closeModal()">&times;</span>

        <div class="detail-card">
            <h3>Task Details</h3>

            <div class="card-grid">
                <div class="card-item"><span>Id:</span><p id="m_id"></p></div>
                <div class="card-item"><span>User:</span><p id="m_user"></p></div>
                <div class="card-item"><span>Work Type:</span><p id="m_work"></p></div>
                <div class="card-item"><span>Date:</span><p id="m_date"></p></div>

                <div class="card-item wide">
                    <span>Description:</span>
                    <p id="m_desc"></p>
                </div>
            </div>

            <h3>Updates</h3>

            <div class="card-grid">
                <div class="card-item"><span>End Date:</span><p id="m_end_date"></p></div>
                <div class="card-item"><span>Start Time:</span><p id="m_start_time"></p></div>
                <div class="card-item"><span>End Time:</span><p id="m_end_time"></p></div>
                <div class="card-item"><span>Duration:</span><p id="m_days"></p></div>
                <div class="card-item"><span>Time Taken:</span><p id="m_taken"></p></div>
                <div class="card-item"><span>Result:</span><p id="m_result"></p></div>
                <div class="card-item wide">
                    <span>Recent Status:</span>
                    <p id="m_updations"></p>
                </div>
            </div>
        </div>
    </div>
</div>

    </div>

<script>
function openModal(
    id, user, work, desc, date, end_date, start_time,
    end_time, days, time_taken, result, updations, color
) {
    document.getElementById('m_id').textContent = id;
    document.getElementById('m_user').textContent = user;
    document.getElementById('m_work').textContent = work;
    document.getElementById('m_desc').textContent = desc;
    document.getElementById('m_date').textContent = date;
    document.getElementById('m_end_date').textContent = end_date;
    document.getElementById('m_start_time').textContent = start_time;
    document.getElementById('m_end_time').textContent = end_time;
    document.getElementById('m_days').textContent = days;
    document.getElementById('m_taken').textContent = time_taken;
    document.getElementById('m_result').textContent = result;
    document.getElementById('m_updations').textContent = updations;

    let ribbon = document.getElementById('m_ribbon');
    ribbon.textContent = result;
    ribbon.style.background = color;

    document.getElementById('rowModal').style.display = "flex";

    document.body.style.overflow = "hidden"; // Prevent background scroll
}

function closeModal() {
    document.getElementById('rowModal').style.display = "none";
    document.body.style.overflow = "auto"; // Restore scroll
}

window.onclick = function(e) {
    if (e.target.id === "rowModal") {
        closeModal();
    }
};

</script>




@endsection