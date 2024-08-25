<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;

use App\Models\Appointment;
use App\Http\Requests\StoreAppointmentRequest;
use App\Http\Requests\UpdateAppointmentRequest;

class AppointmentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if (auth()->user()->hasRole('patient')) {
            return auth()->user()->patient->appointments()->get();
        }

        return auth()->user()->doctor->appointments()->get();
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreAppointmentRequest $request)
    {
        if ($request->user()->cannot('create',Appointment::class)){
            return response()->json(['message'=>'Unauthorized to create with your role'],403);
        }

        $request->user()->patient->appointments()->create($request->validated());

        return response()->json(['message'=>'Appointment created successfully'],201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Appointment $appointment)
    {
        return response()->json(['appointment' => $appointment], 200);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Appointment $appointment)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateAppointmentRequest $request, Appointment $appointment)
    {
        if ($request->user()->cannot('update',$appointment)){
            return response()->json(['message'=>'Unauthorized to update with your role'],403);
        }

        $appointment->update($request->validated());

        return response()->json(['message'=>'Appointment updated successfully'],201);

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Appointment $appointment)
    {
        if (auth()->user()->cannot('delete',$appointment)){
            return response()->json(['message'=>'Unauthorized to delete with your role'],403);
        }

        $appointment->delete();
        return response()->json(['message'=>'Appointment deleted successfully'],200);
    }
}
