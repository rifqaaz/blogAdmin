@extends('layout.template')

@section('title', 'My Profile')
@section('content')
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header bg-white">
                    <h1 class="h4 mb-0">Profile Information</h1>
                </div>
                <div class="card-body">
                    @include('profile.partials.update-info') 
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-header bg-white">
                    <h1 class="h4 mb-0">Update Password</h1>
                </div>
                <div class="card-body">
                    @include('profile.partials.update-password')
                </div>
            </div>

            <div class="card border-danger">
                <div class="card-header bg-white text-danger">
                    <h1 class="h4 mb-0">Delete Account</h1>
                </div>
                <div class="card-body">
                    @include('profile.partials.delete-account')
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
@endsection