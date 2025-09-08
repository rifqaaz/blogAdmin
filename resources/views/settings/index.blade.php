@extends('layout.template')
@section('title', 'Settings')
@section('content')
<div class="welcome-card p-4 mb-4 align-items-center text-center">
    <h1>Settings</h1>
    <p class="text-muted">Manage your account and explore features</p>
</div>

<div class="row justify-content-center">
    <div class="col-md-10 col-lg-8">
        <div class="feature-cards col-md-12 d-flex flex-column">
            @can('edit users')
            <div class="card-body shadow-sm hover-shadow p-4 mb-4 rounded">
                <div class="card-icon mb-3">
                    <i class="bi bi-shield-lock" style="font-size:2rem;"></i>
                </div>
                <a class="text-decoration-none text-black" href="{{ route('roles.index') }}">
                    <h3>Role Management</h3>
                    <p>Create, edit, and manage system roles and permissions. Control access levels and security settings.</p>
                </a>
            </div>
            @endcan
            <div class="card-body shadow-sm hover-shadow p-4 mb-4 rounded">
                <div class="card-icon mb-3">
                    <i class="bi bi-people" style="font-size:2rem;"></i>
                </div>
                <a class="text-decoration-none text-black" href="{{ route('users.index') }}">
                    <h3>User Management</h3>
                    <p>Create, edit, and manage user accounts. Assign roles, reset passwords, and monitor user activity.</p>
                </a>
            </div>
        </div>
    </div>
</div>

@endsection