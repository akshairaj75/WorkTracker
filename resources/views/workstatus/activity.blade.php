@extends('layouts.mainlayout')


@section('extra-css')
<link rel="stylesheet" href="{{ asset('css/index.css') }}">

@endsection
@section('content')


<div class="work-status">
        <div class="work-status-title">
            <h3>Work Statuses : {{$user->username}} </h3><br>           
        </div> 
            <div class="data-table">
            <table>
                <thead>
                    <tr>
                        <th>Work Type</th>
                        <th>Description</th>
                        <th>Date</th>
                        <th>Result</th>
                        <th>user</th>
                    </tr>
               </thead>                
                <tbody>
                     @if ($activityStatuses->count() > 0)
                    @foreach($activityStatuses as $activity)
                        <tr>
                            <td>{{ $activity->work_type }}</td>
                            <td>{{ $activity->description }}</td>
                            <td>{{ $activity->date }}</td>                            
                            <td>{{ $activity->result }}</td>
                            <td>{{ $activity->user->username }}</td>
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
    </div>

@endsection