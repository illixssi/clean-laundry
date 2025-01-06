<form action="{{ isset($user) ? route('pengguna.update', $user->id) : route('pengguna.store') }}" method="POST">
    @csrf
    @if (isset($user))
    @method('POST')
    @endif

    <div class="mb-3 row align-items-center">
        <label for="name" class="col-sm-4 col-form-label fw-semibold">Nama</label>
        <div class="col-sm-8">
            <input type="text" class="form-control" id="name" name="name" value="{{ $user->name ?? '' }}" placeholder="Nama" required>
        </div>
    </div>

    <div class="mb-3 row align-items-center">
        <label for="username" class="col-sm-4 col-form-label fw-semibold">Username</label>
        <div class="col-sm-8">
            <input type="text" class="form-control" id="username" name="username" value="{{ $user->username ?? '' }}" placeholder="Username" required>
        </div>
    </div>

    <div class="mb-3 row align-items-center">
        <label for="role_id" class="col-sm-4 col-form-label fw-semibold">Peran</label>
        <div class="col-sm-8">
            <select class="form-select" id="role_id" name="role_id" required>
                <option value="">Pilih Peran</option>
                @foreach($roles as $role)
                <option value="{{ $role->id }}" {{ (isset($user) && $user->role_id == $role->id) ? 'selected' : '' }}>
                    {{ $role->role_name }}
                </option>
                @endforeach
            </select>
        </div>
    </div>

    <div class="mb-3 row align-items-center">
        <label for="phone_number" class="col-sm-4 col-form-label fw-semibold">Nomor Telepon</label>
        <div class="col-sm-8">
            <input type="tel" class="form-control" id="phone_number" name="phone_number" value="{{ $user->phone_number ?? '' }}" placeholder="Nomor Telepon" required>
        </div>
    </div>

    <div class="d-flex justify-content-end mt-4">
        <a href="{{ route('pengguna') }}" class="btn btn-danger me-3" style="width: 100px;">Batal</a>
        <button type="submit" class="btn btn-primary" style="width: 100px;">{{ isset($user) ? 'Update' : 'Submit' }}</button>
    </div>
</form>