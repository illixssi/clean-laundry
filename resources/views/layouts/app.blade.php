<!DOCTYPE html>
<html lang="en"> <!--begin::Head-->

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <title>Clean Laundry</title><!--begin::Primary Meta Tags-->
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="title" content="AdminLTE v4 | Dashboard">
    <meta name="author" content="ColorlibHQ">
    <meta name="description"
        content="AdminLTE is a Free Bootstrap 5 Admin Dashboard, 30 example pages using Vanilla JS.">
    <meta name="keywords"
        content="bootstrap 5, bootstrap, bootstrap 5 admin dashboard, bootstrap 5 dashboard, bootstrap 5 charts, bootstrap 5 calendar, bootstrap 5 datepicker, bootstrap 5 tables, bootstrap 5 datatable, vanilla js datatable, colorlibhq, colorlibhq dashboard, colorlibhq admin dashboard">
    <!--end::Primary Meta Tags--><!--begin::Fonts-->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fontsource/source-sans-3@5.0.12/index.css"
        integrity="sha256-tXJfXfp6Ewt1ilPzLDtQnJV4hclT9XuaZUKyUvmyr+Q=" crossorigin="anonymous">
    <!--end::Fonts--><!--begin::Third Party Plugin(OverlayScrollbars)-->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/overlayscrollbars@2.3.0/styles/overlayscrollbars.min.css"
        integrity="sha256-dSokZseQNT08wYEWiz5iLI8QPlKxG+TswNRD8k35cpg=" crossorigin="anonymous">
    <!--end::Third Party Plugin(OverlayScrollbars)--><!--begin::Third Party Plugin(Bootstrap Icons)-->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.min.css"
        integrity="sha256-Qsx5lrStHZyR9REqhUF8iQt73X06c8LGIUPzpOhwRrI=" crossorigin="anonymous">
    <!--end::Third Party Plugin(Bootstrap Icons)--><!--begin::Required Plugin(AdminLTE)-->
    <link rel="stylesheet" href="{{ asset('css/adminlte.css') }}"><!--end::Required Plugin(AdminLTE)-->

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/jsvectormap@1.5.3/dist/css/jsvectormap.min.css"
        integrity="sha256-+uGLJmmTKOqBr+2E6KDYs/NRsHxSkONXFHUL0fy2O/4=" crossorigin="anonymous">

    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
    <link rel="stylesheet" href="{{ asset('css/custom.css') }}">
    @stack('styles')
</head> <!--end::Head--> <!--begin::Body-->

<style>
    @media print {
        body {
            width: 75mm;
            margin: 0;
            font-family: 'Courier New', Courier, monospace;
            font-size: 8px;
            color: black;
        }

        .container {
            text-align: center;
        }

        h1 {
            font-size: 12px;
            margin-bottom: 2px;
        }

        table {
            width: 100%;
            margin-top: 5px;
            border-collapse: collapse;
            border: none !important;
        }

        th,
        td {
            padding: 3px;
            text-align: left;
            border: none !important;
        }

        th {
            font-size: 12px;
        }

        td {
            font-size: 10px;
        }

        td:last-child {
            text-align: right;
        }

        .total {
            text-align: right;
            font-weight: bold;
            margin-top: 5px;
            font-size: 10px;
        }

        footer {
            text-align: center;
            margin-top: 5px;
        }

        .customer-data {
            text-align: left;
        }
    }
</style>

<body class="layout-fixed sidebar-expand-lg">
    <div class="app-wrapper">
        @yield('content')
    </div>

    <div class="modal fade" id="feedbackModal" tabindex="-1" aria-labelledby="feedbackModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="feedbackModalLabel">
                        {{ session('modal-tr-print') ? 'Sukses' : (session('modal-success') ? 'Sukses' : 'Gagal') }}
                    </h5>
                </div>
                <div class="modal-body">
                    {{ session('modal-tr-print') ?? session('modal-success') ?? session('modal-error') }}
                </div>
                <div class="modal-footer">
                    @if(session('modal-tr-print'))
                    <button type="button" class="btn btn-secondary" data-dismiss="modal" id="modalBackButton">
                        Kembali
                    </button>
                    <button type="button" class="btn btn-primary" id="modalPrintButton">
                        Cetak Invoice
                    </button>
                    @else
                    <button type="button" class="btn btn-primary" id="modalOkButton" data-dismiss="modal">
                        {{ session('modal-success') ? 'Ok' : 'Tutup' }}
                    </button>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div id="print-area" style="display: none;">
        <div class="container">
            <img src="{{ asset('assets/img/CleanLaundryLogo.png') }}" alt="Clean Laundry" width="100">

            <h1>Clean Laundry</h1>
            <span style="white-space: pre-line; font-size: 12px">
                Jl. Istiqomah RT01/RW08 No. 41
                Kelurahan Cipadu, Kecamatan Larangan
                Kota Tangerang, Banten
            </span>

            <div class="customer-data">
                <pre style="font-size: 10px; text-align: left; margin: 0;">
