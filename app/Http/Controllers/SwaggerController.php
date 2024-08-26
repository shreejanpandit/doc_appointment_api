<?php

namespace App\Http\Controllers;

/**
 * @OA\Info(
 *     title="Doctor Appointment API",
 *     version="1.0.0",
 *     description="API Documentation for Doctor Appointment System",
 *     @OA\Contact(
 *         email="shreezanpandit@gmial.com"
 *     )
 * )
 *
 * @OA\SecurityScheme(
 *     securityScheme="bearerAuth",
 *     type="http",
 *     scheme="bearer",
 *     bearerFormat="JWT",
 *     description="Enter your bearer token in the format: `Bearer {token}`"
 * )
 *
 * @OA\Server(
 *     url="https://bookdoctor.test/api",
 *     description="Doctor Appointment API Server"
 * )
 */

class SwaggerController extends Controller
{
    // This controller can be left empty
}
