<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PostController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/posts",
     *     summary="Get all posts",
     *     description="Retrieve a paginated list of all blog posts with author information",
     *     tags={"Posts"},
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         description="Page number for pagination",
     *         required=false,
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Parameter(
     *         name="per_page",
     *         in="query",
     *         description="Number of posts per page (default: 10)",
     *         required=false,
     *         @OA\Schema(type="integer", example=10)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Posts retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", type="array", @OA\Items(
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="title", type="string", example="My First Blog Post"),
     *                 @OA\Property(property="content", type="string", example="This is the content of my first blog post"),
     *                 @OA\Property(property="image", type="string", example="posts/abc123.jpg", description="Image path relative to storage/app/public"),
     *                 @OA\Property(property="category_id", type="integer", example=1, nullable=true),
     *                 @OA\Property(property="user_id", type="integer", example=1),
     *                 @OA\Property(property="published_at", type="string", format="date-time", example="2025-11-05T12:00:00.000000Z"),
     *                 @OA\Property(property="created_at", type="string", format="date-time", example="2025-11-05T12:00:00.000000Z"),
     *                 @OA\Property(property="updated_at", type="string", format="date-time", example="2025-11-05T12:00:00.000000Z"),
     *                 @OA\Property(property="user", type="object",
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="name", type="string", example="John Doe"),
     *                     @OA\Property(property="email", type="string", example="john@example.com")
     *                 )
     *             )),
     *             @OA\Property(property="pagination", type="object",
     *                 @OA\Property(property="current_page", type="integer", example=1),
     *                 @OA\Property(property="last_page", type="integer", example=5),
     *                 @OA\Property(property="per_page", type="integer", example=10),
     *                 @OA\Property(property="total", type="integer", example=45)
     *             )
     *         )
     *     )
     * )
     */
    public function getAllPosts(Request $request)
    {
        $perPage = $request->get('per_page', 10);
        $posts = Post::with(['user', 'category'])->orderBy('id', 'desc')->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $posts->items(),
            'pagination' => [
                'current_page' => $posts->currentPage(),
                'last_page' => $posts->lastPage(),
                'per_page' => $posts->perPage(),
                'total' => $posts->total(),
            ]
        ], 200);
    }

    /**
     * @OA\Post(
     *     path="/api/posts",
     *     summary="Create a new post",
     *     description="Create a new blog post with image upload and optional category assignment. All posts are automatically published upon creation. Requires authentication with Sanctum token.",
     *     tags={"Posts"},
     *     security={{"sanctum":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         description="Post data with image file",
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 required={"title","content","image"},
     *                 @OA\Property(property="title", type="string", maxLength=255, example="Building Modern Web Applications with Laravel and Next.js", description="Post title (required, max 255 characters)"),
     *                 @OA\Property(property="content", type="string", example="In this comprehensive guide, we'll explore how to build full-stack web applications using Laravel for the backend API and Next.js for the frontend. We'll cover authentication, CRUD operations, image uploads, and more.", description="Post content (required, supports long text)"),
     *                 @OA\Property(property="image", type="string", format="binary", description="Post featured image file (required, accepts: jpeg, jpg, png, gif, webp, max size: 2MB)"),
     *                 @OA\Property(property="category_id", type="integer", example=1, description="Category ID to assign this post to (optional, must exist in categories table)")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Post created successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Post created successfully"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="title", type="string", example="My First Blog Post"),
     *                 @OA\Property(property="content", type="string", example="This is the content of my first blog post"),
     *                 @OA\Property(property="image", type="string", example="posts/abc123xyz.jpg"),
     *                 @OA\Property(property="category_id", type="integer", example=1, nullable=true),
     *                 @OA\Property(property="user_id", type="integer", example=1),
     *                 @OA\Property(property="published_at", type="string", format="date-time", example="2025-11-05T12:00:00.000000Z"),
     *                 @OA\Property(property="created_at", type="string", format="date-time", example="2025-11-05T12:00:00.000000Z"),
     *                 @OA\Property(property="updated_at", type="string", format="date-time", example="2025-11-05T12:00:00.000000Z")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated - Missing or invalid token",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Unauthenticated.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error - Invalid or missing required fields",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="The title field is required. (and 2 more errors)"),
     *             @OA\Property(property="errors", type="object",
     *                 @OA\Property(property="title", type="array", @OA\Items(type="string", example="The title field is required.")),
     *                 @OA\Property(property="content", type="array", @OA\Items(type="string", example="The content field is required.")),
     *                 @OA\Property(property="image", type="array", @OA\Items(type="string", example="The image must be a file of type: jpeg, jpg, png, gif.")),
     *                 @OA\Property(property="category_id", type="array", @OA\Items(type="string", example="The selected category id is invalid."))
     *             )
     *         )
     *     )
     * )
     */
    public function createPost(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'image' => 'required|image',
            'category_id' => 'nullable|exists:categories,id',
        ]);

        $imagePath = $request->file('image')->store('posts', 'public');

        $post = Post::create([
            'title' => $request->title,
            'content' => $request->content,
            'image' => $imagePath,
            'category_id' => $request->category_id,
            'user_id' => Auth::id(),
            'published_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Post created successfully',
            'data' => $post
        ], 201);
    }

    /**
     * @OA\Get(
     *     path="/api/posts/{id}",
     *     summary="Get a specific post",
     *     description="Retrieve a single blog post by its ID, including author information",
     *     tags={"Posts"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Post ID",
     *         required=true,
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Post retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="title", type="string", example="My First Blog Post"),
     *                 @OA\Property(property="content", type="string", example="This is the content of my first blog post"),
     *                 @OA\Property(property="image", type="string", example="posts/abc123.jpg"),
     *                 @OA\Property(property="category_id", type="integer", example=1, nullable=true),
     *                 @OA\Property(property="user_id", type="integer", example=1),
     *                 @OA\Property(property="published_at", type="string", format="date-time", example="2025-11-05T12:00:00.000000Z"),
     *                 @OA\Property(property="created_at", type="string", format="date-time", example="2025-11-05T12:00:00.000000Z"),
     *                 @OA\Property(property="updated_at", type="string", format="date-time", example="2025-11-05T12:00:00.000000Z"),
     *                 @OA\Property(property="user", type="object", description="Post author information",
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="name", type="string", example="John Doe"),
     *                     @OA\Property(property="email", type="string", example="john@example.com"),
     *                     @OA\Property(property="email_verified_at", type="string", format="date-time", nullable=true),
     *                     @OA\Property(property="created_at", type="string", format="date-time"),
     *                     @OA\Property(property="updated_at", type="string", format="date-time")
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Post not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Post not found")
     *         )
     *     )
     * )
     */
    public function getPostById(string $id)
    {
        $post = Post::with(['user', 'category'])->find($id);
        if (!$post) {
            return response()->json([
                'success' => false,
                'message' => 'Post not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $post
        ], 200);
    }

    /**
     * @OA\Post(
     *     path="/api/posts/{id}",
     *     summary="Update a post",
     *     description="Update an existing blog post. All fields are optional - only send the fields you want to update. Requires authentication.",
     *     tags={"Posts"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Post ID to update",
     *         required=true,
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         description="Fields to update (all optional)",
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 @OA\Property(property="title", type="string", maxLength=255, example="Updated Post Title", description="New post title (optional, max 255 characters)"),
     *                 @OA\Property(property="content", type="string", example="Updated post content here", description="New post content (optional)"),
     *                 @OA\Property(property="image", type="string", format="binary", description="New post image (optional, jpeg/png/jpg/gif, max 2MB). Will replace existing image."),
     *                 @OA\Property(property="category_id", type="integer", example=1, description="Category ID (optional)"),
     *                 @OA\Property(property="_method", type="string", example="PATCH", description="HTTP method override - use 'PATCH' for proper RESTful update")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Post updated successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Post updated successfully"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="title", type="string", example="Updated Post Title"),
     *                 @OA\Property(property="content", type="string", example="Updated post content"),
     *                 @OA\Property(property="image", type="string", example="posts/xyz789.jpg"),
     *                 @OA\Property(property="category_id", type="integer", example=1, nullable=true),
     *                 @OA\Property(property="user_id", type="integer", example=1),
     *                 @OA\Property(property="published_at", type="string", format="date-time", example="2025-11-05T12:00:00.000000Z"),
     *                 @OA\Property(property="created_at", type="string", format="date-time", example="2025-11-05T12:00:00.000000Z"),
     *                 @OA\Property(property="updated_at", type="string", format="date-time", example="2025-11-05T13:30:00.000000Z")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated - Missing or invalid token",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Unauthenticated.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Post not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Post not found")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error - Invalid field values",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="The given data was invalid."),
     *             @OA\Property(property="errors", type="object",
     *                 @OA\Property(property="title", type="array", @OA\Items(type="string", example="The title must not exceed 255 characters.")),
     *                 @OA\Property(property="image", type="array", @OA\Items(type="string", example="The image must be an image file."))
     *             )
     *         )
     *     )
     * )
     */
    public function update(Request $request, string $id)
    {
        $post = Post::find($id);

        if (!$post) {
            return response()->json([
                'success' => false,
                'message' => 'Post not found'
            ], 404);
        }

        $request->validate([
            'title' => 'sometimes|string|max:255',
            'content' => 'sometimes|string',
            'image' => 'sometimes|image',
            'category_id' => 'nullable|exists:categories,id',
        ]);

        $updateData = $request->only(['title', 'content', 'category_id']);
  
        // Handle image upload
        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($post->image) {
                \Storage::disk('public')->delete($post->image);
            }
            $updateData['image'] = $request->file('image')->store('posts', 'public');
        }
     
        $post->update($updateData);

        return response()->json([
            'success' => true,
            'message' => 'Post updated successfully',
            'data' => $post
        ], 200);
    }

    /**
     * @OA\Delete(
     *     path="/api/posts/{id}",
     *     summary="Delete a post",
     *     description="Permanently delete a blog post by ID. This action cannot be undone. Requires authentication.",
     *     tags={"Posts"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Post ID to delete",
     *         required=true,
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Post deleted successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Post deleted successfully")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated - Missing or invalid token",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Unauthenticated.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Post not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Post not found")
     *         )
     *     )
     * )
     */
    public function destroy(string $id)
    {
        $post = Post::find($id);

        if (!$post) {
            return response()->json([
                'success' => false,
                'message' => 'Post not found'
            ], 404);
        }

        $post->delete();

        return response()->json([
            'success' => true,
            'message' => 'Post deleted successfully'
        ], 200);
    }
}
