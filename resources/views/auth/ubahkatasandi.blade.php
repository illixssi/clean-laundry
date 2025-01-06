@extends('layouts.app')

@section('title', 'Ubah Kata Sandi')

@section('content')
<div class="d-flex justify-content-center align-items-center" style="height: 100vh; width: 100vw;">
    <div class="login-box text-center" style="max-width: 400px; width: 100%;">
        <div class="login-logo mb-5">
            <img src="{{ asset('assets/img/CleanLaundryLogo.png') }}" alt="Clean Laundry Logo" style="width: 200px;">
        </div>
        <div class="p-4">
            <div>
                <form action="{{ route('password.update') }}" method="post">
                    @csrf
                    <!-- Kata Sandi Lama -->
                    <div class="input-group mb-3">
                        <input type="password" class="form-control" id="old_password" name="old_password" placeholder="Kata Sandi Lama" required autocomplete="current-password">
                        <span class="input-group-text" onclick="togglePasswordVisibility('old_password', 'oldEyeIcon')" style="cursor: pointer;">
                            <img src="{{ asset('assets/img/Eye.svg') }}" alt="Show Password" width="20" height="20" id="oldEyeIcon">
                        </span>
                    </div>

                    <!-- Kata Sandi Baru -->
                    <div class="input-group mb-3">
                        <input type="password" class="form-control" id="new_password" name="new_password" placeholder="Kata Sandi Baru" required autocomplete="new-password">
                        <span class="input-group-text" onclick="togglePasswordVisibility('new_password', 'newEyeIcon')" style="cursor: pointer;">
                            <img src="{{ asset('assets/img/Eye.svg') }}" alt="Show Password" width="20" height="20" id="newEyeIcon">
                        </span>
                    </div>

                    <!-- Konfirmasi Kata Sandi Baru -->
                    <div class="input-group mb-3">
                        <input type="password" class="form-control" id="confirm_password" name="new_password_confirmation" placeholder="Konfirmasi Kata Sandi Baru" required autocomplete="new-password">
                        <span class="input-group-text" onclick="togglePasswordVisibility('confirm_password', 'confirmEyeIcon')" style="cursor: pointer;">
                            <img src="{{ asset('assets/img/Eye.svg') }}" alt="Show Password" width="20" height="20" id="confirmEyeIcon">
                        </span>
                    </div>

                    <!-- Button -->
                    <div class="row">
                        <div class="col-6">
                            <a href="{{ route('transaksi') }}" class="btn btn-danger">Kembali</a>
                        </div>
                        <div class="col-6">
                            <button type="submit" class="btn btn-primary">Ubah</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    function togglePasswordVisibility(inputId, iconId) {
        const passwordInput = document.getElementById(inputId);
        const eyeIcon = document.getElementById(iconId);

        if (passwordInput.type === 'password') {
            passwordInput.type = 'text';
            eyeIcon.src = "{{ asset('assets/img/Hide.svg') }}"; // Ikon mata tertutup
            eyeIcon.alt = "Hide Password";
        } else {
            passwordInput.type = 'password';
            eyeIcon.src = "{{ asset('assets/img/Eye.svg') }}"; // Ikon mata terbuka
            eyeIcon.alt = "Show Password";
        }
    }
</script>
@endsection