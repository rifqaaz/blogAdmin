@extends('layout.template')
@section('title', 'Manage Users')

@section('content')
    <!-- Begin Page Content -->
    <div class="container-fluid">

        <!-- Page Heading -->
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800">Manage Users</h1>
            @can('edit users')<a href="#" class="btn btn-primary mb-3" data-toggle="modal" data-target="#create">Create New User</a>@endcan
        </div>
        
        <div class="card shadow mb-4">
            <div class="card-body">
                <div class="table-responsive">
                    @can('edit users')
                    <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Roles</th>
                                <th>Assigned To</th>
                                <th>Creation Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($users as $user)
                            <tr>
                                <td>{{ $user->name }}</td>
                                <td>{{ $user->email }}</td>
                                <td>
                                    {{-- Display the user's roles --}}
                                    @forelse($user->roles as $role)
                                        <span>{{ $role->name }}</span>
                                    @empty
                                        <span class="text-muted">No roles assigned</span>
                                    @endforelse
                                </td>
                                <td>
                                    {{ $user->editor->name ?? $user->admin->name ?? 'None Assigned' }}
                                </td>
                                <td>
                                    {{ $user->created_at->format('d/m/Y') }}
                                </td>
                                <td class="d-flex flex-row">
                                    <div class="dropdown">
                                        <button class="btn btn-sm btn-outline-primary dropdown-toggle me-2" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                            Edit
                                        </button>
                                        <ul class="dropdown-menu">
                                            <li><a class="dropdown-item" href="#" data-toggle="modal" data-target="#edit{{ $user->id }}">Edit</a></li>
                                            {{--@if($user->hasRole('user'))
                                            <li><a class="dropdown-item" href="#" data-toggle="modal" data-target="#assign{{ $user->id }}">Assign Editor</a></li>
                                            @endif--}}
                                            <li><a class="dropdown-item" href="#" data-toggle="modal" data-target="#reset{{ $user->id }}">Reset Password</a></li>
                                        </ul>
                                    </div>
                                    <!-- <a href="#" class="btn btn-outline-primary btn-sm me-2" data-toggle="modal" data-target="#edit{{ $user->id }}">Edit</a>
                                    <a href="#" class="btn btn-outline-secondary btn-sm me-2" data-toggle="modal" data-target="#reset{{ $user->id }}">Reset</a> -->
                                    <form class="me-2" method="POST" action="{{ route('users.destroy', $user) }}"
                                        onsubmit="return confirm('Delete this user permanently?');">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
                                    </form>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    @endcan

                    @can('edit assigned')
                    <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Creation Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($users as $user)
                            <tr>
                                <td>{{ $user->name }}</td>
                                <td>{{ $user->email }}</td>
                                <td>
                                    {{ $user->created_at->format('d/m/Y') }}
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    @endcan
                </div>
            </div>
        </div>

        @foreach($users as $user)
        <div class="modal fade" id="edit{{ $user->id }}" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="staticBackdrop" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header d-flex flex-row justify-content-between">
                        <h5 class="modal-title" id="exampleModalLabel">Edit {{ $user->name }}</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <i aria-hidden="true" class="ki ki-close"></i>
                        </button>
                    </div>

                    <div class="modal-body">
                        <div class="card card-custom">

                            <!--begin::Form-->
                            <form action="{{ route('users.update', $user->id) }}" method="POST">
                                @csrf
                                @method('PUT')
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label for="name" class="form-label">
                                                    <i class="bi bi-file-person text-primary me-1"></i> Name
                                                </label>
                                                <input type="text" id="name" name="name" value="{{ old('name', $user->name) }}"
                                                        class="form-control @error('name') is-invalid @enderror" required>
                                                @error('name')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            <div class="form-group">
                                                <label for="email" class="form-label">
                                                    <i class="bi bi-envelope text-primary me-1"></i> Email
                                                </label>
                                                <input type="email" id="email" name="email" value="{{ old('email', $user->email) }}"
                                                        class="form-control @error('email') is-invalid @enderror" disabled>
                                                @error('email')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            <div class="form-group">
                                                <label for="roles" class="form-label">
                                                    <i class="bi bi-person-vcard text-primary me-1"></i> Role
                                                </label>
                                                <select id="roles" name="roles" required
                                                    class="form-select @error('roles') is-invalid @enderror">
                                                    <option value="">Choose a role</option>
                                                    @foreach($roles as $role)
                                                        <option value="{{ $role->name }}" 
                                                            {{ old('name', $user->hasRole($role)) == $role->name ? 'selected' : '' }}>
                                                            {{ $role->name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                @error('roles')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                            </div>

                                            <div class="form-group" id="editor_group" style="display:none;">
                                                <label for="assigned_to_editor_id" class="form-label">
                                                    <i class="bi bi-pencil-square text-primary me-1"></i> Editor
                                                </label>
                                                <select id="assigned_to_editor_id" name="assigned_to_editor_id" required
                                                    class="form-select role-selector @error('assigned_to_editor_id') is-invalid @enderror">
                                                    <option value="">Choose an editor</option>
                                                    @foreach($allEditors as $editor)
                                                        <option value="{{ $editor->id }}" 
                                                            {{ $user->assigned_to_editor_id == $editor->id ? 'selected' : '' }}>
                                                            {{ $editor->name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                @error('assigned_to_editor_id')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
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

        @foreach($users as $user)
        <div class="modal fade" id="assign{{ $user->id }}" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="staticBackdrop" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header d-flex flex-row justify-content-between">
                        <h5 class="modal-title" id="exampleModalLabel">Assign Editor to {{ $user->name }}</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <i aria-hidden="true" class="ki ki-close"></i>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="card card-custom">

                            <!--begin::Form-->
                            <form action="{{ route('users.assign.editor', $user->id) }}" method="POST">
                                @csrf
                                @method('PATCH')
                                <div class="card-body">
                                    <div class="form-group row">
                                        <div class="col-lg-12">
                                            {{--<label for="assigned_to_editor_id" class="form-label">
                                                <i class="bi bi-person-vcard text-primary me-1"></i> Editor
                                            </label> --}}
                                            <select id="assigned_to_editor_id" name="assigned_to_editor_id" required
                                                class="form-select @error('assigned_to_editor_id') is-invalid @enderror">
                                                <option value="">Choose an editor</option>
                                                @foreach($allEditors as $editor)
                                                    <option value="{{ $editor->id }}" 
                                                        {{ $user->assigned_to_editor_id == $editor->id ? 'selected' : '' }}>
                                                        {{ $editor->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('assigned_to_editor_id')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
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

        @foreach($users as $user)
        <div class="modal fade" id="reset{{ $user->id }}" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="staticBackdrop" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header d-flex flex-row justify-content-between">
                        <h5 class="modal-title" id="exampleModalLabel">Change Password for {{ $user->name }}</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <i aria-hidden="true" class="ki ki-close"></i>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="card card-custom">

                            <!--begin::Form-->
                            <form action="{{ route('users.password.update', $user->id) }}" method="POST">
                                @csrf
                                @method('PATCH')
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-lg-12">
                                            <div class="form-group">
                                                <label for="password" class="form-label">
                                                    <i class="bi bi-key text-primary me-1"></i> New Password
                                                </label>
                                                <input type="password" id="password" name="password" value="{{ old('password') }}"
                                                            class="form-control @error('password') is-invalid @enderror" required>
                                                @error('password')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            
                                            <div class="form-group">
                                                <label for="confirm-password" class="form-label">
                                                    <i class="bi bi-key text-primary me-1"></i> Confirm Password
                                                </label>
                                                <input type="password" id="confirm-password" name="confirm-password"
                                                            class="form-control @error('password') is-invalid @enderror" required>
                                                @error('password')
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
                        <h5 class="modal-title" id="exampleModalLabel">Create New User</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <i aria-hidden="true" class="ki ki-close"></i>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="card card-custom">
                            <!--begin::Form-->
                            <form action="{{ route('users.store') }}" method="POST">
                                @csrf
                                <div class="card-body">                                
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label for="name" class="form-label">
                                                    <i class="bi bi-file-person text-primary me-1"></i> Name
                                                </label>
                                                <input type="text" id="name" name="name" value="{{ old('name') }}"
                                                        class="form-control @error('name') is-invalid @enderror" required>
                                                @error('name')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            <div class="form-group">
                                                <label for="email" class="form-label">
                                                    <i class="bi bi-envelope text-primary me-1"></i> Email
                                                </label>
                                                <input type="email" id="email" name="email" value="{{ old('email') }}"
                                                        class="form-control @error('email') is-invalid @enderror" required>
                                                @error('email')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            <div class="form-group">
                                                <label for="password" class="form-label">
                                                    <i class="bi bi-key text-primary me-1"></i> Password
                                                </label>
                                                <input type="password" id="password" name="password" value="{{ old('password') }}"
                                                        class="form-control @error('password') is-invalid @enderror" required>
                                                @error('password')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            <div class="form-group">
                                                <label for="role" class="form-label">
                                                    <i class="bi bi-person-badge text-primary me-1"></i> Role
                                                </label>
                                                <select id="role" name="role" required
                                                    class="form-select @error('role') is-invalid @enderror">
                                                    <option value="">Select a role</option>
                                                    <option value="editor" {{ old('role') == 'editor' ? 'selected' : '' }}>Editor</option>
                                                    <option value="user" {{ old('role') == 'user' ? 'selected' : '' }}>User</option>
                                                </select>
                                                @error('role')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            <div class="form-group" id="editor_assignment_group" style="display:none;">
                                                <label for="assigned_to_editor_id" class="form-label">
                                                    <i class="bi bi-pencil-square text-primary me-1"></i> Editor
                                                </label>
                                                <select id="assigned_to_editor_id" name="assigned_to_editor_id" required
                                                    class="form-select role-selector @error('assigned_to_editor_id') is-invalid @enderror">
                                                    <option value="">Choose an editor</option>
                                                    @foreach($allEditors as $editor)
                                                        <option value="{{  $editor->id }}" 
                                                            {{ $user->assigned_to_editor_id == $editor->id ? 'selected' : '' }}>
                                                            {{ $editor->name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                @error('assigned_to_editor_id')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
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
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const roleSelect = document.getElementById('role');
            const editorGroup = document.getElementById('editor_assignment_group');

            function toggleEditorGroup() {
                // Show the editor group ONLY if the selected role is "user"
                if (roleSelect.value === 'user') {
                    editorGroup.style.display = 'block';
                } else {
                    editorGroup.style.display = 'none';
                    // Clear the selection when hiding
                    document.getElementById('assigned_to_editor_id').value = '';
                }
            }

            // Run on page load in case of old input
            toggleEditorGroup();
            
            // Run every time the role selection changes
            roleSelect.addEventListener('change', toggleEditorGroup);
        });

        document.addEventListener('DOMContentLoaded', function() {
            const roleSelect = document.getElementById('roles');
            const editorGroup = document.getElementById('editor_group');

            function toggleEditorGroup() {
                // Show the editor group ONLY if the selected role is "user"
                if (roleSelect.value === 'user') {
                    editorGroup.style.display = 'block';
                } else {
                    editorGroup.style.display = 'none';
                    // Clear the selection when hiding
                    document.getElementById('assigned_to_editor_id').value = '';
                }
            }

            // Run on page load in case of old input
            toggleEditorGroup();
            
            // Run every time the role selection changes
            roleSelect.addEventListener('change', toggleEditorGroup);
        });
    </script>
    @endpush
@endsection