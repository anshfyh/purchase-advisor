@extends('layouts.app')

@section('title', 'Smart Student Purchase Advisor')

@section('content')
<section class="hero">
    <div>
        <p class="eyebrow">Teman Belanja Mahasiswa</p>
        <h1>Belanja lebih tenang, uang saku tetap aman.</h1>
        <p class="lead">Pertimbangkan setiap pembelian berdasarkan kebutuhan dan kondisi keuanganmu saat ini.</p>
        <div class="actions">
            @auth
                <a class="button" href="{{ auth()->user()->isAdmin() ? route('admin.dashboard') : route('dashboard') }}">Masuk</a>
            @else
                <a class="button" href="{{ route('register') }}">Mulai Sekarang</a>
                <a class="button secondary" href="{{ route('login') }}">Masuk</a>
            @endauth
        </div>
    </div>
    <div class="hero-card purchase-demo">
        <p class="demo-label">Contoh pertimbangan</p>
        <div class="demo-item">
            <div>
                <strong>Charger Laptop</strong>
                <small>Dibutuhkan untuk kuliah</small>
            </div>
            <span>Rp 250.000</span>
        </div>
        <div class="demo-result">
            <span>Rekomendasi</span>
            <strong>Layak dibeli</strong>
            <p>Kebutuhanmu penting dan dana harian tetap terjaga setelah pembelian.</p>
        </div>
    </div>
</section>
@endsection
