<?php

namespace App\Http\Controllers\Admin;

use App\Models\Post;
use App\Models\Category;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $posts = Post::when(request('search'), function($query) {
                $query->where('title', 'like', '%'.request('search').'%');
            })
            ->when(request('category'), function($query) {
                $query->where('category_id', request('category'));
            })
            ->when(request('show_inactive'), function($query) {
                $query->where('is_active', false);
            }, function($query) {
                $query->active();
            })
            ->with(['category', 'user', 'editor'])
            ->latest()
            ->paginate(12);

        $categories = Category::withCount(['posts' => function($query) {
            $query->active(); // Count only active posts
        }])->get();

        return view('posts.index', compact('posts', 'categories'));
    }

    public function myPosts() 
    {
        $user = Auth::user();
        $posts = Post::when($user->hasRole('editor'), function($query) use ($user) {
                return $query->forEditor($user->id); // Use the scope
            }, function ($query) use ($user) {
                return $query->where('user_id', $user->id);
            })
            ->when(request('search'), function($query) {
                $query->where('title', 'like', '%'.request('search').'%');
            })
            ->when(request('category'), function($query) {
                $query->where('category_id', request('category'));
            })
            ->when(request('show_active'), function($query) {
                $query->where('is_active', true);
            }, function($query) {
                $query->where('is_active', false);
            })
            ->with('category')
            ->latest()
            ->paginate(12);

        $categories = Category::withCount(['posts' => function($query) {
            $query->active(); // Count only active posts
        }])->get();

        // Return the view with the posts
        return view('posts.myposts', compact('posts', 'categories'));
    }

    //Toggle Status
    public function toggleStatus(Post $post) 
    {
        $post->update(['is_active' => !$post->is_active]);
        return back()->with('success', 'Post status updated!');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categories = Category::all();
        return view('posts.create', compact('categories'));
    }

    /**
     * Store a newly created resource in storage(db).
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title'       => 'required|string|max:255',
            'body'        => 'required|string',
            'category_id' => 'required|exists:categories,id', // Ensure category exists
            'image'       => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        // Handle image upload
        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('posts', 'public');
        }

        // Create post with user_id
        auth()->user()->posts()->create($validated);

        return back()->with('success', 'Post created successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show($slug)
    {
        $post = Post::where('slug', $slug)->firstOrFail();
        return view('posts.show', ['post' => $post]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $post = Post::findOrFail($id);
        $categories = Category::all();
        return view('posts.edit', compact('post', 'categories'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        // dd($request->all());
        $validated = $request->validate([
            'title' => 'required|max:255',
            'body' => 'required',
            'category_id' => 'required|exists:categories,id',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'remove_image' => 'sometimes|boolean',
        ]);

        $post = Post::findOrFail($id);
        if ($request->has('remove_image') && $request->remove_image === "1") {
            // Delete old image if exists
            if ($post->image) {
                Storage::delete('public/'.$post->image);
            }
            $validated['image'] = null; // Set image to null
        }  else if ($request->hasFile('image')) {     // Handle image upload/cleanup
            // Delete old image if exists
            if ($post->image) {
                Storage::delete('public/'.$post->image);
            }
            $validated['image'] = $request->file('image')->store('posts', 'public');
        } else {
            unset($validated['image']); // Preserve current image if no update
        }

        $post->update($validated);

        return back()->with('success', 'Post updated!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $post = Post::findOrFail($id);
        $post->delete();

        return redirect()->route('posts.index')->with('success', 'Post deleted successfully!');
    }
}
