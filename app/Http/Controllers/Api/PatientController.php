<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;

use App\Models\Patient;
use App\Http\Requests\StorePatientRequest;
use App\Http\Requests\UpdatePatientRequest;

class PatientController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
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
    public function store(StorePatientRequest $request)
    {
        if ($request->user()->cannot('create',Patient::class)){
            return response()->json(['message'=>'Unauthorized to create with your role'],403);
        }
        $patient = $request->user()->patient()->firstOrCreate(['user_id' => auth()->id],$request->validated());

        return response()->json(['message'=>'patient created','patient'=>$patient],201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Patient $patient)
    {
        return response()->json(['patient'=>$patient],200);

    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Patient $patient)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePatientRequest $request, Patient $patient)
    {
        if ($request->user()->cannot('update',$patient)){
            return response()->json(['message'=>'Unauthorized to update with your role'],403);
        }

        $patient->update($request->validated());
        return response()->json(['message'=>'patient updated'],201);

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Patient $patient)
    {
        if (auth()->user()->cannot('delete',$patient)){
            return response()->json(['message'=>'Unauthorized to delete with your role'],403);
        }
        $patient->delete();
        return response()->json(['message'=>'patient deleted successfully'],200);

    }
}
