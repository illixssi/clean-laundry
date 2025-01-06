@extends('layouts.app')

@section('title', 'Login')

@section('content')
<div class="d-flex justify-content-center align-items-center bg-light" style="height: 100vh; width: 100vw;">
    <div class="d-flex shadow-lg rounded-3 flex-column flex-md-row align-items-stretch" style="max-width: 800px; width: 100%; background-color: #ffffff; border-radius: 10px; height: 100%;">
        <div class="px-4" style="flex: 1; display: flex; flex-direction: column; justify-content: center;">
            <div class="login-logo mb-4 text-center">
                <img src="{{ asset('assets/img/CleanLaundryLogo.png') }}" alt="Clean Laundry Logo" style="width: 150px;">
            </div>
            <h5 class="text-center mb-3 text-primary">Selamat Datang di <b>Clean Laundry</b></h5>
            <div class="mt-4" style="text-align: justify; text-justify: inter-word;">
                <p class="text-muted small">Clean Laundry didirikan pada Januari 2024 untuk memenuhi kebutuhan masyarakat modern yang memerlukan jasa laundry praktis dan berkualitas. Berkomitmen menghadirkan layanan cepat dan hasil terbaik, Clean Laundry menggabungkan teknologi terkini untuk efisiensi operasional dan kepuasan pelanggan.</p>
            </div>
        </div>
        <div class="card p-3 border-0 d-flex align-items-center" style="flex: 1; display: flex; flex-direction: column; justify-content: center; height: 100%;">
            <div class="card-body w-100 d-flex align-items-center justify-content-center" style="height: 100%;">
                <form action="{{ route('login.submit') }}" method="post" class="w-100">
                    @csrf
                    <div class="mb-3">
                        <label for="username" class="form-label">Username</label>
                        <input type="text" id="username" name="username" class="form-control" placeholder="Masukkan Username" required>
                    </div>

                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <div class="input-group">
                            <input type="password" class="form-control" id="password" name="password" placeholder="Masukkan Kata Sandi" required>
                            <span class="input-group-text" onclick="togglePassword()" style="cursor: pointer;">
                                <img src="{{ asset('assets/img/Eye.svg') }}" alt="Show Password" width="20" height="20" id="newEyeIcon">
                            </span>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary w-100 mt-3">Login</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    function togglePassword() {
        const passwordInput = document.getElementById('password');
        const eyeIcon = document.getElementById('eyeIcon');

        if (passwordInput.type === 'password') {
            passwordInput.type = 'text';
            eyeIcon.src = "{{ asset('assets/img/Hide.svg') }}"; // Closed eye icon
            eyeIcon.alt = "Hide Password";
        } else {
            passwordInput.type = 'password';
            eyeIcon.src = "{{ asset('assets/img/Eye.svg') }}"; // Open eye icon
            eyeIcon.alt = "Show Password";
        }
    }
</script>
@endsection