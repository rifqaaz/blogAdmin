<?php

namespace App\Http\Controllers\Api;

use App\Models\Post;
use App\Models\User;
use App\Models\Category;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $query = Post::where('is_active', 1);

            // Search Filter
            if ($request->has('search') && !empty($request->search)) {
                $searchTerm = $request->search;
                $query->where('title', 'like', '%' . $searchTerm . '%');
            }

            // Category Filter
            if ($request->has('category') && !empty($request->category)) {
                $categoryId = $request->category;
                $query->where('category_id', $categoryId);
            }

            // Pagination
            $posts = $query->orderBy('id', 'ASC')->paginate(10);

            return response()->json([
                'data' => [
                    'posts' => $posts->items(),
                ],
                'pagination' => [ // Helpful pagination info for the front-end
                    'current_page' => $posts->currentPage(),
                    'per_page' => $posts->perPage(),
                    'total' => $posts->total(),
                    'last_page' => $posts->lastPage(),
                ]
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function myPosts(Request $request) 
    {

        $user = Auth::user(); // or $request->user();

        $posts = $request->user()->posts()
            ->when($request->search, function($query, $search) {
                $query->where('title', 'like', '%' . $search . '%');
            })
            ->when($request->category, function($query, $category) {
                $query->where('category_id', $category);
            })
            ->when($request->has('show_active'), function($query) use ($request) {
                $query->where('is_active', $request->boolean('show_active'));
            })
            ->with('category')
            ->latest()
            ->paginate(12);

        return response()->json([
            'data' => $posts->items(),
            'pagination' => [
                'current_page' => $posts->currentPage(),
                'per_page' => $posts->perPage(),
                'total' => $posts->total(),
                'last_page' => $posts->lastPage(),
            ]
        ], 200); // HTTP 200 OK
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title'       => 'required|string|max:255',
            'body'        => 'required|string',
            'category_id' => 'required|exists:categories,id',
            'image'       => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        // Handle image upload
        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('posts', 'public');
        }

        // Create post with user_id
        $post = posts()->create($validated);

        return response()->json([
            'message' => 'Post created successfully!',
            'data' => $post
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try{

            $posts = Post::where('is_active', 1)
            ->orderBy('id', 'ASC')
            ->where('id', $id)->first();

            return response()->json([
                'data' => [
                    'posts' => $posts,
                ]
            ], 200);


        } catch (\Exception $e)
        {
            return response()->json([
                'message' => $e->getMessage()
            ], 500);

        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
