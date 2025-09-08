@extends('layout.template')
@section('title', 'Manage Roles')

@section('content')
<div class="container-fluid d-flex flex-column justify-content-evenly">
    <section>
        <div class="d-flex flex-row justify-content-between">
            <h1>Manage Roles</h1>
            <a href="#" class="btn btn-primary mb-3" data-toggle="modal" data-target="#create">Create New Role</a>
        </div>

        <div class="card shadow mb-4">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Role Name</th>
                                <th>Permissions</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($roles as $role)
                            <tr>
                                <td>{{ $role->name }}</td>
                                <td>
                                    @foreach($role->permissions as $permission)
                                    <span class="badge bg-secondary text-black">{{ $permission->name }}</span>
                                    @endforeach
                                </td>
                                <td class="d-flex flex-row justify-content-evenly">
                                    <a href="#" class="btn btn-sm btn-primary me-3" data-toggle="modal" data-target="#edit{{ $role->id }}">
                                    <i class="bi bi-pencil"></i> Edit
                                    </a>
                                    @notprotected($role->name)
                                    <form action="{{ route('roles.destroy', $role->id) }}" method="POST" onsubmit="return confirm('Delete this role?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm">
                                            <i class="bi bi-trash"></i> Delete
                                        </button>
                                    </form>
                                    @endnotprotected

                                    @protected($role->name)
                                    <span class="badge bg-warning">Protected Role</span>
                                    @endprotected
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>
    
    <section class="mt-5" style="max-width: 800px;">
        <!-- <div class="d-flex flex-row justify-content-between">
            <h1>Manage Permissions</h1>
            <a href="#" class="btn btn-primary mb-3" data-toggle="modal" data-target="#createpermission">Create New Permission</a>
        </div> -->
        <div class="dropdown">
            <a class="btn btn-outline-secondary dropdown-toggle h1 fw-medium" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                Manage Permissions
            </a>

            <ul class="dropdown-menu">
                <li><a class="dropdown-item mb-3" href="#" data-toggle="modal" data-target="#createpermission">Create New Permission</a></li>
            </ul>
        </div>

        <div class="card shadow mb-4 col-md-8 col-lg-6">
            <div class="card-body">
                <ul class="list-group list-group-flush">
                    @foreach($allPermissions as $permission)
                    <li class="list-group-item">
                        <p>{{ $permission->name }}</p>
                    </li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
</section>

    @foreach($roles as $role)
    <div class="modal fade" id="edit{{ $role->id }}" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="staticBackdrop" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Edit Role</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <i aria-hidden="true" class="ki ki-close"></i>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="card card-custom">

                        <!--begin::Form-->
                        <form action="{{ route('roles.update', $role->id) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <div class="card-body">                                
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="title" class="form-label">
                                                <i class="bi bi-card-heading text-primary me-1"></i> Role
                                            </label>
                                            <input type="text" id="name" name="name" value="{{ old('name', $role->name) }}"
                                                    class="form-control @error('name') is-invalid @enderror" required>
                                            @error('name')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="permissions" class="form-label">
                                                <i class="bi bi-star text-primary me-1"></i> Assign Permissions
                                            </label>
                                            <select id="permissions" name="permissions[]" class="form-select @error('permissions') is-invalid @enderror" multiple>
                                                <option value="">Select permissions</option>
                                                @foreach($allPermissions as $permission)
                                                    <option value="{{ $permission->name }}"
                                                        @if(old('permissions') !== null)
                                                            {{ in_array($permission->name, old('permissions', [])) ? 'selected' : '' }}
                                                        @else
                                                            {{ $role->permissions->contains('name', $permission->name) ? 'selected' : '' }}
                                                        @endif>
                                                        {{ $permission->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            <small class="form-text text-muted">Start typing to search. Use Ctrl/Cmd to select multiple.</small>
                                            @error('permissions')
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

    <div class="modal fade" id="create" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="staticBackdrop" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">New Role</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <i aria-hidden="true" class="ki ki-close"></i>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="card card-custom">

                        <!--begin::Form-->
                        <form action="{{ route('roles.store') }}" method="POST">
                            @csrf
                            <div class="card-body">                                
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="title" class="form-label">
                                                <i class="bi bi-card-heading text-primary me-1"></i> Role
                                            </label>
                                            <input type="text" id="name" name="name" value="{{ old('name') }}"
                                                    class="form-control @error('name') is-invalid @enderror" required>
                                            @error('name')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="permissions" class="form-label">
                                                <i class="bi bi-star text-primary me-1"></i> Assign Permission(s)
                                            </label>
                                            <select id="permissions" name="permissions[]" class="form-select @error('permissions') is-invalid @enderror" multiple>
                                                <option value="">Select permissions</option>
                                                @foreach($allPermissions as $permission)
                                                    <option value="{{ $permission->name }}" 
                                                        {{ in_array($permission->name, old('permissions', [])) ? 'selected' : '' }}>
                                                        {{ $permission->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            <small class="form-text text-muted">Use Ctrl/Cmd to select multiple.</small>
                                            @error('permissions')
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
                        </form>
                        <!--end::Form-->
                    </div>


                </div>

            </div>
        </div>
    </div>

    <div class="modal fade" id="createpermission" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="staticBackdrop" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">New Permission</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <i aria-hidden="true" class="ki ki-close"></i>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="card card-custom">

                        <!--begin::Form-->
                        <form action="{{ route('permissions.store') }}" method="POST">
                            @csrf
                            <div class="card-body">                                
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="title" class="form-label">
                                                <i class="bi bi-card-heading text-primary me-1"></i> Permission
                                            </label>
                                            <input type="text" id="name" name="name" value="{{ old('name') }}"
                                                    class="form-control @error('name') is-invalid @enderror" required>
                                            @error('name')
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
                        </form>
                        <!--end::Form-->
                    </div>


                </div>

            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        $(document).ready(function() {
            // Initialize Select2 on the permissions dropdown
            $('#permissions').select2({
                placeholder: "Select one or more permissions",
                allowClear: true, // Adds an 'x' to clear all selections
                width: '100%' // Makes it responsive
            });
        });
    </script>
    @endpush
@endsection