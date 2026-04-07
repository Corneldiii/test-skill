<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PostController extends Controller
{
    use AuthorizesRequests;

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $posts = Post::active()
            ->with('user')
            ->paginate(20);

        return response()->json([
            'data' => $posts,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        if (! Auth::check()) {
            return redirect('/login');
        }

        return 'posts.create';
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->authorize('create', Post::class);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'published_at' => 'nullable|date',
            'is_draft' => 'boolean',
        ]);

        $validated['user_id'] = Auth::id();

        $post = Post::create($validated);

        return response()->json([
            'message' => 'Post created',
            'post' => $post,
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Post $post)
    {
        if ($post->is_draft || $post->published_at > now()) {
            abort(404);
        }

        return response()->json([
            'data' => $post->load('user'),
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Post $post)
    {
        $this->authorize('update', $post);

        return 'posts.edit';
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Post $post)
    {
        $this->authorize('update', $post);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'published_at' => 'nullable|date',
            'is_draft' => 'boolean',
        ]);

        $post->update($validated);

        return response()->json([
            'message' => 'Post updated',
            'post' => $post,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Post $post)
    {
        $this->authorize('delete', $post);

        $post->delete();

        return response()->json([
            'message' => 'Post deleted',
        ]);
    }
}
