<aside class="app-sidebar bg-body-secondary shadow" data-bs-theme="dark">
    <div class="sidebar-brand">
        <a href="./home" class="brand-link">
            <img src={{asset('assets/img/CleanLaundryLogo.png')}} alt="Clean Laundry Logo" class="brand-image">
        </a>
    </div>
    <div class="sidebar-wrapper">
        <nav class="sidebar--nav--container">
            <ul class="nav sidebar-menu flex-column" data-lte-toggle="treeview" role="menu" data-accordion="false">
                <div class="nav--item--container">
                    <div class="upper--nav--item">
                        @php
                        // Mengambil token dari cookie
                        $token = request()->cookie('user_token');
                        $roleName = '';

                        if ($token) {
                        $tokenData = json_decode(base64_decode($token), true);

                        // Pengecekan token valid dan role
                        if ($tokenData && isset($tokenData['role_name'])) {
                        $roleName = $tokenData['role_name'];
                        }
                        }
                        @endphp

                        {{-- Transaksi bisa diakses oleh semua role --}}
                        <li class="nav-item {{ Route::currentRouteName() == 'transaksi' ? 'active' : '' }}">
                            <a href="{{ route('transaksi') }}" class="nav-link">
                                <img src="{{ asset('assets/img/Transaksi.svg') }}" alt="Transaksi" width="10%" height="10%">
                                <p>Transaksi</p>
                            </a>
                        </li>

                        {{-- Pelanggan hanya bisa diakses oleh admin, kepala_operasional, dan kasir --}}
                        @if (in_array($roleName, ['admin', 'kepala_operasional', 'kasir']))
                        <li class="nav-item {{ Route::currentRouteName() == 'pelanggan' ? 'active' : '' }}">
                            <a href="{{ route('pelanggan') }}" class="nav-link">
                                <img src="{{ asset('assets/img/Pelanggan.svg') }}" alt="Pelanggan" width="10%" height="10%">
                                <p>Pelanggan</p>
                            </a>
                        </li>
                        @endif

                        {{-- Layanan hanya bisa diakses oleh admin, kepala_operasional, kasir, dan owner --}}
                        @if (in_array($roleName, ['admin', 'kepala_operasional', 'kasir', 'owner']))
                        <li class="nav-item {{ Route::currentRouteName() == 'layanan' ? 'active' : '' }}">
                            <a href="{{ route('layanan') }}" class="nav-link">
                                <img src="{{ asset('assets/img/Layanan.svg') }}" alt="Layanan" width="10%" height="10%">
                                <p>Layanan</p>
                            </a>
                        </li>
                        @endif

                        {{-- Pengguna hanya bisa diakses oleh admin --}}
                        @if ($roleName === 'admin')
                        <li class="nav-item {{ Route::currentRouteName() == 'pengguna' ? 'active' : '' }}">
                            <a href="{{ route('pengguna') }}" class="nav-link">
                                <img src="{{ asset('assets/img/Pengguna.svg') }}" alt="Pengguna" width="10%" height="10%">
                                <p>Pengguna</p>
                            </a>
                        </li>
                        @endif

                        {{-- Laporan hanya bisa diakses oleh admin dan owner --}}
                        @if (in_array($roleName, ['admin', 'owner']))
                        <li class="nav-item {{ Route::currentRouteName() == 'laporan' ? 'active' : '' }}">
                            <a href="{{ route('laporan') }}" class="nav-link">
                                <img src="{{ asset('assets/img/Laporan.svg') }}" alt="Laporan" width="10%" height="10%">
                                <p>Laporan</p>
                            </a>
                        </li>
                        @endif
                    </div>
                    <div class="bottom--nav--item">
                        <li class="nav-item">
                            <a href="{{ route('password.change') }}" class="nav-link">
                                <img src="{{ asset('assets/img/UbahKataSandi.svg') }}" alt="UbahKataSandi" width="10%" height="10%">
                                <p>Ubah Kata Sandi</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                @csrf
                            </form>
                            <a href="#" class="nav-link" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                <img src="{{ asset('assets/img/Keluar.svg') }}" alt="Keluar" width="10%" height="10%">
                                <p>Keluar</p>
                            </a>
                        </li>
                    </div>
                </div>
            </ul>
        </nav>
    </div>
</aside>