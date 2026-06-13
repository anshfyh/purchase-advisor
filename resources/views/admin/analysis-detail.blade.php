@extends('layouts.app')

@section('title', 'Detail Analisis')

@section('content')
<div class="heading-row">
    <div>
        <p class="eyebrow">Riwayat Analisis</p>
        <h1>{{ $analysis->item_name }}</h1>
        <p class="muted">Dibuat oleh {{ $analysis->user->name }} pada {{ $analysis->created_at->format('d/m/Y H:i') }}.</p>
    </div>
    <a class="button secondary" href="{{ route('admin.analyses') }}">← Kembali</a>
</div>

<section class="panel">
    <div class="score-card">
        <div>
            <span>Tingkat Kelayakan</span>
            <strong>{{ $analysis->score }}/100</strong>
        </div>
        <b>{{ str_replace('_', ' ', $analysis->category) }}</b>
    </div>
</section>

<div class="admin-grid">
    <section class="panel">
        <h2>Ringkasan</h2>
        <div class="detail-list">
            <div><span>User</span><strong>{{ $analysis->user->name }}</strong></div>
            <div><span>Email</span><strong>{{ $analysis->user->email }}</strong></div>
            <div><span>Skor</span><strong>{{ $analysis->score }}/100</strong></div>
            <div><span>Kategori</span><strong>{{ str_replace('_', ' ', $analysis->category) }}</strong></div>
        </div>
    </section>
    <section class="panel">
        <h2>Data Pembelian</h2>
        <div class="detail-list">
            <div><span>Uang saku bulanan</span><strong>Rp {{ number_format($analysis->monthly_allowance, 0, ',', '.') }}</strong></div>
            <div><span>Uang saat ini</span><strong>Rp {{ number_format($analysis->current_money, 0, ',', '.') }}</strong></div>
            <div><span>Harga barang</span><strong>Rp {{ number_format($analysis->item_price, 0, ',', '.') }}</strong></div>
            <div><span>Kebutuhan</span><strong>{{ $analysis->need_level }}/10</strong></div>
            <div><span>Hari ke uang saku berikutnya</span><strong>{{ $analysis->days_until_allowance }} hari</strong></div>
            <div><span>Sisa Uang</span><strong>{{ $analysis->remaining_percentage }}%</strong></div>
        </div>
    </section>
</div>

<section class="panel">
    <h2>✨ Rekomendasi AI</h2>

    @if($analysis->ai_recommendation)
        <div style="
            background:#f0f7ff;
            border-left:4px solid #1a73e8;
            padding:1rem;
            border-radius:.5rem;
            line-height:1.8;
        ">
            {{ $analysis->ai_recommendation }}
        </div>
    @else
        <div class="empty">
            Belum ada rekomendasi AI untuk pertimbangan ini.
        </div>
    @endif
</section>
@endsection