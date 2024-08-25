<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;

use App\Models\Doctor;
use App\Models\Schedule;
use App\Http\Requests\StoreScheduleRequest;
use App\Http\Requests\UpdateScheduleRequest;

class ScheduleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {

        if (auth()->user()->doctor) {
            return auth()->user()->doctor->schedules()->get();
        }

        return response()->json(['message' => 'Schedule not found'], 404);

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
    public function store(StoreScheduleRequest $request)
    {
        if ($request->user()->cannot('create',Schedule::class)){
            return response()->json(['message'=>'Unauthorized to create with your role'],403);
        }

        auth()->user()->doctor->schedules()->firstOrCreate(['week_day' => $request->week_day,'start_time' => $request->start_time,'end_time' => $request->end_time],$request->validated());
        return response()->json(['message' => 'Schedule Created'], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Schedule $schedule)
    {
        return response()->json(['schedule' => $schedule], 200);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Schedule $schedule)
    {
        return response()->json(['schedule' => $schedule], 200);

    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateScheduleRequest $request, Schedule $schedule)
    {
        if ($request->user()->cannot('update',$schedule)){
            return response()->json(['message'=>'Unauthorized to update with your role'],403);
        }

        $schedule->update($request->validated());
        return response()->json(['message' => 'Schedule Updated'], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Schedule $schedule)
    {
        if (auth()->user()->cannot('delete',$schedule)){
            return response()->json(['message'=>'Unauthorized to delete with your role'],403);
        }
        $schedule->delete();
        return response()->json(['message' => 'Schedule Deleted'], 200);
    }
}
