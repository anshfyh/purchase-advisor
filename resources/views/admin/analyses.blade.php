@extends('layouts.app')

@section('title', 'Riwayat Analisis')

@section('content')
<div class="heading-row">
    <div>
        <p class="eyebrow">Admin</p>
        <h1>Riwayat Analisis</h1>
        <p class="muted">Cari, filter, dan tinjau seluruh pertimbangan pembelian pengguna.</p>
    </div>
</div>

<section class="panel filter-panel">
    <form method="GET" action="{{ route('admin.analyses') }}" class="filter-form">
        <label>Cari user atau barang
            <input type="search" name="search" value="{{ request('search') }}" placeholder="Contoh: sepatu, pia, email">
        </label>
        <label>Kategori
            <select name="category">
                <option value="">Semua kategori</option>
                @foreach($categories as $category)
                    <option value="{{ $category }}" @selected(request('category') === $category)>{{ str_replace('_', ' ', $category) }}</option>
                @endforeach
            </select>
        </label>
        <button class="button" type="submit">Terapkan</button>
        <a class="button secondary" href="{{ route('admin.analyses') }}">Reset</a>
    </form>
</section>

<section class="panel">

@forelse($analyses as $analysis)

    <div class="history-card">

        <div class="history-card-main">
            <h3 class="history-card-title">{{ $analysis->item_name }}</h3>
            <small class="history-card-meta">
                {{ $analysis->user->name }} ({{ $analysis->user->email }})
                •
                {{ $analysis->created_at->format('d M Y H:i') }}
            </small>
        </div>

        <div class="history-card-status">
            <span class="badge">{{ str_replace('_', ' ', $analysis->category) }}</span>
            <strong class="history-card-score">{{ $analysis->score }}/100</strong>
        </div>

        <div class="history-card-action">
            <a href="{{ route('admin.analyses.show', $analysis) }}" class="button secondary">
                Detail
            </a>
        </div>

    </div>

@empty

    <div class="empty">
        Data tidak ditemukan.
    </div>

@endforelse

<div style="margin-top:1.5rem;">
    {{ $analyses->links() }}
</div>

</section>
@endsection