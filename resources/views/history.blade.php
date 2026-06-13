@extends('layouts.app')

@section('title', 'Riwayat Pembelian')

@section('content')

<div class="heading-row">
    <div>
        <p class="eyebrow">Aktivitasmu</p>
        <h1>Riwayat Pertimbangan</h1>
    </div>

<div class="history-actions">
    <div class="stat">
        <strong>{{ $totalAnalyses }}</strong>
        <span>Riwayat tersimpan</span>
    </div>
</div>

</div>

<section class="panel">

@forelse($analyses as $analysis)

    <div class="history-card">

        <div class="history-card-main">
            <h3 class="history-card-title">{{ $analysis->item_name }}</h3>
            <small class="history-card-meta">
                Rp {{ number_format($analysis->item_price, 0, ',', '.') }}
                •
                {{ $analysis->created_at->format('d M Y H:i') }}
            </small>
        </div>

        <div class="history-card-status">
            <span class="badge">{{ str_replace('_', ' ', $analysis->category) }}</span>
            <strong class="history-card-score">{{ $analysis->score }}/100</strong>
        </div>

        <div class="history-card-action">
            <a href="{{ route('analyses.show', $analysis->id) }}" class="button secondary">
                Detail
            </a>
        </div>

    </div>

@empty

    <div class="empty">
        Belum ada riwayat pembelian.
    </div>

@endforelse

<div style="margin-top:1.5rem;">
    {{ $analyses->links() }}
</div>

</section>

@endsection