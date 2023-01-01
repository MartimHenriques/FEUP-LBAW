@extends ('layouts.app')

@section('title', 'AdminReports')

@section('content')

<h3>Reports</h3>

<table class="table table-striped">
    @foreach($reports as $report)
        <tr>
            <td>{{$report->date}}</td>
            <td>{{$report->motive}}</td>
            <td>
                @switch($report->STATE)
                    @case(0)
                        {{'Pending'}}
                        @break
                    @case(1)
                        {{'Banned'}}
                        @break
                    @case(2)
                        {{'Rejected'}}
                        @break
                    @default
                        {{''}} 
                @endswitch
            </td>
        </tr>
    @endforeach
</table>
<div class="text-center">
    {!! $reports->links(); !!}
</div>
@endsection