<form action="{{ $action }}" method="POST">
    @csrf
    @isset($transaction)
    @method('POST')
    @endisset

    <!-- Field Pelanggan -->
    <div class="d-flex justify-content-between mb-3">
        <div class="w-100 me-3">
            <label for="customerDropdown" class="form-label">Pelanggan</label>
            <div class="dropdown">
                <button type="button" onclick="toggleDropdown('customerDropdown')" class="btn btn-light form-control dropdown-toggle {{ isset($isEdit) && $isEdit ? 'disabled--style' : '' }}" {{ isset($isEdit) && $isEdit ? 'disabled' : '' }}>
                    {{ isset($transaction) && isset($transaction->customer) ? $transaction->customer->name : 'Pilih Pelanggan' }}
                </button>
                <div id="customerDropdown" class="dropdown-menu custom-dropdown {{ isset($isEdit) && $isEdit ? 'disabled--style' : '' }}" style="display: none;" {{ isset($isEdit) && $isEdit ? 'disabled' : '' }}>
                    <input type="text" class="form-control dropdown-search {{ isset($isEdit) && $isEdit ? 'disabled--style' : '' }}" placeholder="Search.." onkeyup="filterFunction('customerDropdown')" {{ isset($isEdit) && $isEdit ? 'disabled' : '' }}>
                    <div>
                        @foreach($customers as $customer)
                        <a href="javascript:void(0);" class="dropdown-item" onclick="selectCustomer('{{ $customer->id }}', '{{ $customer->name }}', '{{ $customer->phone_number }}', '{{ $customer->address }}')">
                            {{ $customer->name }}
                        </a>
                        @endforeach
                    </div>
                </div>
            </div>
            <input type="hidden" name="customer_id" id="customer_id" value="{{ $transaction->customer_id ?? '' }}">
            <div class="mt-4 p-4 border" id="customerDetails">
                @if(isset($transaction->customer))
                <p>{{ $transaction->customer->phone_number }}</p>
                <p>{{ $transaction->customer->address }}</p>
                @endif
            </div>
        </div>

        <!-- Jumlah Pakaian -->
        <div class="w-100 me-3 jumlah--pakaian">
            <label for="clothesQuantity" class="form-label">Jumlah Pakaian</label>
            <div class="input-group">
                <input type="number" class="form-control" id="clothesQuantity" name="clothes_quantity" value="{{ $transaction->clothes_quantity ?? '' }}" placeholder="">
                <span class="input-group-text">pcs</span>
            </div>
        </div>

        <!-- Catatan -->
        <div class="w-100">
            <label for="notes" class="form-label">Catatan</label>
            <textarea class="form-control" id="notes" name="notes" rows="3" placeholder="Masukkan catatan">{{ $transaction->notes ?? '' }}</textarea>
        </div>
    </div>

    <!-- Dropdown Layanan -->
    <div class="d-flex justify-content-between align-items-end mb-3">

        <div class="w-100 me-3">
            <label for="serviceDropdown" class="form-label">Layanan</label>
            <div class="dropdown">
                <button type="button" onclick="toggleDropdown('serviceDropdown')" class="btn btn-light form-control dropdown-toggle">
                    Pilih Layanan
                </button>
                <div id="serviceDropdown" class="dropdown-menu custom-dropdown" style="display: none;">
                    <input type="text" class="form-control dropdown-search" placeholder="Search.." onkeyup="filterFunction('serviceDropdown')">
                    <div>
                        @foreach($services as $service)
                        <a href="javascript:void(0);" class="dropdown-item" onclick="selectService('{{ $service->id }}', '{{ $service->service_name }}', '{{ $service->unit }}', '{{$service->price}}')">
                            {{ $service->service_name }}
                        </a>
                        @endforeach
                    </div>
                </div>
            </div>
            <input type="hidden" name="service_id" id="service_id">
            <input type="hidden" name="price" id="price">
        </div>
        <div class="w-100 me-3">
            <div class="input-group mt-2 table--unit">
                <input type="number" class="form-control" name="quantity" id="quantityInput" placeholder="Kg">
                <span id="unit" class="input-group-text"></span>
                <button type="button" class="btn btn-primary" onclick="addService()">Add</button>
            </div>
        </div>
    </div>

    <!-- Tabel Layanan -->
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Layanan</th>
                <th>Kuantitas/Satuan</th>
                <th>Harga</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody id="serviceTableBody">
            @if(isset($transaction->details) && $transaction->details->isNotEmpty())
            @foreach($transaction->details as $index => $detail)
            <tr data-index="{{ $index }}">
                <td>{{ $detail->service->service_name }}</td>
                <td>{{ $detail->quantity }} {{ $detail->service->unit }}</td>
                <td>Rp {{ number_format($detail->price, 0, ',', '.') }}</td>
                <td>
                    <button type="button" class="btn btn-danger btn-sm" onclick="removeService(this, '{{ $detail->price }}')">Hapus</button>
                </td>

                <input type="hidden" name="details[{{ $index }}][service_id]" value="{{ $detail->service_id }}">
                <input type="hidden" name="details[{{ $index }}][quantity]" value="{{ $detail->quantity }}">
                <input type="hidden" name="details[{{ $index }}][price]" value="{{ $detail->price }}">
            </tr>
            @endforeach
            @endif
        </tbody>
    </table>

    <!-- Total Harga -->
    <div class="d-flex justify-content-end align-items-center mt-4">
        <div class="total-harga">
            <div class="total--harga--text">
                @php
                $existingTotalPrice = isset($transaction->details) ? $transaction->details->sum('price') : 0;
                @endphp
                <input type="hidden" id="totalPriceInput" name="total_price" value="{{ $existingTotalPrice }}">
                Total Harga: <span class="text-primary" id="totalPrice">Rp {{ number_format($existingTotalPrice, 0, ',', '.') }}</span>
            </div>
            <div class="transaksi--button--group">
                <a href="{{ route('transaksi') }}" class="btn btn-danger me-2">Batal</a>
                <button type="submit" class="btn btn-primary">{{ isset($transaction) ? 'Update' : 'Submit' }}</button>
            </div>
        </div>
    </div>

