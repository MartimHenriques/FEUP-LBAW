<?php

namespace App\Http\Controllers;


use App\Models\User;
use App\Models\Event;
use App\Models\Notification;    
use App\Models\Report;
use App\Events\ReportN;
use App\Models\Attendee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class ReportsController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function showReportForm($id)
    {
        $not = Notification::find($id);
        $report = Report::find($not->id_report);
        $event = Event::find($report->id_event);
        $user = User::find($report->id_reporter);
        return view('pages.reportForm', ['report' => $report, 'event' => $event, 'user' => $user, 'not' => $not]);
    }

    public function dealWithReport(int $id, Request $request)
    {
        $not = Notification::find($id);
        $report = Report::find($not->id_report);
        $event = Event::find($report->id_event);

        if ($request->action == 'ignore') {
            $report->state = 'Rejected';
            $report->save();
            $not->read = true;
            $not->save();
            return redirect('/manage/reports');
        }
        else {
            $report->state = 'Banned';
            $report->save();
            $event->is_canceled = true;
            $event->save();
            $not->read = true;
            $not->save();
        }
        return redirect('/manage/reports');
    }
}

