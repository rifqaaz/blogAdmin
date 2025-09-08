@extends('layout.change')

@section('title', 'Edit')
<div class="container py-4">
    <div class="d-flex justify-content-between mb-4">
        <h1 class="text-primary">{{ $post->title }}</h1>
        <div>
            <a href="{{ route('posts.edit', $post->id) }}" class="btn btn-secondary">Edit</a>
            <form method="POST" class="d-inline" action="{{ route('posts.destroy', $post->id) }}"
            onsubmit="return confirm('Delete this post and its image permanently?');">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger">Delete</button>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
        <!-- Image Section -->
            <div class="col-md-5">
                <img src="{{ $post->image ? asset('storage/' . $post->image) : asset('images/placeholder.jpg') }}" 
                        class="img-fluid rounded-start object-fit-cover" alt="{{ $post->title }}">
            </div>
        <!-- Post Content -->
            <div class="post-content">
                {{  $post->body }}
            </div>
        </div>
        <div class="card-footer text-muted">
            Created {{ $post->created_at->diffForHumans() }}
        </div>
    </div>

    <a href="{{ route('posts.index') }}" class="btn btn-link text-decoration-none mt-3 text-primary">← Back to posts</a>
</div>