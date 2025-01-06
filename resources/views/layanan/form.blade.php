<form action="{{ isset($service) ? route('layanan.update', $service->id) : route('layanan.store') }}" method="POST">
    @csrf
    @if (isset($service))
    @method('POST')
    @endif
    <div class="mb-3 row align-items-center">
        <label for="service_name" class="col-sm-4 col-form-label fw-semibold">Nama Layanan</label>
        <div class="col-sm-8">
            <input type="text" class="form-control" id="service_name" name="service_name" value="{{ $service->service_name ?? '' }}" placeholder="Nama Layanan" required>
        </div>
    </div>

    <div class="mb-3 row align-items-center">
        <label for="unit" class="col-sm-4 col-form-label fw-semibold">Satuan</label>
        <div class="col-sm-8">
            <input type="tel" class="form-control" id="unit" name="unit" value="{{ $service->unit ?? '' }}" placeholder="Satuan" required>
        </div>
    </div>

    <div class="mb-3 row align-items-center">
        <label for="price" class="col-sm-4 col-form-label fw-semibold">Harga Per Satuan</label>
                <div class="col-sm-8">
            <input type="text" class="form-control" id="price" name="price" value="{{ $service->price ?? '' }}" placeholder="Harga Per Satuan" required>
        </div>
    </div>

    <div class="d-flex justify-content-end mt-4">
        <a href="{{ route('layanan') }}" class="btn btn-danger me-3" style="width: 100px;">Batal</a>
        <button type="submit" class="btn btn-primary" style="width: 100px;">{{ isset($service) ? 'Update' : 'Submit' }}</button>
    </div>
</form>