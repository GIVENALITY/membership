@extends('layouts.app')

@section('title', 'Change Password - Membership MS')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <h5 class="card-header">Change Password</h5>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    @if($errors->any())
                        <div class="alert alert-danger alert-dismissible" role="alert">
                            <ul class="mb-0">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <form action="{{ route('users.password.update') }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="alert alert-info">
                            <h6 class="alert-heading">Password Requirements</h6>
                            <p class="mb-0">Your new password must be at least 8 characters long and should include a mix of letters, numbers, and special characters for better security.</p>
                        </div>

                        <div class="mb-3">
                            <label for="current_password" class="form-label">Current Password *</label>
                            <div class="input-group input-group-merge">
                                <input type="password" id="current_password" class="form-control" name="current_password" 
                                       placeholder="Enter your current password" required />
                                <span class="input-group-text cursor-pointer" onclick="togglePassword('current_password')">
                                    <i class="bx bx-hide"></i>
                                </span>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="new_password" class="form-label">New Password *</label>
                            <div class="input-group input-group-merge">
                                <input type="password" id="new_password" class="form-control" name="new_password" 
                                       placeholder="Enter your new password" required />
                                <span class="input-group-text cursor-pointer" onclick="togglePassword('new_password')">
                                    <i class="bx bx-hide"></i>
                                </span>
                            </div>
                            <div class="form-text">
                                <small>Password strength: <span id="password-strength">Weak</span></small>
                                <div class="progress mt-1" style="height: 4px;">
                                    <div class="progress-bar" id="password-progress" role="progressbar" style="width: 0%"></div>
                                </div>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label for="new_password_confirmation" class="form-label">Confirm New Password *</label>
                            <div class="input-group input-group-merge">
                                <input type="password" id="new_password_confirmation" class="form-control" name="new_password_confirmation" 
                                       placeholder="Confirm your new password" required />
                                <span class="input-group-text cursor-pointer" onclick="togglePassword('new_password_confirmation')">
                                    <i class="bx bx-hide"></i>
                                </span>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('users.profile') }}" class="btn btn-outline-secondary">Back to Profile</a>
                            <button type="submit" class="btn btn-primary">Change Password</button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Security Tips -->
            <div class="card mt-4">
                <h5 class="card-header">Security Tips</h5>
                <div class="card-body">
                    <ul class="list-unstyled mb-0">
                        <li class="mb-2">
                            <i class="bx bx-check-circle text-success me-2"></i>
                            Use a unique password that you don't use elsewhere
                        </li>
                        <li class="mb-2">
                            <i class="bx bx-check-circle text-success me-2"></i>
                            Include a mix of uppercase and lowercase letters
                        </li>
                        <li class="mb-2">
                            <i class="bx bx-check-circle text-success me-2"></i>
                            Add numbers and special characters
                        </li>
                        <li class="mb-2">
                            <i class="bx bx-check-circle text-success me-2"></i>
                            Avoid using personal information like birthdays
                        </li>
                        <li>
                            <i class="bx bx-check-circle text-success me-2"></i>
                            Consider using a password manager for better security
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function togglePassword(inputId) {
    const input = document.getElementById(inputId);
    const icon = input.nextElementSibling.querySelector('i');
    
    if (input.type === 'password') {
        input.type = 'text';
        icon.classList.remove('bx-hide');
        icon.classList.add('bx-show');
    } else {
        input.type = 'password';
        icon.classList.remove('bx-show');
        icon.classList.add('bx-hide');
    }
}

// Password strength checker
document.getElementById('new_password').addEventListener('input', function() {
    const password = this.value;
    const strength = checkPasswordStrength(password);
    const strengthText = document.getElementById('password-strength');
    const progressBar = document.getElementById('password-progress');
    
    strengthText.textContent = strength.text;
    strengthText.className = strength.class;
    progressBar.style.width = strength.percentage + '%';
    progressBar.className = 'progress-bar ' + strength.barClass;
});

function checkPasswordStrength(password) {
    let score = 0;
    let feedback = [];
    
    if (password.length >= 8) score += 1;
    if (password.match(/[a-z]/)) score += 1;
    if (password.match(/[A-Z]/)) score += 1;
    if (password.match(/[0-9]/)) score += 1;
    if (password.match(/[^a-zA-Z0-9]/)) score += 1;
    
    if (score <= 2) {
        return { text: 'Weak', class: 'text-danger', percentage: 25, barClass: 'bg-danger' };
    } else if (score <= 3) {
        return { text: 'Fair', class: 'text-warning', percentage: 50, barClass: 'bg-warning' };
    } else if (score <= 4) {
        return { text: 'Good', class: 'text-info', percentage: 75, barClass: 'bg-info' };
    } else {
        return { text: 'Strong', class: 'text-success', percentage: 100, barClass: 'bg-success' };
    }
}
</script>
@endsection 