</form>

<!-- CSS -->
<style>
    .custom-dropdown {
        max-height: 200px;
        overflow-y: auto;
        width: 100%;
    }

    .dropdown-search {
        margin-bottom: 5px;
    }

    #customerDetails {
        border: 1px solid #ccc;
        padding: 10px;
        margin-top: 5px;
    }

    .transaksi--button--group,
    .total--harga--text {
        padding: 1vw;
        text-align: right;
    }

    /* Media query untuk layar kecil */
    @media (max-width: 733px) {
        .form--container {
            max-width: 100% !important;
        }

        .d-flex {
            flex-direction: column !important;
        }

        .me-3 {
            margin-right: 0 !important;
        }

        .input-group {
            flex-direction: column !important;
            width: 100% !important;
        }

        .input-group .input-group-text {
            margin-top: 10px;
            width: 100%;
            text-align: center;
        }

        .input-group .form-control {
            width: 100% !important;
        }

        .total-harga {
            width: 100%;
            text-align: center;
            margin-top: 15px;
        }

        .table-responsive {
            overflow-x: auto;
        }

        .jumlah--pakaian span,
        .table--unit span {
            display: none;
        }

        .app-main h3 {
            text-align: center;
        }
    }
</style>

<!-- JavaScript -->
<script>
    let totalPrice = <?= json_encode($existingTotalPrice  ?? 0) ?>;

    function toggleDropdown(dropdownId) {
        // Hide other open dropdowns
        document.querySelectorAll('.custom-dropdown').forEach(dropdown => {
            if (dropdown.id !== dropdownId) {
                dropdown.style.display = 'none';
            }
        });

        // Toggle the selected dropdown
        const dropdown = document.getElementById(dropdownId);
        dropdown.style.display = dropdown.style.display === 'block' ? 'none' : 'block';
    }

    function filterFunction(dropdownId) {
        const input = document.querySelector(`#${dropdownId} .dropdown-search`);
        const filter = input.value.toUpperCase();
        const items = document.querySelectorAll(`#${dropdownId} .dropdown-item`);

        items.forEach(item => {
            const txtValue = item.textContent || item.innerText;
            item.style.display = txtValue.toUpperCase().includes(filter) ? "" : "none";
        });
    }

    function selectCustomer(id, name, phone, address) {
        document.getElementById('customer_id').value = id;
        document.querySelector('button[onclick="toggleDropdown(\'customerDropdown\')"]').innerText = name;
        const customerDetails = document.getElementById('customerDetails');
        customerDetails.style.display = "block";
        customerDetails.innerHTML = `<p>${phone}</p><p>${address}</p>`;
        toggleDropdown('customerDropdown');
    }

    function selectService(id, name, unit, price) {
        document.getElementById('service_id').value = id;
        document.getElementById('unit').textContent = unit;
        document.getElementById('unit').value = unit;
        document.getElementById('price').value = price;
        document.querySelector('button[onclick="toggleDropdown(\'serviceDropdown\')"]').innerText = name;
        toggleDropdown('serviceDropdown');
    }

    function addService() {
        const serviceId = document.getElementById('service_id').value;
        const serviceName = document.querySelector('button[onclick="toggleDropdown(\'serviceDropdown\')"]').innerText
        const quantity = document.getElementById('quantityInput').value;
        const unit = document.getElementById('unit').value;
        const price = document.getElementById('price').value;

        const totalItemPrice = price * quantity;

        if (!serviceId || !quantity) {
            alert('Pilih layanan dan masukkan kuantitas.');
            return;
        }

        const rowIndex = document.querySelectorAll('#serviceTableBody tr').length;
        const newRow = `<tr data-index="${rowIndex}">
            <td>${serviceName}</td>
            <td>${quantity} ${unit}</td>
            <td>Rp ${totalItemPrice.toLocaleString()}</td>
            <td><button type="button" class="btn btn-danger btn-sm" onclick="removeService(this, ${totalItemPrice})">Hapus</td>
            <input type="hidden" name="details[${rowIndex}][service_id]" value="${serviceId}">
            <input type="hidden" name="details[${rowIndex}][quantity]" value="${quantity}">
            <input type="hidden" name="details[${rowIndex}][price]" value="${totalItemPrice}">
        </tr>`;

        document.getElementById('serviceTableBody').insertAdjacentHTML('beforeend', newRow);

        totalPrice += totalItemPrice;
        document.getElementById('totalPrice').textContent = `Rp ${totalPrice.toLocaleString()}`;
        document.getElementById('totalPriceInput').value = totalPrice;

        document.getElementById('quantityInput').value = '';
        document.getElementById('service_id').value = '';
        document.querySelector('button[onclick="toggleDropdown(\'serviceDropdown\')"]').innerText = 'Pilih Layanan'
    }

    function removeService(button, itemPrice) {
        const row = button.closest('tr');
        row.remove();

        totalPrice -= itemPrice;
        updateTotalPriceDisplay();
    }

    function updateTotalPriceDisplay() {
        document.getElementById('totalPrice').textContent = `Rp ${totalPrice.toLocaleString()}`;
        document.getElementById('totalPriceInput').value = totalPrice;
    }

    // Close dropdowns if clicked outside
    window.onclick = function(event) {
        if (!event.target.matches('.dropdown-toggle')) {
            document.querySelectorAll('.custom-dropdown').forEach(dropdown => {
                dropdown.style.display = 'none';
            });
        }
    };

    document.addEventListener('DOMContentLoaded', function() {
        let totalPriceInput = document.getElementById('totalPriceInput');

        // Convert value to a number
        let totalPrice = parseFloat(totalPriceInput.value);
        if (isNaN(totalPrice)) {
            totalPrice = 0;
        }

        totalPriceInput.value = totalPrice;
    });


    // Jika Anda memiliki fungsi untuk mengupdate total harga di halaman, pastikan fungsi tersebut juga mengupdate input ini
    function updateTotalPrice(newTotalPrice) {
        let totalPriceInput = document.getElementById('totalPriceInput');

        // Pastikan newTotalPrice adalah angka, jika tidak, default ke 0
        let totalPrice = parseFloat(newTotalPrice);
        if (isNaN(totalPrice)) {
            totalPrice = 0;
        }

        // Update total harga di input
        totalPriceInput.value = totalPrice;
    }
</script>