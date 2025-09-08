<div class="mb-5">
    <!-- Email Verification Alert -->
    @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
        <div class="alert alert-warning email-verification-alert mb-4">
            <p class="mb-2">Your email address is unverified.</p>
            <form method="POST" action="{{ route('verification.send') }}">
                @csrf
                <button type="submit" class="btn btn-sm btn-outline-dark">
                    Click here to re-send verification email
                </button>
            </form>
        </div>
    @endif

    <!-- Success Message -->
    @if (session('status') === 'profile-updated')
        <div class="alert alert-success alert-dismissible fade show">
            Profile updated successfully!
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{--
    <form method="POST" action="{{ route('profile.update') }}" id="profile-update-form">
        @csrf
        @method('patch')

        <!-- Name Field -->
        <div class="mb-3">
            <label for="name" class="form-label">
                <i class="bi bi-person"></i> Full Name
            </label>
            <input type="text" class="form-control @error('name') is-invalid @enderror" 
                    id="name" name="name" value="{{ old('name', $user->name) }}" 
                    required autofocus autocomplete="name">
            @error('name')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <!-- Email Field -->
        <div class="mb-4">
            <label for="email" class="form-label">
                <i class="bi bi-envelope"></i> Email Address
            </label>
            <input type="email" class="form-control @error('email') is-invalid @enderror" 
                    id="email" name="email" value="{{ old('email', $user->email) }}" 
                    required autocomplete="email">
            @error('email')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="d-flex align-items-center gap-3">
            <button type="submit" class="btn btn-primary px-4">
                Save Changes
            </button>
            
            <!-- Loading Indicator -->
            <div class="spinner-border text-primary d-none" role="status" id="profile-spinner">
                <span class="visually-hidden">Loading...</span>
            </div>
        </div>
    </form>
    --}}
    
    <div class="mb-3">
        <label for="name" class="form-label">
            <i class="bi bi-person"></i> Full Name
        </label>
        <input type="text" class="form-control" value="{{ old('name', $user->name) }}" disabled>
        
    </div>
    <div class="mb-4">
        <label for="email" class="form-label">
            <i class="bi bi-envelope"></i> Email Address
        </label>
        <input type="text" class="form-control" value="{{ old('email', $user->email) }}" disabled>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Form submission handler
    document.getElementById('profile-update-form').addEventListener('submit', function() {
        document.getElementById('profile-spinner').classList.remove('d-none');
    });
</script>