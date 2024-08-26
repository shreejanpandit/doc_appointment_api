<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Doctor;
use App\Http\Requests\StoreDoctorRequest;
use App\Http\Requests\UpdateDoctorRequest;

class DoctorController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if (request()->filled('department_id')){
            request()->validate(['department_id'=> 'exists:department,id']);
            $doctors = Doctor::where('department_id', request('department_id'))->get();
        }
        else{
            $doctors = Doctor::all();
        }
        return $doctors;

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
    public function store(StoreDoctorRequest $request)
    {
//        if ($request->user()->cannot('create',Doctor::class)){
//            return response()->json(['message'=>'Unauthorized to create with your role'],403);
//        }
       $doctor = $request->user()->doctor()->firstOrCreate($request->validated());

       return response()->json(['message'=>'doctor created','doctor'=>$doctor],201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Doctor $doctor)
    {
        return response()->json(['doctor'=>$doctor->load('schedules')],200);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Doctor $doctor)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateDoctorRequest $request, Doctor $doctor)
    {
//        if ($request->user()->cannot('update',$doctor)){
//            return response()->json(['message'=>'Unauthorized to update with your role'],403);
//        }

       $doctor->update($request->validated());
        return response()->json(['message'=>'doctor updated'],201);

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Doctor $doctor)
    {
//        if (auth()->user()->cannot('delete',$doctor)){
//            return response()->json(['message'=>'Unauthorized to delete with your role'],403);
//        }
        $doctor->delete();
        return response()->json(['message'=>'doctor deleted successfully'],200);

    }
}
