@extends('layout.change')

@section('title', 'Create')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header bg-white border-0">
                    <h1 class="h4 mb-0 text-primary">Create New Post</h1>
                </div>

                <div class="card-body">
                    <form action="{{ route('posts.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        
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
                        </div>

                        <!-- Post Title -->
                        <div class="mb-4">
                            <label for="title" class="form-label">
                                <i class="bi bi-card-heading text-primary me-1"></i> Title
                            </label>
                            <input type="text" id="title" name="title" 
                                    class="form-control @error('title') is-invalid @enderror" 
                                    value="{{ old('title') }}"
                                    required>
                            @error('title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Category Selection -->
                        <div class="mb-4">
                            <label for="category_id" class="form-label">
                                <i class="bi bi-star text-primary me-1"></i> Category
                            </label>
                            <select 
                                id="category_id"
                                name="category_id"
                                class="form-select @error('category_id') is-invalid @enderror"
                                required
                            >
                                <option value="">Select a category</option>
                                @foreach($categories as $category)
                                    <option 
                                        value="{{ $category->id }}" 
                                        {{ old('category_id') == $category->id ? 'selected' : '' }}
                                    >
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('category_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Post Content -->
                        <div class="mb-4">
                            <label for="content" class="form-label">
                                <i class="bi bi-body-text text-primary me-1"></i> Content
                            </label>
                            <textarea id="body" 
                                        name="body" 
                                        class="form-control @error('body') is-invalid @enderror" 
                                        rows="6"
                                        required>{{ old('body') }}</textarea>
                            @error('body')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Submit Button -->
                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <button type="submit" class="btn btn-primary">
                                Publish Post
                            </button>
                            <a href="{{ route('posts.index') }}" class="btn btn-outline-secondary me-md-2">
                                Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>