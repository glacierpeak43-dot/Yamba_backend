<?php

namespace App\Http\Controllers;

use App\Models\Ambulances;
use App\Models\FireStations;
use App\Models\NationalHelpLines;
use App\Models\NearBySupport;
use App\Models\PoliceContacts;
use App\Models\UniversityHelpLine;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EmergencyContactsController extends Controller
{
    public function getContacts(Request $request): \Illuminate\Http\JsonResponse
    {
        $user = Auth::user();
        $ambulances = Ambulances::where('university_id', $user->university_id)->get();
        $fireStations = FireStations::where('university_id', $user->university_id)->get();
        $police = PoliceContacts::where('university_id', $user->university_id)->get();
        $nearby = NearBySupport::where('university_id', $user->university_id)->get();
        $uni = UniversityHelpLine::where('university_id', $user->university_id)->get();
        $nationalHelplines = NationalHelpLines::all();
        return response()->json(['success' => true,
            'ambulances' => $ambulances,
            'police' => $police,
            'fire_stations' => $fireStations,
            'nearby' => $nearby,
            'helplines' => $nationalHelplines,
            'unihelplines' => $uni
        ]);
    }


}
