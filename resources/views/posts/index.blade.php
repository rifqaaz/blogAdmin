@extends('layout.template')

@section('title', 'Posts')
@section('content')

<!-- Categories Section -->
<div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-4 justify-content-evenly">
    <div class="p-4 text-gray-900">
        @if($categories->count())
            <div class="d-flex flex-wrap gap-2 mb-4">
                @foreach($categories as $category)
                    <a href="{{ route('posts.index', ['category' => $category->id]) }}" 
                    class="badge rounded-pill p-3 px-5 text-decoration-none gap-2 me-2 
                    {{ request('category') == $category->id ? 'bg-primary text-white' : 'bg-light text-dark' }}">
                        {{ $category->name }} ({{ $category->posts_count }})
                    </a>
                @endforeach
            </div>
        @else
            <p class="text-muted mb-0">No categories available</p>
        @endif
        
        @if(request('category'))
            <a href="{{ route('posts.index') }}" class="badge bg-danger text-white rounded-pill p-2 px-3 text-decoration-none">
                Clear Filter
            </a>
        @endif
    </div>
</div>

@auth
<div class="mb-4 d-flex justify-content-end align-items-center">
    <!-- Show Inactive Toggle
    <div class="mb-4">
        <a href="{{ request()->fullUrlWithQuery(['show_inactive' => request('show_inactive') ? null : '1']) }}" 
        class="btn btn-sm {{ request('show_inactive') ? 'btn-danger' : 'btn-outline-secondary' }}">
            {{ request('show_inactive') ? 'Hide Inactive Posts' : 'Show Inactive Posts' }}
        </a>
    </div> -->



    <div class="d-flex flex-row align-items-center" style="gap: 10px;">
        <!-- My Posts Button -->
        @cannot('edit users')
        <a href="{{ route('posts.myposts') }}" class="btn btn-sm btn-outline-primary" role="button">My Posts</a>
        @endcannot     
        <!-- New Post Button -->
        @can('create posts')
        <a href="#" class="btn btn-primary btn-sm text-decoration-none" role="button"  data-toggle="modal" data-target="#create">New Post</a>
        @endcan
        <!-- Search Bar -->
        <form action="{{ route('posts.index') }}" method="GET" role="search">
            <div class="input-group">
                <input type="text" class="form-control form-control-sm" name="search" placeholder="Search posts..."
                    value="{{ request('search') }}" aria-label="Search posts">
                <button class="btn btn-sm btn-outline-primary" type="submit" id="button-search">
                    <i class="bi bi-search"></i>
                </button>
            </div>
        </form>
    </div>
</div>
@endauth

