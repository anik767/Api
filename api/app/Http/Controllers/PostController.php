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
     *     description="Retrieve a list of all posts",
     *     tags={"Posts"},
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         description="Page number",
     *         required=false,
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Parameter(
     *         name="per_page",
     *         in="query",
     *         description="Number of posts per page",
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
     *                 @OA\Property(property="title", type="string", example="Sample Post"),
     *                 @OA\Property(property="content", type="string", example="This is a sample post content"),
     *                 @OA\Property(property="status", type="string", example="published"),
     *                 @OA\Property(property="published_at", type="string", format="date-time"),
     *                 @OA\Property(property="user", type="object",
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="name", type="string", example="John Doe"),
     *                     @OA\Property(property="email", type="string", example="john@example.com")
     *                 ),
     *                 @OA\Property(property="created_at", type="string", format="date-time"),
     *                 @OA\Property(property="updated_at", type="string", format="date-time")
     *             ))
     *         )
     *     )
     * )
     */
    public function index(Request $request)
    {
        $perPage = $request->get('per_page', 10);
        $posts = Post::with('user')->paginate($perPage);

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
     *     description="Create a new blog post",
     *     tags={"Posts"},
     *     security={{"sanctum":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"title","content"},
     *             @OA\Property(property="title", type="string", example="My First Post"),
     *             @OA\Property(property="content", type="string", example="This is the content of my first post"),
     *             @OA\Property(property="status", type="boolean", example=true)
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
     *                 @OA\Property(property="title", type="string", example="My First Post"),
     *                 @OA\Property(property="content", type="string", example="This is the content of my first post"),
     *                 @OA\Property(property="status", type="boolean", example="true"),
     *                 @OA\Property(property="user_id", type="integer", example=1),
     *                 @OA\Property(property="published_at", type="string", format="date-time", nullable=true),
     *                 @OA\Property(property="created_at", type="string", format="date-time"),
     *                 @OA\Property(property="updated_at", type="string", format="date-time")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Unauthenticated.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="The given data was invalid."),
     *             @OA\Property(property="errors", type="object",
     *                 @OA\Property(property="title", type="array", @OA\Items(type="string")),
     *                 @OA\Property(property="content", type="array", @OA\Items(type="string"))
     *             )
     *         )
     *     )
     * )
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'status' => 'sometimes|boolean',
        ]);

        $post = Post::create([
            'title' => $request->title,
            'content' => $request->content,
            'user_id' => Auth::id(),
            'status' => $request->status ?? true,
            'published_at' => $request->status === true ? now() : null,
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
     *     description="Retrieve a specific post by ID",
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
     *                 @OA\Property(property="title", type="string", example="Sample Post"),
     *                 @OA\Property(property="content", type="string", example="This is a sample post content"),
     *                 @OA\Property(property="status", type="string", example="published"),
     *                 @OA\Property(property="published_at", type="string", format="date-time"),
     *                 @OA\Property(property="user", type="object",
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="name", type="string", example="John Doe"),
     *                     @OA\Property(property="email", type="string", example="john@example.com")
     *                 ),
     *                 @OA\Property(property="created_at", type="string", format="date-time"),
     *                 @OA\Property(property="updated_at", type="string", format="date-time")
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
    public function show(string $id)
    {
        $post = Post::with('user')->find($id);

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
     * @OA\Put(
     *     path="/api/posts/{id}",
     *     summary="Update a post",
     *     description="Update an existing post",
     *     tags={"Posts"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Post ID",
     *         required=true,
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="title", type="string", example="Updated Post Title"),
     *             @OA\Property(property="content", type="string", example="Updated post content"),
     *             @OA\Property(property="status", type="boolean", example=true)
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
     *                 @OA\Property(property="status", type="string", example="published"),
     *                 @OA\Property(property="published_at", type="string", format="date-time"),
     *                 @OA\Property(property="created_at", type="string", format="date-time"),
     *                 @OA\Property(property="updated_at", type="string", format="date-time")
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
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Forbidden",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="You can only update your own posts")
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

        // Check if user owns the post
        if ($post->user_id !== Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'You can only update your own posts'
            ], 403);
        }

        $request->validate([
            'title' => 'sometimes|string|max:255',
            'content' => 'sometimes|string',
            'status' => 'sometimes|boolean',
        ]);

        $updateData = $request->only(['title', 'content', 'status']);
        
        // Set published_at if status is being changed to true
        if (isset($updateData['status']) && $updateData['status'] === true && $post->status !== true) {
            $updateData['published_at'] = now();
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
     *     description="Delete an existing post",
     *     tags={"Posts"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Post ID",
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
     *         response=404,
     *         description="Post not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Post not found")
     *         )
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Forbidden",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="You can only delete your own posts")
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

        // Check if user owns the post
        if ($post->user_id !== Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'You can only delete your own posts'
            ], 403);
        }

        $post->delete();

        return response()->json([
            'success' => true,
            'message' => 'Post deleted successfully'
        ], 200);
    }
}
