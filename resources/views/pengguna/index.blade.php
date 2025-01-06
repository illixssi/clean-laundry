@extends('layouts.app')
@section('content')
@include('layouts.header')
@include('layouts.sidebar')

<!-- Content di sini -->
<main class="app-main">
    <div class="app-content">
        <div class="container-fluid">
            <div class="mt-5">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-4 search-actions-container">
                        @if(in_array($roleName, ['admin']))
                        <a href="{{ route('pengguna.create') }}" class="button--primary">Tambah Pengguna</a>
                        @endif
                        <form action="{{ route('pengguna') }}" method="GET" id="searchForm" class="search-container">
                            <input type="text" name="search" class="form-control" placeholder="Search" aria-label="Search" value="{{ $search }}" oninput="delayedSearch()">
                        </form>
                    </div>
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Nama</th>
                                <th>Username</th>
                                <th>Peran</th>
                                <th>Nomor Telepon</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($users as $user)
                            <tr class="align-middle">
                                <td>{{ $user->name }}</td>
                                <td>{{ $user->username }}</td>
                                <td>{{ $user->role->role_name ?? 'N/A' }}</td>
                                <td>{{ $user->phone_number }}</td>
                                <td>
                                    <div class="icon-actions">
                                        @if(in_array($roleName, ['admin']))
                                        <a href="{{ route('pengguna.edit', $user->id) }}" class="btn btn-sm"><img src="{{ asset('assets/img/Edit.svg') }}" alt="Edit" width="60%" height="60%"></a>
                                        <!-- Tombol Delete -->
                                        <button class="btn btn-sm" onclick="confirmAction('{{ $user->id }}', 'Apakah kamu yakin ingin menghapus data ini?', '/pengguna/{{ $user->id }}', 'DELETE', 'Hapus')">
                                            <img src="{{ asset('assets/img/Trash.svg') }}" alt="Hapus" width="60%" height="60%">
                                        </button>

                                        <!-- Tombol Reset Password -->
                                        <button class="btn btn-sm" onclick="confirmAction('{{ $user->id }}', 'Apakah kamu yakin ingin mereset password pengguna ini?', '/pengguna/reset-password/{{ $user->id }}', 'POST', 'Reset Password')">
                                            <img src="{{ asset('assets/img/Synchronize.svg') }}" alt="Reset Password" width="60%" height="60%">
                                        </button>
                                        @endif
                                    </div>
                                    <div class="action-menu">
                                        <button class="btn btn-sm" data-bs-toggle="dropdown" aria-expanded="false">
                                            <i class="bi bi-three-dots-vertical"></i>
                                        </button>
                                        <ul class="dropdown-menu">
                                            @if(in_array($roleName, ['admin']))
                                            <li><a class="dropdown-item" href="{{ route('layanan.edit', $user->id) }}">Ubah</a></li>
                                            <li>
                                                <button type="submit" class="dropdown-item" onclick="confirmDelete('{{ $user->id }}')">Hapus</button>
                                            </li>
                                            <li>
                                                <button type="submit" class="dropdown-item" onclick="confirmReset('{{ $user->id }}')">Reset Password</button>
                                            </li>
                                            @endif
                                        </ul>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="text-center">Tidak ada data pengguna</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>

                    <!-- Pagination -->
                    <div class="card-footer clearfix pagination--container">
                        {{ $users->links('pagination::bootstrap-4') }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<div class="modal fade" id="confirmActionModal" tabindex="-1" aria-labelledby="confirmActionLabel">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="confirmActionLabel">Konfirmasi</h5>
            </div>
            <div class="modal-body" id="confirmActionMessage">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" data-dismiss="modal">Batal</button>
                <form id="actionForm" method="POST">
                    @csrf
                    <input type="hidden" name="_method" id="actionMethod" value="DELETE">
                    <button type="submit" class="btn btn-danger" id="confirmButtonText">

                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
 let searchTimeout;
    function confirmAction(id, message, actionUrl, method = 'DELETE', buttonText = 'Hapus') {
        document.getElementById('confirmActionMessage').textContent = message;
        const actionForm = document.getElementById('actionForm');
        actionForm.action = actionUrl;
        document.getElementById('actionMethod').value = method;
        document.getElementById('confirmButtonText').textContent = buttonText;
        const modal = new bootstrap.Modal(document.getElementById('confirmActionModal'));
        modal.show();
    }

    function delayedSearch() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => {
            document.getElementById('searchForm').submit();
        }, 2000);
    }

    document.querySelector('#confirmDeleteModal .btn-primary').addEventListener('click', () => {
            const modalElement = document.getElementById('confirmDeleteModal');
            const modalInstance = bootstrap.Modal.getInstance(modalElement);
            if (modalInstance) {
                modalInstance.hide();
            }
        });

        document.addEventListener('DOMContentLoaded', () => {
            const dropdownToggles = document.querySelectorAll('.btn-sm[data-bs-toggle="dropdown"]');

            dropdownToggles.forEach((toggle) => {
                const dropdownMenu = toggle.nextElementSibling;

                let popperInstance = Popper.createPopper(toggle, dropdownMenu, {
                    placement: 'bottom-start',
                    modifiers: [{
                            name: 'offset',
                            options: {
                                offset: [0, 10], 
                            },
                        },
                        {
                            name: 'preventOverflow',
                            options: {
                                boundary: 'viewport',
                            },
                        },
                    ],
                });

                toggle.addEventListener('click', () => {
                    popperInstance.update();
                });
            });
        });
</script>

@endsection