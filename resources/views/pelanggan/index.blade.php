@extends('layouts.app')

@section('content')
<!-- Memanggil Header -->
@include('layouts.header')

<!-- Memanggil Sidebar -->
@include('layouts.sidebar')

<!-- Content di sini -->
<main class="app-main">
    <div class="app-content">
        <div class="container-fluid">
            <div class="mt-5">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-4 search-actions-container">
                        @if(in_array($roleName, ['admin', 'kepala_operasional', 'kasir']))
                        <a href="{{ route('pelanggan.create') }}" class="button--primary">Tambah Data Pelanggan</a>
                        @endif
                        <form action="{{ route('pelanggan') }}" method="GET" id="searchForm" class="search-container">
                            <input type="text" name="search" class="form-control" placeholder="Search" aria-label="Search" value="{{ $search }}" oninput="delayedSearch()">
                        </form>
                    </div>

                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Nama Pelanggan</th>
                                <th>Nomor Telepon</th>
                                <th>Alamat</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($customers as $customer)
                            <tr class="align-middle">
                                <td>{{ $customer->name }}</td>
                                <td>{{ $customer->phone_number }}</td>
                                <td>{{ $customer->address }}</td>
                                <td>
                                    <div class="icon-actions">
                                        @if(in_array($roleName, ['admin', 'kepala_operasional', 'kasir']))
                                        <a href="{{ route('pelanggan.edit', $customer->id) }}" class="btn btn-sm">
                                            <img src="{{ asset('assets/img/Edit.svg') }}" alt="Edit" width="60%" height="60%">
                                        </a>
                                        @endif
                                        @if(in_array($roleName, ['admin', 'kepala_operasional']))
                                        <button class="btn btn-sm" onclick="confirmDelete('{{ $customer->id }}')">
                                            <img src="{{ asset('assets/img/Trash.svg') }}" alt="Hapus" width="60%" height="60%">
                                        </button>
                                        @endif
                                    </div>
                                    <!-- Tombol three-dots dengan dropdown untuk resolusi kecil -->
                                    <div class="action-menu">
                                        <button class="btn btn-sm" data-bs-toggle="dropdown" aria-expanded="false">
                                            <i class="bi bi-three-dots-vertical"></i>
                                        </button>
                                        <ul class="dropdown-menu">
                                            @if(in_array($roleName, ['admin', 'kepala_operasional', 'kasir']))
                                            <li><a class="dropdown-item" href="{{ route('pelanggan.edit', $customer->id) }}">Ubah</a></li>
                                            @endif
                                            @if(in_array($roleName, ['admin', 'kepala_operasional']))
                                            <li>
                                                <button type="submit" class="dropdown-item" onclick="confirmDelete('{{ $customer->id }}')">Hapus</button>
                                            </li>
                                            @endif
                                        </ul>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="text-center">Tidak ada data pelanggan</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>

                    <!-- Pagination -->
                    <div class="card-footer clearfix pagination--container">
                        {{ $customers->links('pagination::bootstrap-4') }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<script>
    let searchTimeout;

    function delayedSearch() {
        // Clear timeout sebelumnya jika ada
        clearTimeout(searchTimeout);

        // Set timeout baru untuk delay 2 detik
        searchTimeout = setTimeout(() => {
            document.getElementById('searchForm').submit();
        }, 2000); // 2000 ms = 2 detik
    }
</script>

<div class="modal fade" id="confirmDeleteModal" tabindex="-1" aria-labelledby="confirmDeleteLabel">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="confirmDeleteLabel">Konfirmasi</h5>
            </div>
            <div class="modal-body">
                Apakah kamu yakin ingin menghapus data ini?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" data-dismiss="modal">Batal</button>
                <form id="deleteForm" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Hapus</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    let deleteCustomerId;

    function confirmDelete(id) {
        const deleteForm = document.getElementById('deleteForm');
        deleteForm.action = `/pelanggan/${id}`;
        const modal = new bootstrap.Modal(document.getElementById('confirmDeleteModal'));
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