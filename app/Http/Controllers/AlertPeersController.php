<?php

namespace App\Http\Controllers;

use App\Events\EveryOneEvent;
use App\Models\Alerts;
use App\Models\Location;
use App\Models\Notifications;
use Encore\Admin\Widgets\Alert;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use KMLaravel\GeographicalCalculator\Facade\GeoFacade;

class AlertPeersController extends Controller
{

    public function alertPeers(Request $request)
    {
        $request->validate([
            'longitude' => 'required',
            'latitude' => 'required',
        ]);
        $usersToAlert = [];
        //get locations
        $user = Auth::user();
        $friends = $user->friends;

        foreach ($friends as $friend) {
                array_push($usersToAlert, $friend);
        }
        $user = Auth::user();
        $this->createNewLocation(
            $request
        );

        foreach ($usersToAlert as $user_id) {
            $body = "Your Friend $user->name needs emergency Attention";
           Notifications::create(
                [
                    'type' => 'alert',
                    'message' => $body,
                    'user_id' => $user_id->id,
                    'locations' => $user->location->id
                ]
            );
            $title = 'Emergency Alert';

            $this->sendNotification($body, $title, $this->tokens($user_id), ['longitude' => $request->longitude,
            'latitude' => $request->latitude], 'alert');
        }
        // new Alert record
        Alerts::create(
            [
                'user_id' => $user->id,
                'longitude' => $request->longitude,
                'latitude' => $request->latitude,
            ]
        );

        $usersToAlertCount = count($usersToAlert);

        // send mail to alerting user //
        Mail::send('email.alertPeersMail', [], function ($message) use ($request) {
            $message->to(Auth::user()->email);
            $message->subject('Your peers have been alerted');
        });

        if ($usersToAlertCount == 0) {
            return $this->jsonSuccess(200, "Alerted 0 Peers this message will be forwarded to the administrator", count($usersToAlert), "count",);
        } else {
            return $this->jsonSuccess(200, "Alerted $usersToAlertCount Successfully", count($usersToAlert), "count",);
        }

    }

    public function createNewLocation(Request $request)
    {
        $request->validate([
            'longitude' => 'required',
            'latitude' => 'required',
        ]);

        if (Auth::user()->location != null) {
            return $this->updateLocation($request, Auth::user()->location->id);
        } else {

            $location = new Location();
            $location->longitude = $request->input('longitude');
            $location->latitude = $request->input('latitude');
            $location->user_id = Auth::user()->id;
            $location->save();
            return response(['success' => true, 'message' => 'location created successfully',]);
        }
    }

    public function updateLocation(Request $request, $locationId)
    {

        Location::find($locationId)->update(
            $request->all()
        );

        return response(['success' => true, 'message' => 'location updated successfully',]);

    }
}
