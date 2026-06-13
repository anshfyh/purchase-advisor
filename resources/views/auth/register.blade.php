@extends('layouts.app')

@section('title', 'Daftar - Smart Student Advisor')

@section('content')
<div class="auth-card">
    <p class="eyebrow">Akun Baru</p>
    <h1>Daftar</h1>
    <p class="muted">Buat akun untuk mulai mengatur rencana belanjamu.</p>

    <form method="POST" action="{{ route('register') }}" class="form-stack">
        @csrf
        <label>Nama
            <input type="text" name="name" value="{{ old('name') }}" required autofocus>
            @error('name') <span class="error">{{ $message }}</span> @enderror
        </label>
        <label>Email
            <input type="email" name="email" value="{{ old('email') }}" required>
            @error('email') <span class="error">{{ $message }}</span> @enderror
        </label>
        <label>Password
            <input type="password" name="password" required>
            @error('password') <span class="error">{{ $message }}</span> @enderror
        </label>
        <label>Konfirmasi Password
            <input type="password" name="password_confirmation" required>
        </label>
        <button class="button" type="submit">Buat Akun</button>
    </form>
</div>
@endsection
