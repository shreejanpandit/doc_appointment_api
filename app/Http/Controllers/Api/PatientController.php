<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;

use App\Models\Patient;
use App\Http\Requests\StorePatientRequest;
use App\Http\Requests\UpdatePatientRequest;

class PatientController extends Controller
{
    /**
     * @OA\Get(
     *     path="/patients",
     *     summary="List all patients",
     *     description="Retrieve a list of patients. This endpoint may be restricted based on user roles.",
     *     tags={"Patients"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Successful retrieval of patient list",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="user_id", type="integer", example=1),
     *                 @OA\Property(property="dob", type="string", format="date", example="1990-01-01"),
     *                 @OA\Property(property="image", type="string", example="patient_image.jpg"),
     *                 @OA\Property(property="gender", type="string", enum={"male", "female", "other"}, example="female"),
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
       return   Patient::all();
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
     *     path="/patients",
     *     summary="Create a new patient",
     *     description="Store a new patient record in the database. This endpoint is accessible to authenticated users with the appropriate role.",
     *     tags={"Patients"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"dob", "gender"},
     *             @OA\Property(property="dob", type="string", format="date", example="1990-01-01"),
     *             @OA\Property(property="gender", type="string", enum={"male", "female", "other"}, example="male"),
     *             @OA\Property(property="image", type="string", example="patient_image.jpg")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Patient successfully created",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Patient created"),
     *             @OA\Property(property="patient", type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="user_id", type="integer", example=1),
     *                 @OA\Property(property="dob", type="string", format="date", example="1990-01-01"),
     *                 @OA\Property(property="image", type="string", example="patient_image.jpg"),
     *                 @OA\Property(property="gender", type="string", enum={"male", "female", "other"}, example="male"),
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
     *                 @OA\Property(property="dob", type="array", @OA\Items(type="string", example="The dob field is required and must be a valid date before today.")),
     *                 @OA\Property(property="gender", type="array", @OA\Items(type="string", example="The selected gender is invalid.")),
     *                 @OA\Property(property="image", type="array", @OA\Items(type="string", example="The image must be a file of type: jpeg, png, jpg, gif."))
     *             )
     *         )
     *     )
     * )
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
     * @OA\Get(
     *     path="/patients/{id}",
     *     summary="Get a specific patient",
     *     description="Retrieve details of a specific patient by ID.",
     *     tags={"Patients"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of the patient to retrieve",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful retrieval of patient details",
     *         @OA\JsonContent(
     *             @OA\Property(property="patient", type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="user_id", type="integer", example=1),
     *                 @OA\Property(property="dob", type="string", format="date", example="1990-01-01"),
     *                 @OA\Property(property="image", type="string", example="patient_image.jpg"),
     *                 @OA\Property(property="gender", type="string", enum={"male", "female", "other"}, example="female"),
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
     *         description="Patient not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Patient not found"),
     *             @OA\Property(property="type", type="string", example="error")
     *         )
     *     )
     * )
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
     * @OA\Put(
     *     path="/patients/{id}",
     *     summary="Update a specific patient",
     *     description="Update details of a specific patient by ID.",
     *     tags={"Patients"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of the patient to update",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"dob", "gender"},
     *             @OA\Property(property="dob", type="string", format="date", example="1990-01-01"),
     *             @OA\Property(property="gender", type="string", enum={"male", "female", "other"}, example="female"),
     *             @OA\Property(property="image", type="string", example="patient_image.jpg")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Patient successfully updated",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Patient updated successfully"),
     *             @OA\Property(property="patient", type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="user_id", type="integer", example=1),
     *                 @OA\Property(property="dob", type="string", format="date", example="1990-01-01"),
     *                 @OA\Property(property="image", type="string", example="updated_image.jpg"),
     *                 @OA\Property(property="gender", type="string", enum={"male", "female", "other"}, example="female"),
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
     *         description="Patient not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Patient not found"),
     *             @OA\Property(property="type", type="string", example="error")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation Error",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="The given data was invalid."),
     *             @OA\Property(property="errors", type="object",
     *                 @OA\Property(property="dob", type="array", @OA\Items(type="string", example="The dob field is required and must be a valid date before today.")),
     *                 @OA\Property(property="gender", type="array", @OA\Items(type="string", example="The selected gender is invalid.")),
     *                 @OA\Property(property="image", type="array", @OA\Items(type="string", example="The image must be a file of type: jpeg, png, jpg, gif."))
     *             )
     *         )
     *     )
     * )
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
     * @OA\Delete(
     *     path="/patients/{id}",
     *     summary="Delete a specific patient",
     *     description="Remove a specific patient record from the database by ID.",
     *     tags={"Patients"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of the patient to delete",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Patient successfully deleted",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Patient deleted successfully")
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
     *         description="Patient not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Patient not found"),
     *             @OA\Property(property="type", type="string", example="error")
     *         )
     *     )
     * )
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
