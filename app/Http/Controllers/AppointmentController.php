<?php

namespace App\Http\Controllers;

use App\Events\ActionEvent;
use App\Http\Resources\AppointmentResource;
use App\Models\Appointment;
use App\Models\Notifications;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AppointmentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user = User::find(Auth::user()->id);

        return $this->jsonSuccess(200, "Appointments", AppointmentResource::collection($user->myAppointments()), "appointments");
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //create appointment
        $data = $request->validate([
            'consellor_id' => 'required',
            'appointment_date' => 'required',
            'appointment_time' => 'required',
            'title' => 'required',
            'description' => 'required',

        ]);
        $data['status'] = "Pending";
        $data['user_id'] = Auth::user()->id;
        $appointment = Appointment::create($data);
        Notifications::create([
            'type' => 'appointment',
            'message' => 'New appointment from ' . Auth::user()->name,
            'user_id' => $appointment->counsellor->id,
        ]);
        $name = Auth::user()->name;

        $this->sendNotification("New appointment from $name",'Appointment' , $this->tokens($appointment->counsellor->id), 'appointment', 'appointment');
        broadcast(new ActionEvent("Appointment", User::find($appointment->counsellor->id), "New appointment from Auth::user()->name"))->toOthers();
        return $this->jsonSuccess(200, "Appointment created successfully", new AppointmentResource($appointment), "appointment");
    }

    //create function to cancel appointment
    public function cancelAppointment(Appointment $appointment)
    {

        $appointment->status = "Cancelled";
        $appointment->save();
        broadcast(new ActionEvent("Appointment", User::find($appointment->counsellor->id), "Appointment cancelled successfully"))->toOthers();
        $name =  $appointment->user()->name;
        $this->sendNotification("New appointment from $name",'Appointment Cancelled' , $this->tokens($appointment->counsellor->id), 'appointment', 'appointment');

        return $this->jsonSuccess(200, "Appointment cancelled successfully", new AppointmentResource($appointment), "appointment");
    }

    //create function to reschedule appointment
    public function rescheduleAppointment(Request $request, Appointment $appointment)
    {
        $data = $request->validate([
            'appointment_date' => 'required',
            'appointment_time' => 'required',
            'description' => 'required',
        ]);
        $appointment->appointment_date = $data['appointment_date'];
        $appointment->appointment_time = $data['appointment_time'];
        $appointment->save();
        Notifications::create([
            'type' => 'appointment',
            'message' => "Appointment reschuled by" . Auth::user()->name,
            'user_id' => $appointment->counsellor->id,
        ]);
        $name =  $appointment->user()->name;

        $this->sendNotification("Appointment rescheduled by $name",'Appointment rescheduled successfully' , $this->tokens($appointment->counsellor->id), 'appointment', 'appointment');

        broadcast(new ActionEvent("Appointment", User::find($appointment->counsellor->id), "Appointment rescheduled successfully"))->toOthers();
        return $this->jsonSuccess(200, "Appointment rescheduled successfully", new AppointmentResource($appointment), "appointment");
    }

    public function getAppoinmentsWithCounsellor(Request $request)
    {

        return $this->jsonSuccess(200, "Appointments", AppointmentResource::collection(Auth::user()->getAppointmentsWithCounsellor($request->counsellor_id)), "appointments");
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Appointment  $appointment
     * @return \Illuminate\Http\Response
     */
    public function show(Appointment $appointment)
    {
        return $this->jsonSuccess(200, "Appointment found", $appointment, "appointment");
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Appointment  $appointment
     * @return \Illuminate\Http\Response
     */
    public function edit(Appointment $appointment)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Appointment  $appointment
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Appointment $appointment)
    {
        //update
        $data = $request->validate([
            'counsellor_id' => 'required',
            'appointment_date' => 'required',
            'title' => 'required',
            'description' => 'required',
        ]);
        $appointment->update($data);
        $this->sendNotification("Appointment updated",'Appointment updated successfully' , $this->tokens($appointment->counsellor->id), 'appointment', 'appointment');

        broadcast(new ActionEvent("Appointment", User::find($appointment->counsellor->id), "Appointment updated successfully"))->toOthers();
        return $this->jsonSuccess(200, "Appointment updated successfully", $appointment, "appointment");
    }

    //create function to reject appointment
    public function reject(Request $request, Appointment $appointment)
    {
        $appointment->update([
            'status' => 'Rejected'
        ]);
        Notifications::create([
            'type' => 'appointment',
            'message' => 'Appointment rejected by' . $appointment->counsellor->name,
            'user_id' => $appointment->user->id,
        ]);
        broadcast(new ActionEvent("Appointment", Auth::user(), "Appointment rejected successfully"))->toOthers();
        $this->sendNotification("Appointment rejected",'Appointment rejected successfully' , $this->tokens($appointment->user->id), 'appointment', 'appointment');

        return $this->jsonSuccess(200, "Appointment rejected successfully", new AppointmentResource($appointment), "appointment");
    }
    public function accept(Request $request, Appointment $appointment)
    {
        $data = $request->validate([
            'appointment_date' => 'required',
            'appointment_time' => 'required',
            'description' => 'required',
        ]);
        $data['status'] = "Approved";
        $appointment->update($data);
        Notifications::create([
            'type' => 'appointment',
            'message' => 'Appointment accepted by' . $appointment->counsellor->name,
            'user_id' => $appointment->user->id,
        ]);
        broadcast(new ActionEvent("Appointment", Auth::user(), "Appointment accepted successfully"))->toOthers();
        $this->sendNotification("Appointment accepted",'Appointment accepted successfully' , $this->tokens($appointment->user->id), 'appointment', 'appointment');

        return $this->jsonSuccess(200, "Appointment accepted successfully", new AppointmentResource($appointment), "appointment");
    }
    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Appointment  $appointment
     * @return \Illuminate\Http\Response
     */
    public function destroy(Appointment $appointment)
    {
        //
    }
}
