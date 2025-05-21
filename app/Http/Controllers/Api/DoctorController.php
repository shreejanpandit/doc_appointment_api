<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Doctor;
use App\Http\Requests\StoreDoctorRequest;
use App\Http\Requests\UpdateDoctorRequest;

class DoctorController extends Controller
{
    /**
     * @OA\Get(
     *     path="/doctors",
     *     summary="List all doctors",
     *     description="Retrieve a list of doctors. Optionally, filter by department_id.",
     *     tags={"Doctors"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="department_id",
     *         in="query",
     *         description="Filter doctors by department ID",
     *         required=false,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful retrieval of doctor list",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="contact", type="string", example="1234567890"),
     *                 @OA\Property(property="bio", type="string", example="Experienced cardiologist"),
     *                 @OA\Property(property="department_id", type="integer", example=2),
     *                 @OA\Property(property="image", type="string", example="doctor_image.jpg"),
     *                 @OA\Property(property="created_at", type="string", format="date-time", example="2023-01-01T00:00:00Z"),
     *                 @OA\Property(property="updated_at", type="string", format="date-time", example="2023-01-01T00:00:00Z")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation Error",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="The given data was invalid."),
     *             @OA\Property(property="errors", type="object",
     *                 @OA\Property(property="department_id", type="array", @OA\Items(type="string", example="The selected department_id is invalid."))
     *             )
     *         )
     *     )
     * )
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
     * @OA\Post(
     *     path="/doctors",
     *     summary="Create a new doctor",
     *     description="Store a new doctor record in the database.",
     *     tags={"Doctors"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"contact", "bio", "department_id"},
     *             @OA\Property(property="contact", type="string", example="1234567890"),
     *             @OA\Property(property="bio", type="string", example="Experienced cardiologist"),
     *             @OA\Property(property="department_id", type="integer", example=2),
     *             @OA\Property(property="image", type="string", example="doctor_image.jpg")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Doctor successfully created",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="doctor created"),
     *             @OA\Property(property="doctor", type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="contact", type="string", example="1234567890"),
     *                 @OA\Property(property="bio", type="string", example="Experienced cardiologist"),
     *                 @OA\Property(property="department_id", type="integer", example=2),
     *                 @OA\Property(property="image", type="string", example="doctor_image.jpg"),
     *                 @OA\Property(property="created_at", type="string", format="date-time", example="2023-01-01T00:00:00Z"),
     *                 @OA\Property(property="updated_at", type="string", format="date-time", example="2023-01-01T00:00:00Z")
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
     *                 @OA\Property(property="contact", type="array", @OA\Items(type="string", example="The contact field is required.")),
     *                 @OA\Property(property="bio", type="array", @OA\Items(type="string", example="The bio field is required.")),
     *                 @OA\Property(property="department_id", type="array", @OA\Items(type="string", example="The selected department_id is invalid."))
     *             )
     *         )
     *     )
     * )
     */

    public function store(StoreDoctorRequest $request)
    {
        if ($request->user()->cannot('create',Doctor::class)){
            return response()->json(['message'=>'Unauthorized to create with your role'],403);
        }
       $doctor = $request->user()->doctor()->firstOrCreate($request->validated());

       return response()->json(['message'=>'doctor created','doctor'=>$doctor],201);
    }

    /**
     * @OA\Get(
     *     path="/doctors/{id}",
     *     summary="Get a specific doctor",
     *     description="Retrieve details of a specific doctor including their schedules.",
     *     tags={"Doctors"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of the doctor to retrieve",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful retrieval of doctor details",
     *         @OA\JsonContent(
     *             @OA\Property(property="doctor", type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="contact", type="string", example="1234567890"),
     *                 @OA\Property(property="bio", type="string", example="Experienced cardiologist"),
     *                 @OA\Property(property="department_id", type="integer", example=2),
     *                 @OA\Property(property="image", type="string", example="doctor_image.jpg"),
     *                 @OA\Property(property="created_at", type="string", format="date-time", example="2023-01-01T00:00:00Z"),
     *                 @OA\Property(property="updated_at", type="string", format="date-time", example="2023-01-01T00:00:00Z"),
     *                 @OA\Property(property="schedules", type="array", @OA\Items(
     *                     type="object",
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="schedule_time", type="string", example="09:00:00"),
     *                     @OA\Property(property="appointment_id", type="integer", example=1)
     *                 ))
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Doctor not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Doctor not found"),
     *             @OA\Property(property="type", type="string", example="error")
     *         )
     *     )
     * )
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
     * @OA\Put(
     *     path="/doctors/{id}",
     *     summary="Update a specific doctor",
     *     description="Update details of a specific doctor.",
     *     tags={"Doctors"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of the doctor to update",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"contact", "bio", "department_id"},
     *             @OA\Property(property="contact", type="string", example="1234567890"),
     *             @OA\Property(property="bio", type="string", example="Experienced cardiologist"),
     *             @OA\Property(property="department_id", type="integer", example=2),
     *             @OA\Property(property="image", type="string", example="doctor_image.jpg")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Doctor successfully updated",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="doctor updated")
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
     *         response=422,
     *         description="Validation Error",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="The given data was invalid."),
     *             @OA\Property(property="errors", type="object",
     *                 @OA\Property(property="contact", type="array", @OA\Items(type="string", example="The contact field is required.")),
     *                 @OA\Property(property="bio", type="array", @OA\Items(type="string", example="The bio field is required.")),
     *                 @OA\Property(property="department_id", type="array", @OA\Items(type="string", example="The selected department_id is invalid."))
     *             )
     *         )
     *     )
     * )
     */
    public function update(UpdateDoctorRequest $request, Doctor $doctor)
    {
        if ($request->user()->cannot('update',$doctor)){
            return response()->json(['message'=>'Unauthorized to update with your role'],403);
        }

       $doctor->update($request->validated());
        return response()->json(['message'=>'doctor updated'],201);

    }

    /**
     * @OA\Delete(
     *     path="/doctors/{id}",
     *     summary="Delete a specific doctor",
     *     description="Remove a specific doctor from the database.",
     *     tags={"Doctors"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of the doctor to delete",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Doctor successfully deleted",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="doctor deleted successfully")
     *         )
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Unauthorized",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Unauthorized to delete with your role"),
     *             @OA\Property(property="type", type="string", example="error")
     *         )
     *     )
     * )
     */
    public function destroy(Doctor $doctor)
    {
        if (auth()->user()->cannot('delete',$doctor)){
            return response()->json(['message'=>'Unauthorized to delete with your role'],403);
        }
        $doctor->delete();
        return response()->json(['message'=>'doctor deleted successfully'],200);

    }
}
