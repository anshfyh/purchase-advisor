@extends('layouts.app')

@section('title', 'Masuk - Smart Student Advisor')

@section('content')
<div class="auth-card">
    <p class="eyebrow">Selamat Datang</p>
    <h1>Masuk</h1>
    <p class="muted">Lanjutkan mengelola rencana pembelianmu.</p>

    <form method="POST" action="{{ route('login') }}" class="form-stack">
        @csrf
        <label>Email
            <input type="email" name="email" value="{{ old('email') }}" required autofocus>
            @error('email') <span class="error">{{ $message }}</span> @enderror
        </label>
        <label>Password
            <input type="password" name="password" required>
            @error('password') <span class="error">{{ $message }}</span> @enderror
        </label>
        <label class="check"><input type="checkbox" name="remember" value="1"> Ingat saya</label>
        <button class="button" type="submit">Masuk</button>
    </form>
    <p class="muted center">Belum punya akun? <a href="{{ route('register') }}">Daftar</a></p>
</div>
@endsection
