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
     * @OA\Get(
     *     path="/schedules",
     *     summary="List all schedules for the authenticated doctor",
     *     description="Retrieve a list of schedules for the authenticated doctor. Returns a 404 error if no schedules are found.",
     *     tags={"Schedules"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Successful retrieval of schedules",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="doctor_id", type="integer", example=1),
     *                 @OA\Property(property="week_day", type="string", enum={"sunday", "monday", "tuesday", "wednesday", "thursday", "friday", "saturday"}, example="monday"),
     *                 @OA\Property(property="start_time", type="string", format="time", example="09:00:00"),
     *                 @OA\Property(property="end_time", type="string", format="time", example="17:00:00"),
     *                 @OA\Property(property="created_at", type="string", format="date-time", example="2024-08-29T00:00:00Z"),
     *                 @OA\Property(property="updated_at", type="string", format="date-time", example="2024-08-29T00:00:00Z")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="No schedules found",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Schedule not found")
     *         )
     *     )
     * )
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
     * @OA\Post(
     *     path="/schedules",
     *     summary="Create a new schedule",
     *     description="Store a new schedule for the authenticated doctor. Access to this endpoint is restricted to authorized users.",
     *     tags={"Schedules"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"week_day", "start_time", "end_time"},
     *             @OA\Property(property="week_day", type="string", enum={"sunday", "monday", "tuesday", "wednesday", "thursday", "friday", "saturday"}, example="monday"),
     *             @OA\Property(property="start_time", type="string", format="time", example="09:00:00"),
     *             @OA\Property(property="end_time", type="string", format="time", example="17:00:00")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Schedule successfully created",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Schedule Created"),
     *             @OA\Property(property="schedule", type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="doctor_id", type="integer", example=1),
     *                 @OA\Property(property="week_day", type="string", enum={"sunday", "monday", "tuesday", "wednesday", "thursday", "friday", "saturday"}, example="monday"),
     *                 @OA\Property(property="start_time", type="string", format="time", example="09:00:00"),
     *                 @OA\Property(property="end_time", type="string", format="time", example="17:00:00"),
     *                 @OA\Property(property="created_at", type="string", format="date-time", example="2024-08-29T00:00:00Z"),
     *                 @OA\Property(property="updated_at", type="string", format="date-time", example="2024-08-29T00:00:00Z")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Unauthorized",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Unauthorized to create with your role"),
     *             @OA\Property(property="type", type="string", example="error")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation Error",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="The given data was invalid."),
     *             @OA\Property(property="errors", type="object",
     *                 @OA\Property(property="week_day", type="array", @OA\Items(type="string", example="The selected week_day is invalid.")),
     *                 @OA\Property(property="start_time", type="array", @OA\Items(type="string", example="The start_time field is required and must be a valid time.")),
     *                 @OA\Property(property="end_time", type="array", @OA\Items(type="string", example="The end_time field is required and must be a valid time."))
     *             )
     *         )
     *     )
     * )
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
     * @OA\Get(
     *     path="/schedules/{id}",
     *     summary="Get a specific schedule",
     *     description="Retrieve details of a specific schedule by ID.",
     *     tags={"Schedules"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of the schedule to retrieve",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful retrieval of schedule details",
     *         @OA\JsonContent(
     *             @OA\Property(property="schedule", type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="doctor_id", type="integer", example=1),
     *                 @OA\Property(property="week_day", type="string", enum={"sunday", "monday", "tuesday", "wednesday", "thursday", "friday", "saturday"}, example="monday"),
     *                 @OA\Property(property="start_time", type="string", format="time", example="09:00:00"),
     *                 @OA\Property(property="end_time", type="string", format="time", example="17:00:00"),
     *                 @OA\Property(property="created_at", type="string", format="date-time", example="2024-08-29T00:00:00Z"),
     *                 @OA\Property(property="updated_at", type="string", format="date-time", example="2024-08-29T00:00:00Z")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Unauthorized"),
     *             @OA\Property(property="type", type="string", example="error")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Schedule not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Schedule not found"),
     *             @OA\Property(property="type", type="string", example="error")
     *         )
     *     )
     * )
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
     * @OA\Put(
     *     path="/schedules/{id}",
     *     summary="Update a specific schedule",
     *     description="Update details of a specific schedule by ID.",
     *     tags={"Schedules"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of the schedule to update",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"week_day", "start_time", "end_time"},
     *             @OA\Property(property="week_day", type="string", enum={"sunday", "monday", "tuesday", "wednesday", "thursday", "friday", "saturday"}, example="monday"),
     *             @OA\Property(property="start_time", type="string", format="time", example="09:00:00"),
     *             @OA\Property(property="end_time", type="string", format="time", example="17:00:00")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Schedule successfully updated",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Schedule Updated"),
     *             @OA\Property(property="schedule", type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="doctor_id", type="integer", example=1),
     *                 @OA\Property(property="week_day", type="string", enum={"sunday", "monday", "tuesday", "wednesday", "thursday", "friday", "saturday"}, example="monday"),
     *                 @OA\Property(property="start_time", type="string", format="time", example="09:00:00"),
     *                 @OA\Property(property="end_time", type="string", format="time", example="17:00:00"),
     *                 @OA\Property(property="created_at", type="string", format="date-time", example="2024-08-29T00:00:00Z"),
     *                 @OA\Property(property="updated_at", type="string", format="date-time", example="2024-08-29T00:00:00Z")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Unauthorized",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Unauthorized to update with your role"),
     *             @OA\Property(property="type", type="string", example="error")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Schedule not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Schedule not found"),
     *             @OA\Property(property="type", type="string", example="error")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation Error",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="The given data was invalid."),
     *             @OA\Property(property="errors", type="object",
     *                 @OA\Property(property="week_day", type="array", @OA\Items(type="string", example="The selected week_day is invalid.")),
     *                 @OA\Property(property="start_time", type="array", @OA\Items(type="string", example="The start_time field is required and must be a valid time.")),
     *                 @OA\Property(property="end_time", type="array", @OA\Items(type="string", example="The end_time field is required and must be a valid time."))
     *             )
     *         )
     *     )
     * )
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
     * @OA\Delete(
     *     path="/schedules/{id}",
     *     summary="Delete a specific schedule",
     *     description="Remove a specific schedule from the database by ID.",
     *     tags={"Schedules"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of the schedule to delete",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Schedule successfully deleted",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Schedule Deleted")
     *         )
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Unauthorized",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Unauthorized to delete with your role"),
     *             @OA\Property(property="type", type="string", example="error")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Schedule not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Schedule not found"),
     *             @OA\Property(property="type", type="string", example="error")
     *         )
     *     )
     * )
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
