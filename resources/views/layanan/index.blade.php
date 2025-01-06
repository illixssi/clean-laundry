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
                            @if(in_array($roleName, ['admin', 'kepala_operasional']))
                            <a href="{{ route('layanan.create') }}" class="button--primary">Tambah Layanan</a>
                            @endif
                            <form action="{{ route('layanan') }}" method="GET" id="searchForm" class="search-container">
                                <input type="text" name="search" class="form-control" placeholder="Search" aria-label="Search" value="{{ $search }}" oninput="delayedSearch()">
                            </form>
                        </div>
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Nama Layanan</th>
                                    <th>Satuan</th>
                                    <th>Harga Per Satuan</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($services as $service)
                                <tr class="align-middle">
                                    <td>{{ $service->service_name }}</td>
                                    <td>{{ $service->unit }}</td>
                                    <td>Rp {{ number_format($service->price, 0, ',', '.') }}</td>
                                    <td>
                                        <div class="icon-actions">
                                            @if(in_array($roleName, ['admin', 'kepala_operasional']))
                                            <a href="{{ route('layanan.edit', $service->id) }}" class="btn btn-sm"><img src="{{ asset('assets/img/Edit.svg') }}" alt="Edit" width="60%" height="60%"></a>
                                            <button class="btn btn-sm" onclick="confirmDelete('{{ $service->id }}')">
                                                <img src="{{ asset('assets/img/Trash.svg') }}" alt="Hapus" width="60%" height="60%">
                                            </button>
                                            @endif
                                        </div>
                                        <div class="action-menu">
                                            <button class="btn btn-sm" data-bs-toggle="dropdown" aria-expanded="false">
                                                <i class="bi bi-three-dots-vertical"></i>
                                            </button>
                                            <ul class="dropdown-menu">
                                                @if(in_array($roleName, ['admin', 'kepala_operasional']))
                                                <li><a class="dropdown-item" href="{{ route('layanan.edit', $service->id) }}">Ubah</a></li>
                                                <li>
                                                    <button type="submit" class="dropdown-item" onclick="confirmDelete('{{ $service->id }}')">Hapus</button>
                                                </li>
                                                @endif
                                            </ul>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="text-center">Tidak ada data layanan</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>

                        <div class="card-footer clearfix pagination--container">
                            {{ $services->links('pagination::bootstrap-4') }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

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
        let deleteServiceId;
        let searchTimeout;

        function confirmDelete(id) {
            const deleteForm = document.getElementById('deleteForm');
            deleteForm.action = `/layanan/${id}`;
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