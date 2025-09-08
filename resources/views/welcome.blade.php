@extends('layout.sign')

@section('title', 'Sign In')
@section('content')
    <div class="sign-container">
        <h1 class="title">Welcome</h1>
        <p class="welcome-message">
            Get started by logging in to your account or registering a new one.
        </p>
        
        <div class="auth-links">
            <a href="{{ route('login') }}" class="auth-link login-link">
                Login
            </a>
            <a href="{{ route('register') }}" class="auth-link register-link">
                Register
            </a>
        </div>

        @if(session('success'))
        <div class="alert alert-success mt-4">
            {{ session('success') }}
        </div>
        @endif
    </div>

    @push('styles')
    <style>
       .welcome-message {
            color: #64748b;
            margin-bottom: 2.5rem;
            font-size: 1.1rem;
            line-height: 1.6;
        }
        .auth-links {
            display: flex;
            gap: 1rem;
            justify-content: center;
        }
        .auth-link {
            padding: 0.75rem 1.5rem;
            border-radius: 8px;
            font-weight: 500;
            text-decoration: none;
            transition: all 0.2s;
        }
        .login-link {
            background-color: var(--bs-btn);
            color: white;
        }
        .login-link:hover {
            background-color: var(--bs-grn);
        }
        .register-link {
            border: 1px solid var(--bs-grn);
            color: var(--bs-grn);
        }
        .register-link:hover {
            background-color: #ecfdeeff;
        } 
    </style>
    @endpush
@endsection