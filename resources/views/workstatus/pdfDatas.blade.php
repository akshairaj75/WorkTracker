<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Work Status Report</title>
    <style>
        body { font-family: sans-serif; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #000; padding: 8px; text-align: left; }
        img { width: 80px; height: auto; }

    </style>
</head>
<body>
    <h2>Work Status Report</h2>
    <table>
        <tr>
            {{-- <th>ID</th> --}}
            <th>Date</th>
            <th>Type</th>
            <th>Assigned user</th>
            <th>Description</th>
            <th>Start time</th>
            <th>End time</th>
            <th>Status</th>
            <th >Attatchments</th>
        </tr>
        @if ($workDatas->count() > 0)
            @foreach($workDatas as $data)
            <tr>
                {{-- <td>{{$data->id}}</td> --}}
                <td>{{$data->date}}</td>
                <td>{{$data->work_type}}</td>
                <td>{{$data->user->username}} </td>
                <td>{{$data->description}}</td>
                <td>{{$data->start_time }}</td>
                <td>{{$data->end_time }}</td>
                <td>{{$data->result }}</td>
                <td>
                    @foreach($data->attachments as $file)
                            <a style="padding: 5px; border-radius: 5px; color: rgb(83, 55, 225)" href="{{ asset('uploads/'.$file->file_name) }}" target="_blank">
                                <img style="width: 60px" src="{{ asset('uploads/'.$file->file_name) }}" alt="*open "/>
                        @endforeach
                </td>
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
</body>
</html>
