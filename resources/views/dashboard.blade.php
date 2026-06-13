@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')

{{-- Header --}}
<div class="heading-row">
    <div>
        <p class="eyebrow">Beranda</p>
        <h1>Halo, {{ auth()->user()->name }}</h1>
        <p class="muted">Berikut ringkasan aktivitas pertimbanganmu.</p>
    </div>
    <a href="{{ route('analyses.create') }}" class="button">
    + Pertimbangan Baru
</a>
</div>

{{-- Statistik --}}
<div class="stats-grid">
    <div class="stat-card">
        <span class="stat-label">Total Pertimbangan</span>
        <strong class="stat-value">{{ $stats['total'] }}</strong>
    </div>
    <div class="stat-card stat-layak">
        <span class="stat-label">Layak</span>
        <strong class="stat-value">{{ $stats['layak'] }}</strong>
    </div>
    <div class="stat-card stat-kurang">
        <span class="stat-label">Kurang Layak</span>
        <strong class="stat-value">{{ $stats['kurang_layak'] }}</strong>
    </div>
    <div class="stat-card stat-tidak">
        <span class="stat-label">Tidak Layak</span>
        <strong class="stat-value">{{ $stats['tidak_layak'] }}</strong>
    </div>
</div>

<div class="dashboard-grid">

    {{-- Prioritas Pembelian — Line Chart --}}
    <section class="panel">
        <div class="heading-row compact">
            <h2>🏆 Prioritas Pembelian</h2>
        </div>

        @if($priorities->isEmpty())
            <p class="empty">Belum ada data untuk diprioritaskan.</p>
        @else
            <canvas id="priorityChart" height="220"></canvas>

            {{-- Legend ranking di bawah chart --}}
            <div class="priority-legend">
                @foreach($priorities as $i => $item)
                <div class="priority-legend-item">
                    <span class="legend-rank
                        @if($i === 0) rank-gold
                        @elseif($i === 1) rank-silver
                        @elseif($i === 2) rank-bronze
                        @else rank-default @endif">
                        #{{ $i + 1 }}
                    </span>
                    <span class="legend-name">{{ $item->item_name }}</span>
                    <span class="badge">{{ str_replace('_', ' ', $item->category) }}</span>
                    <strong class="legend-score">{{ $item->score }}</strong>
                </div>
                @endforeach
            </div>

            @if($priorities->count() >= 5)
                <a href="{{ route('history') }}" class="see-all">Lihat semua →</a>
            @endif
        @endif
    </section>

    {{-- Aktivitas Terbaru --}}
    <section class="panel">
        <div class="heading-row compact">
            <h2>🕐 Pertimbangan Terbaru</h2>
            <a href="{{ route('history') }}">Lihat semua</a>
        </div>

        @if($recent->isEmpty())
            <p class="empty">Belum ada pertimbangan tersimpan.</p>
        @else
            @foreach($recent as $item)
            <div class="history-row">
                <div>
                    <strong>{{ $item->item_name }}</strong>
                    <small>{{ $item->created_at->format('d/m/Y H:i') }}</small>
                </div>
                <span class="badge">{{ str_replace('_', ' ', $item->category) }}</span>
                <strong>{{ $item->score }}/100</strong>
            </div>
            @endforeach
        @endif
    </section>

</div>

@endsection

@push('scripts')
@if($priorities->isNotEmpty())
<script src="https://cdn.jsdelivr.net/npm/chart.js@4"></script>
<script>
    const priorityLabels = {!! json_encode($priorities->pluck('item_name')) !!};
    const priorityScores = {!! json_encode($priorities->pluck('score')) !!};
    const priorityCategories = {!! json_encode($priorities->pluck('category')) !!};

    const categoryColors = {
        'SANGAT_LAYAK': '#16a34a',
        'LAYAK':        '#2563eb',
        'KURANG_LAYAK': '#d97706',
        'TIDAK_LAYAK':  '#dc2626',
    };

    const pointColors = priorityCategories.map(c => categoryColors[c] || '#9ca3af');

    const ctx = document.getElementById('priorityChart').getContext('2d');

    // Gradient fill di bawah garis
    const gradient = ctx.createLinearGradient(0, 0, 0, 220);
    gradient.addColorStop(0, 'rgba(37, 99, 235, .25)');
    gradient.addColorStop(1, 'rgba(37, 99, 235, 0)');

    new Chart(ctx, {
        type: 'line',
        data: {
            labels: priorityLabels,
            datasets: [{
                label: 'Skor Kelayakan',
                data: priorityScores,
                borderColor: '#2563eb',
                backgroundColor: gradient,
                borderWidth: 3,
                tension: 0.4,
                fill: true,
                pointRadius: 6,
                pointHoverRadius: 8,
                pointBackgroundColor: pointColors,
                pointBorderColor: '#fff',
                pointBorderWidth: 2,
            }]
        },
        options: {
            responsive: true,
            interaction: { intersect: false, mode: 'index' },
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: '#1f2937',
                    padding: 12,
                    cornerRadius: 8,
                    titleFont: { weight: '700' },
                    callbacks: {
                        label: ctx => {
                            const cat = priorityCategories[ctx.dataIndex].replaceAll('_', ' ');
                            return [`Skor: ${ctx.parsed.y}`, `Kategori: ${cat}`];
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    max: 100,
                    grid: { color: '#f3f4f6' },
                    ticks: { stepSize: 20 }
                },
                x: {
                    grid: { display: false },
                    ticks: {
                        callback: function(value) {
                            const label = this.getLabelForValue(value);
                            return label.length > 12 ? label.substring(0, 12) + '…' : label;
                        }
                    }
                }
            }
        }
    });
</script>
@endif
@endpush