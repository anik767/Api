<?php

namespace App\Http\Controllers;

/**
 * @OA\Info(
 *     title="API",
 *     version="2.0.0",
 *     description="Complete REST API for managing blog posts and user authentication. Built with Laravel and Sanctum authentication.",
 *     @OA\Contact(
 *         name="API Support",
 *         email="admin@gmail.com",
 *         url="https://admin.com"
 *     ),
 *     @OA\License(
 *         name="MIT",
 *         url="https://opensource.org/licenses/MIT"
 *     )
 * )
 * @OA\Server(
 *     url="http://localhost:8000",
 *     description="Local Development Server"
 * )
 * @OA\Server(
 *     url="https://api.yourdomain.com",
 *     description="Production Server"
 * )
 * @OA\SecurityScheme(
 *     securityScheme="sanctum",
 *     type="http",
 *     scheme="bearer",
 *     bearerFormat="Token",
 *     description="Enter your Bearer token in the format: Bearer {token}"
 * )
 * @OA\Tag(
 *     name="Auth",
 *     description="Authentication endpoints - Login, Register, Logout"
 * )
 * @OA\Tag(
 *     name="Posts",
 *     description="Blog post management - CRUD operations for posts"
 * )
 */
abstract class Controller
{
    //
}