<!-- Posts List-->
<div class="container px-0">
    <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3">
        @foreach($posts as $post)
        <div class="col-lg-4 mb-4">
            <div class="card h-100"> <!-- Ensures equal height -->
                <div class="card card-custom">
                    <div class="card-header">
                        <div class="card-title">
                            <span class="card-icon">
                                <i class="flaticon2-chat-1 text-primary"></i>
                            </span>
                            <h3 class="card-label">
                                <a href="#" class="text-decoration-none text-black" data-toggle="modal" data-target="#show{{ $post->id }}">{{ $post->title }}</a>
                            </h3>
                        </div>
                    </div>
                    <div class="card-body d-flex flex-column">
                        <div class="d-flex flex-row justify-content-between">
                            <p>Author: {{ $post->user->name }}</p>
                            <p>Editor: {{ $post->user->editor->name }}</p>
                        </div>
                        <p class="card-text mb-3">{{ Str::limit($post->body, 150) }}</p>
                        <div class="mt-auto"> <!-- Pushes meta to bottom --> 
                            <div class="d-flex justify-content-between align-items-center"> 
                                <span class="badge bg-primary">{{ $post->category->name }}</span> 
                                <small class="text-muted">{{ $post->created_at->format('d/m/Y') }}</small> 
                            </div> 
                        </div>
                    </div>
                    @can('edit assigned')
                    <div class="card-footer d-flex justify-content-between">
                        <div class="d-flex align-items-center" style="gap: 10px;">
                            <a href="#" class="btn btn-sm btn-outline-primary" data-toggle="modal" data-target="#show{{ $post->id }}">View</a>
                                <a href="#" class="btn btn-sm btn-outline-secondary" data-toggle="modal" data-target="#edit{{ $post->id }}">Edit</a>
                                <form class="d-inline" method="POST" action="{{ route('posts.destroy', $post) }}"
                                    onsubmit="return confirm('Delete this post and its image permanently?');">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
                                </form>
                        </div>
                        <!-- Toggle switch  -->
                        <form action="{{ route('posts.toggle-status', $post) }}" method="POST" class="d-flex align-items-center gap-2">
                            @csrf
                            @method('PATCH')
                            <div class="form-check form-switch m-0">
                                <input type="checkbox" class="form-check-input" role="switch" 
                                {{ $post->is_active ? 'checked' : '' }}
                                onChange="this.form.submit()"
                                >
                            </div>
                            <small class="text-muted">{{ $post->is_active ? 'Active' : 'Inactive' }}</small>
                        </form>
                    </div>
                    @endcan
                </div>
            </div>
        </div>
        @endforeach
    </div>

    @foreach($posts as $post)
    <div class="modal fade" id="edit{{ $post->id }}" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="staticBackdrop" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Edit Post</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <i aria-hidden="true" class="ki ki-close"></i>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="card card-custom">

                        <!--begin::Form-->
                        <form action="{{ route('posts.update', $post->id) }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')
                            <div class="card-body">
                                <div class="form-group row">
                                    <div class="col-lg-12">
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
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="title" class="form-label">
                                                <i class="bi bi-card-heading text-primary me-1"></i> Title
                                            </label>
                                            <input type="text" id="title" name="title" value="{{ old('title', $post->title) }}"
                                                    class="form-control @error('title') is-invalid @enderror" required>
                                            @error('title')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
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
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group>
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
                                    </div>
                                </div>

                                <div class="text-center pt-15">
                                    <button type="submit" class="btn btn-primary">
                                        <span class="indicator-label">Update</span>
                                    </button>
                                    <button data-dismiss="modal" type="button" class="btn btn-light ms-3">Close</button>
                                </div>
                            </div>
                        </form>
                        <!--end::Form-->
                    </div>


                </div>

            </div>
        </div>
    </div>
    @endforeach

    @foreach($posts as $post)
    <div class="modal fade" id="show{{ $post->id }}" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="staticBackdrop" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">{{  $post->title }}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <i aria-hidden="true" class="ki ki-close"></i>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="card-custom">
                        <div class="card-body">
                        <!-- Image Section -->
                            <div class="col-md-5 mb-4">
                                <img src="{{ $post->image ? asset('storage/' . $post->image) : asset('images/placeholder.jpg') }}" 
                                        class="img-fluid rounded-start object-fit-cover" alt="{{ $post->title }}">
                            </div>
                        <!-- Post Content -->
                            <div class="post-content">
                                {{  $post->body }}
                            </div>
                        </div>
                        <div class="card-footer text-muted">
                            Created {{ $post->created_at->diffForHumans() }} <br>
                            Approved {{ $post->approved_at?->diffForHumans() ?? 'Not yet approved' }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endforeach

    <div class="modal fade" id="create" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="staticBackdrop" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h6 class="modal-title" id="exampleModalLabel"><b>Create New Post</b></h6>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <i aria-hidden="true" class="ki ki-close"></i>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="card card-custom">

                        <!--begin::Form-->
                        <form class="form" action="{{ route('posts.store') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="card-body">
                                <div class="form-group row">
                                    <div class="col-lg-12">
                                        <label for="image" class="form-label">
                                            <i class="bi bi-image text-primary me-1"></i> Image
                                        </label>
                                        <input class="form-control @error('image') is-invalid @enderror" 
                                            type="file" name="image" accept="jpg,jpeg,png">
                                        @error('image')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
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
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="category_id" class="form-label">
                                                <i class="bi bi-star text-primary me-1"></i> Category
                                            </label>
                                            <select id="category_id" name="category_id" required
                                                class="form-select @error('category_id') is-invalid @enderror">
                                                <option value="">Select a category</option>
                                                @foreach($categories as $category)
                                                    <option value="{{ $category->id }}" 
                                                        {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                                        {{ $category->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('category_id')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror                                                        
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
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
                                    </div>
                                </div>
                                <div class="text-center pt-15">
                                    <button type="submit" class="btn btn-primary">
                                        <span class="indicator-label">Create</span>
                                    </button>
                                    <button data-dismiss="modal" type="button" class="btn btn-light ms-3">Close</button>
                                </div>
                            </div>
                            <!--end::Actions-->
                        </form>
                        <!--end::Form-->
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="d-flex justify-content-between align-items-center">
        {{ $posts->links() }}
    </div>
</div>
@endsection