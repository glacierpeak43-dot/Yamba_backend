<?php

namespace App\Http\Controllers;

use App\Models\AbuseReports;
use App\Models\FeedBack;
use App\Models\ReportAbuse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AbuseReportsController extends Controller
{
    public function report(Request $request)
    {

        $request->validate([
            'report' => 'required'
        ]);

        AbuseReports::Create([
            'report' => $request->report,
            'user_id' => Auth::user()->id
        ]);

        return response(['success' => true, 'message' => 'Reported Successfully',]);
    }


    public function feedback(Request $request)
    {

        $request->validate([
            'report' => 'required'
        ]);

        FeedBack::Create([
            'report' => $request->report,
            'user_id' => Auth::user()->id
        ]);

        return response(['success' => true, 'message' => 'Reported Successfully',]);
    }
}