<hr>
OrderNo:   <span class="order-number"></span>
Nama:      <span class="customer-name"></span>
Tanggal:   <span class="date"></span>
Jumlah:    <span class="clothes-quantity"></span>
            </pre>
            </div>

            <table>
                <thead>
                    <tr>
                        <th>Layanan</th>
                        <th>Harga</th>
                        <th>Qty</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>

            <div class="total"></div>

        </div>

        <footer>
            <span style="white-space: pre-line; font-size: 9px">
                Terima kasih sudah memakai jasa Clean Laundry
                Semoga Anda puas dengan layanan kami!
            </span>
        </footer>
    </div>


    @stack('scripts')

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/overlayscrollbars@2.3.0/browser/overlayscrollbars.browser.es6.min.js"
        integrity="sha256-H2VM7BKda+v2Z4+DRy69uknwxjyDRhszjXFhsL4gD3w=" crossorigin="anonymous"></script>
    <!--end::Third Party Plugin(OverlayScrollbars)--><!--begin::Required Plugin(popperjs for Bootstrap 5)-->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"
        integrity="sha256-whL0tQWoY1Ku1iskqPFvmZ+CHsvmRWx/PIoEvIeWh4I=" crossorigin="anonymous"></script>
    <!--end::Required Plugin(popperjs for Bootstrap 5)--><!--begin::Required Plugin(Bootstrap 5)-->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.min.js"
        integrity="sha256-YMa+wAM6QkVyz999odX7lPRxkoYAan8suedu4k2Zur8=" crossorigin="anonymous"></script>
    <!--end::Required Plugin(Bootstrap 5)--><!--begin::Required Plugin(AdminLTE)-->
    <script src="{{ asset('js/adminlte.js') }}"></script>
    <!--end::Required Plugin(AdminLTE)--><!--begin::OverlayScrollbars Configure-->

    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.10.2/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.min.js"></script>
    <script>
        const SELECTOR_SIDEBAR_WRAPPER = ".sidebar-wrapper";
        const Default = {
            scrollbarTheme: "os-theme-light",
            scrollbarAutoHide: "leave",
            scrollbarClickScroll: true,
        };
        document.addEventListener("DOMContentLoaded", function() {
            const sidebarWrapper = document.querySelector(SELECTOR_SIDEBAR_WRAPPER);
            if (
                sidebarWrapper &&
                typeof OverlayScrollbarsGlobal?.OverlayScrollbars !== "undefined"
            ) {
                OverlayScrollbarsGlobal.OverlayScrollbars(sidebarWrapper, {
                    scrollbars: {
                        theme: Default.scrollbarTheme,
                        autoHide: Default.scrollbarAutoHide,
                        clickScroll: Default.scrollbarClickScroll,
                    },
                });
            }
        });
    </script> <!--end::OverlayScrollbars Configure--> <!-- OPTIONAL SCRIPTS --> <!-- sortablejs -->
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"
        integrity="sha256-ipiJrswvAR4VAx/th+6zWsdeYmVae0iJuiR+6OqHJHQ=" crossorigin="anonymous"></script>
    <!-- sortablejs -->
    <script>
        const connectedSortables =
            document.querySelectorAll(".connectedSortable");
        connectedSortables.forEach((connectedSortable) => {
            let sortable = new Sortable(connectedSortable, {
                group: "shared",
                handle: ".card-header",
            });
        });

        const cardHeaders = document.querySelectorAll(
            ".connectedSortable .card-header",
        );
        cardHeaders.forEach((cardHeader) => {
            cardHeader.style.cursor = "move";
        });
    </script> <!-- jsvectormap -->
    <script src="https://cdn.jsdelivr.net/npm/jsvectormap@1.5.3/dist/js/jsvectormap.min.js"
        integrity="sha256-/t1nN2956BT869E6H4V1dnt0X5pAQHPytli+1nTZm2Y=" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/jsvectormap@1.5.3/dist/maps/world.js"
        integrity="sha256-XPpPaZlU8S/HWf7FZLAncLg2SAkP8ScUTII89x9D3lY=" crossorigin="anonymous"></script>

    <!-- Bootstrap 4 Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- Toastr -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <!-- AdminLTE App -->
    <script src="https://cdn.jsdelivr.net/npm/admin-lte@3.1/dist/js/adminlte.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.10.2/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@eonasdan/tempus-dominus@6.0.0-beta.6/dist/js/tempus-dominus.min.js"></script>

    @if (session('success'))
    <script>
        $(document).ready(function() {
            $(document).Toasts('create', {
                class: 'bg-success',
                title: 'Sukses',
                body: '{{ session("success", "success")}}',
            });
        });
    </script>
    @elseif (session('error'))
    <script>
        $(document).ready(function() {
            $(document).Toasts('create', {
                class: 'bg-danger',
                title: 'Error',
                body: '{{ session("error") }}',
            });
        });
    </script>
    @endif

    @if(session('modal-success') || session('modal-error') || session('modal-tr-print'))
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            var feedbackModal = new bootstrap.Modal(document.getElementById('feedbackModal'));
            feedbackModal.show();

            var isSuccess = "{{ session('modal-success') ? 'true' : 'false' }}" === 'true';
            var isPrint = "{{ session('modal-tr-print') ? 'true' : 'false' }}" === 'true';
            var redirectRoute = "{{ session('redirectRoute', route('transaksi')) }}";
            var trId = "{{ session('transaction_id') }}";

            if (isPrint) {
                var modalPrintButton = document.getElementById('modalPrintButton');
                var modalBackButton = document.getElementById('modalBackButton')
                if (modalPrintButton) {
                    modalPrintButton.addEventListener('click', function() {
                        fetchTransactionData(trId);
                    });
                }
                if (modalBackButton) {
                    modalBackButton.addEventListener('click', function() {
                        window.location.href = redirectRoute;
                    });
                }
            } else {
                var modalOkButton = document.getElementById('modalOkButton');
                if (modalOkButton) {
                    modalOkButton.addEventListener('click', function() {
                        if (isSuccess) {
                            window.location.href = redirectRoute; // Redirect jika sukses
                        } else {
                            feedbackModal.hide();
                        }
                    });
                }
            }
        });

        function fetchTransactionData(transactionId) {
            fetch(`/transaksi/${transactionId}/print`)
                .then(response => response.json())
                .then(data => {
                    console.log(data);

                    // Logo dan header
                    document.querySelector('#print-area h1').textContent = 'Clean Laundry';

                    // Alamat
                    document.querySelector('#print-area span').textContent = `Jl. Istiqomah RT01/RW08 No. 41
            Kelurahan Cipadu, Kecamatan Larangan
            Kota Tangerang, Banten`;

                    // Data pelanggan
                    document.querySelector('#print-area .customer-name').textContent = data.customer_name;
                    document.querySelector('#print-area .order-number').textContent = data.order_number;
                    document.querySelector('#print-area .date').textContent = data.created_at;
                    document.querySelector('#print-area .clothes-quantity').textContent = `${data.clothes_quantity} pcs`;

                    // Tabel layanan
                    const tbody = document.querySelector('#print-area table tbody');
                    tbody.innerHTML = ''; // Kosongkan isi tabel
                    data.details.forEach(detail => {
                        tbody.innerHTML += `
                                <tr>
                                    <td>${detail.service_name}</td>
                                    <td>Rp ${detail.price.toLocaleString('id-ID', { minimumFractionDigits: 0 })}</td>
                                    <td>${detail.quantity} ${detail.unit}</td>
                                    <td>Rp ${detail.total.toLocaleString('id-ID', { minimumFractionDigits: 0 })}</td>
                                </tr>
                            `;
                    });

                    document.querySelector('#print-area .total').textContent = `Total Harga: Rp ${data.total_price.toLocaleString('id-ID', { minimumFractionDigits: 0 })}`;

                    cetakInvoice();
                })
                .catch(error => {
                    console.error('Error fetching transaction data:', error);
                    alert('Gagal mendapatkan data transaksi.');
                });
        }

        function cetakInvoice() {
            var redirectRoute = "{{ session('redirectRoute', route('transaksi')) }}";
            const printContent = document.getElementById('print-area').innerHTML;
            const originalContent = document.body.innerHTML;

            document.body.innerHTML = printContent;
            window.print();

            document.body.innerHTML = originalContent;
            window.location.href = redirectRoute;
        }
    </script>
    @endif
    <!--end::Script-->
</body><!--end::Body-->

</html>