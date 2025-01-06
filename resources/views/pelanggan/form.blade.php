<form action="{{ isset($customer) ? route('pelanggan.update', $customer->id) : route('pelanggan.store') }}" method="POST">
    @csrf
    @if (isset($customer))
        @method('POST')
    @endif
    <div class="mb-3 row align-items-center">
        <label for="name" class="col-sm-4 col-form-label fw-semibold">Nama</label>
        <div class="col-sm-8">
            <input type="text" class="form-control" id="name" name="name" value="{{ $customer->name ?? '' }}" placeholder="Nama" required>
        </div>
    </div>

    <div class="mb-3 row align-items-center">
        <label for="phone_number" class="col-sm-4 col-form-label fw-semibold">Nomor Telepon</label>
        <div class="col-sm-8">
            <input type="tel" class="form-control" id="phone_number" name="phone_number" value="{{ $customer->phone_number ?? '' }}" placeholder="+62 812 1234 5678" required>
        </div>
    </div>

    <div class="mb-3 row align-items-center">
        <label for="address" class="col-sm-4 col-form-label fw-semibold">Alamat</label>
        <div class="col-sm-8">
            <textarea class="form-control" id="address" name="address" rows="3" placeholder="Alamat" required>{{ $customer->address ?? '' }}</textarea>
        </div>
    </div>

    <div class="d-flex justify-content-center mt-4">
        <a href="{{ route('pelanggan') }}" class="btn btn-danger me-3" style="width: 100px;">Batal</a>
        <button type="submit" class="btn btn-primary" style="width: 100px;">{{ isset($customer) ? 'Update' : 'Submit' }}</button>
    </div>
</form>
