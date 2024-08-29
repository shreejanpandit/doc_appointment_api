<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;

use App\Models\Appointment;
use App\Http\Requests\StoreAppointmentRequest;
use App\Http\Requests\UpdateAppointmentRequest;

class AppointmentController extends Controller
{
    /**
     * @OA\Get(
     *     path="/appointments",
     *     summary="List all appointments",
     *     description="Retrieve a list of appointments for the authenticated user. If the user is a patient, only their appointments are returned. If the user is a doctor, only appointments they are involved in are returned.",
     *     tags={"Appointments"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Successful retrieval of appointments list",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="patient_id", type="integer", example=1),
     *                 @OA\Property(property="doctor_id", type="integer", example=2),
     *                 @OA\Property(property="date", type="string", format="date", example="2024-08-29"),
     *                 @OA\Property(property="time", type="string", format="time", example="14:30"),
     *                 @OA\Property(property="description", type="string", example="Consultation regarding recent symptoms"),
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
     *     )
     * )
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
     * @OA\Post(
     *     path="/appointments",
     *     summary="Create a new appointment",
     *     description="Store a new appointment record in the database. This endpoint is accessible to authenticated users with the role of 'patient'.",
     *     tags={"Appointments"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"doctor_id", "description", "time", "date"},
     *             @OA\Property(property="doctor_id", type="integer", example=2),
     *             @OA\Property(property="description", type="string", example="Consultation regarding recent symptoms"),
     *             @OA\Property(property="time", type="string", format="time", example="14:30"),
     *             @OA\Property(property="date", type="string", format="date", example="2024-08-29")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Appointment successfully created",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Appointment created successfully"),
     *             @OA\Property(property="appointment", type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="patient_id", type="integer", example=1),
     *                 @OA\Property(property="doctor_id", type="integer", example=2),
     *                 @OA\Property(property="date", type="string", format="date", example="2024-08-29"),
     *                 @OA\Property(property="time", type="string", format="time", example="14:30"),
     *                 @OA\Property(property="description", type="string", example="Consultation regarding recent symptoms"),
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
     *                 @OA\Property(property="doctor_id", type="array", @OA\Items(type="string", example="The selected doctor_id is invalid.")),
     *                 @OA\Property(property="description", type="array", @OA\Items(type="string", example="The description field is required.")),
     *                 @OA\Property(property="time", type="array", @OA\Items(type="string", example="The time field must be in the format H:i.")),
     *                 @OA\Property(property="date", type="array", @OA\Items(type="string", example="The date field must be in the format Y-m-d."))
     *             )
     *         )
     *     )
     * )
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
     * @OA\Get(
     *     path="/appointments/{id}",
     *     summary="Get a specific appointment",
     *     description="Retrieve details of a specific appointment by ID.",
     *     tags={"Appointments"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of the appointment to retrieve",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful retrieval of appointment details",
     *         @OA\JsonContent(
     *             @OA\Property(property="appointment", type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="patient_id", type="integer", example=1),
     *                 @OA\Property(property="doctor_id", type="integer", example=2),
     *                 @OA\Property(property="date", type="string", format="date", example="2024-08-29"),
     *                 @OA\Property(property="time", type="string", format="time", example="14:30"),
     *                 @OA\Property(property="description", type="string", example="Consultation regarding recent symptoms"),
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
     *         description="Appointment not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Appointment not found"),
     *             @OA\Property(property="type", type="string", example="error")
     *         )
     *     )
     * )
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
     * @OA\Put(
     *     path="/appointments/{id}",
     *     summary="Update a specific appointment",
     *     description="Update details of a specific appointment by ID.",
     *     tags={"Appointments"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of the appointment to update",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"doctor_id", "description", "time", "date"},
     *             @OA\Property(property="doctor_id", type="integer", example=2),
     *             @OA\Property(property="description", type="string", example="Updated description"),
     *             @OA\Property(property="time", type="string", format="time", example="15:00"),
     *             @OA\Property(property="date", type="string", format="date", example="2024-08-30")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Appointment successfully updated",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Appointment updated successfully"),
     *             @OA\Property(property="appointment", type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="patient_id", type="integer", example=1),
     *                 @OA\Property(property="doctor_id", type="integer", example=2),
     *                 @OA\Property(property="date", type="string", format="date", example="2024-08-30"),
     *                 @OA\Property(property="time", type="string", format="time", example="15:00"),
     *                 @OA\Property(property="description", type="string", example="Updated description"),
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
     *         description="Appointment not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Appointment not found"),
     *             @OA\Property(property="type", type="string", example="error")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation Error",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="The given data was invalid."),
     *             @OA\Property(property="errors", type="object",
     *                 @OA\Property(property="doctor_id", type="array", @OA\Items(type="string", example="The selected doctor_id is invalid.")),
     *                 @OA\Property(property="description", type="array", @OA\Items(type="string", example="The description field is required.")),
     *                 @OA\Property(property="time", type="array", @OA\Items(type="string", example="The time field must be in the format H:i.")),
     *                 @OA\Property(property="date", type="array", @OA\Items(type="string", example="The date field must be in the format Y-m-d."))
     *             )
     *         )
     *     )
     * )
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
     * @OA\Delete(
     *     path="/appointments/{id}",
     *     summary="Delete a specific appointment",
     *     description="Remove a specific appointment record from the database by ID.",
     *     tags={"Appointments"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of the appointment to delete",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Appointment successfully deleted",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Appointment deleted successfully")
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
     *         description="Appointment not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Appointment not found"),
     *             @OA\Property(property="type", type="string", example="error")
     *         )
     *     )
     * )
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
