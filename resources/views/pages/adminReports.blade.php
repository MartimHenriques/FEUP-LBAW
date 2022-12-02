@extends ('layouts.app')

@section('title', 'AdminReports')

@section('content')

<h1>Admin Page</h1>
<h3>Manage Reports</h3>
<table class="table table-striped">
<th>Reports</th>

    @foreach($reports as $report)
        <tr>
            <td>{{$report->date}}</td>
            <td>{{$report->motive}}</td>
            <td>{{$type = $report->STATE}}</td>
            @switch($type)
                @case('Pending')
                    {{$type = 'Pending'}}
                    @break
                @case('Banned')
                    {{$type = 'Banned'}}
                    @break
                @case('Rejected')
                    {{$type = 'Rejected'}}
                    @break
                @default
                    {{$type = 'Not working'}} 
            @endswitch
            <td>{{$type}}</td>
            <td>{{--<a class="button" href="{{route('deleteUser',['id'=>$user->id])}}">User</a>--}}</td>
        </tr>
    @endforeach

</table>
@endsection