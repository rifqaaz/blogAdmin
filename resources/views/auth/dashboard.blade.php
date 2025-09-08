@extends('layout.template')
@section('title', 'User Dashboard')

@section('content')
<div class="welcome-card p-4 mb-4 align-items-center text-center">
    <h1>Welcome to Your Dashboard</h1>
    <p class="text-muted">Manage your account and explore features</p>
</div>

<div class="row justify-content-center">
    <div class="col-md-10 col-lg-8">
        <div class="feature-cards col-md-12 d-flex flex-column">
            <div class="card-body shadow-sm hover-shadow p-4 mb-4 rounded">
                <div class="card-icon mb-3">
                    <i class="bi bi-person-circle" style="font-size:2rem;"></i>
                </div>
                <a class="text-decoration-none text-black" href="{{ route('profile') }}">
                    <h3>User Profile</h3>
                    <p>View and edit your personal information and account settings.</p>
                </a>
            </div>
            <div class="card-body shadow-sm hover-shadow p-4 mb-4 rounded">
                <div class="card-icon mb-3">
                    <i class="bi bi-postcard" style="font-size:2rem;"></i>
                </div>
                <a class="text-decoration-none text-black" href="{{ route('posts.index') }}">
                    <h3>Posts Management</h3>
                    <p>Create, edit, and organize your published content in one place.</p>
                </a>
            </div>
        </div>
    </div>
</div>
    
@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const alerts = document.querySelectorAll('.alert-notification');
        alerts.forEach(alert => {
            setTimeout(() => {
                new bootstrap.Alert(alert).close();
            }, 5000);
        });
    });
</script>
@endpush
@endsection