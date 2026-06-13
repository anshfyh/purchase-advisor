<form method="POST" action="{{ route('profile.update') }}" class="form-stack">
    @csrf
    @method('PATCH')

    <label>Nama
        <input type="text" name="name" value="{{ old('name', auth()->user()->name) }}" required>
        @error('name') <span class="error">{{ $message }}</span> @enderror
    </label>

    <label>Email
        <input type="email" name="email" value="{{ old('email', auth()->user()->email) }}" required>
        @error('email') <span class="error">{{ $message }}</span> @enderror
    </label>

    <div class="form-divider"></div>
    <p class="muted compact-text">Kosongkan password jika tidak ingin menggantinya.</p>

    <label>Password baru
        <input type="password" name="password">
        @error('password') <span class="error">{{ $message }}</span> @enderror
    </label>

    <label>Konfirmasi password baru
        <input type="password" name="password_confirmation">
    </label>

    <button class="button" type="submit" style="width:100%;">Simpan Perubahan</button>
</form>