<?php

namespace App\Http\Controllers;

/**
 * @OA\Info(
 *     title="API",
 *     version="2.0.0",
 *     description="Complete API for user management.",
 *     @OA\Contact(
 *         name="Anik",
 *         email="your.email@company.com",
 *         url="https://yourwebsite.com"
 *     ),
 *     @OA\License(
 *         name="MIT",
 *         url="https://opensource.org/licenses/MIT"
 *     )
 * )
 * @OA\Server(
 *     url="http://localhost:8000",
 *     description="Development server"
 * )
 * @OA\Server(
 *     url="https://api.yourdomain.com",
 *     description="Production server"
 * )
 * @OA\SecurityScheme(
 *     securityScheme="sanctum",
 *     type="http",
 *     scheme="bearer",
 *     bearerFormat="JWT"
 * )
 */
abstract class Controller
{
    //
}
