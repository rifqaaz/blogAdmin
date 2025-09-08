@extends('layout.change')

@section('title', 'Edit')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header bg-white border-0">
                    <h1 class="h4 mb-0 text-primary">Edit Post</h1>
                </div>

                <div class="card-body">
                    <form action="{{ route('posts.update', $post) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        
                        <!-- Image Upload -->
                        <div class="mb-3">
                            <label for="image" class="form-label">
                                <i class="bi bi-image text-primary me-1"></i> Image
                            </label>
                            <input class="form-control @error('image') is-invalid @enderror"
                                type="file" name="image" accept="jpg,jpeg,png">
                            @error('image')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            @if($post->image)
                                <div class="mt-2">
                                    <img src="{{ asset('storage/' . $post->image) }}" alt="Current image" class="img-thumbnail" style="max-height: 150px">
                                    <div class="form-check mt-2">
                                        <input class="form-check-input" type="checkbox" value="1" name="remove_image">
                                        <label class="form-check-label" for="remove_image">
                                            Remove current image
                                        </label>
                                    </div>
                                </div>
                            @endif
                        </div>

                        <div class="mb-4">
                            <label for="title" class="form-label">
                                <i class="bi bi-card-heading text-primary me-1"></i> Title
                            </label>
                            <input type="text" id="title" name="title" value="{{ old('title', $post->title) }}"
                                    class="form-control @error('title') is-invalid @enderror" required>
                            @error('title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="category" class="form-label">
                                <i class="bi bi-star text-primary me-1"></i> Category
                            </label>
                            <select id="category" name="category_id" required
                                    class="form-select @error('category_id') is-invalid @enderror">
                                <option>Select a category</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ old('category_id', $post->category_id) == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('category_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="body" class="form-label">
                                <i class="bi bi-body-text text-primary me-1"></i>  Content
                            </label>
                            <textarea id="body" name="body" rows="6" 
                                class="form-control @error('body') is-invalid @enderror" required>
                                {{ old('body', $post->body) }}
                            </textarea>
                            @error('body')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <button type="submit" class="btn btn-primary">
                                Update Post
                            </button>
                            <a href="{{ route('posts.show', $post) }}" class="btn btn-outline-secondary me-md-2">
                                Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>