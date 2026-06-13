@extends('layouts.app')

@section('title', 'Statistik')

@section('content')
<div class="heading-row">
    <div>
        <p class="eyebrow">Admin</p>
        <h1>Statistik</h1>
        <p class="muted">Pantau pola barang dan rekomendasi yang paling sering muncul.</p>
    </div>
</div>

<div class="grid four stats">
    <article><span>Total Analisis</span><strong>{{ $totalAnalyses }}</strong></article>
    <article><span>Rata-rata Skor</span><strong>{{ $averageScore }}</strong></article>
    <article><span>Barang Teratas</span><strong>{{ $topItems->first()->item_name ?? '-' }}</strong></article>
    <article><span>Kategori Terbanyak</span><strong>{{ str_replace('_',' ', $categoryStats->first()->category ?? '-') }}</strong></article>
</div>

<div class="admin-grid">

    {{-- Barang Paling Sering Dianalisis — Bar Chart --}}
    <section class="panel">
        <h2>Barang Paling Sering Dianalisis</h2>

        @if($topItems->isEmpty())
            <p class="empty">Belum ada data barang.</p>
        @else
            <canvas id="topItemsChart" height="240"></canvas>
        @endif
    </section>

    {{-- Kategori Terbanyak — Doughnut Chart --}}
    <section class="panel">
        <h2>Kategori Terbanyak</h2>

        @if($categoryStats->isEmpty())
            <p class="empty">Belum ada data kategori.</p>
        @else
            <canvas id="categoryDoughnut" height="240"></canvas>
        @endif
    </section>

</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4"></script>
<script>
    @if($topItems->isNotEmpty())
    // ── Bar Chart: Barang Paling Sering Dianalisis ──────────────
    new Chart(document.getElementById('topItemsChart').getContext('2d'), {
        type: 'bar',
        data: {
            labels: {!! json_encode($topItems->pluck('item_name')) !!},
            datasets: [{
                label: 'Jumlah Analisis',
                data: {!! json_encode($topItems->pluck('total')) !!},
                backgroundColor: '#2563eb',
                borderRadius: 6,
                barThickness: 28,
            }]
        },
        options: {
            indexAxis: 'y',
            responsive: true,
            plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: { label: ctx => `${ctx.parsed.x} kali dianalisis` }
                }
            },
            scales: {
                x: { beginAtZero: true, ticks: { stepSize: 1, precision: 0 } }
            }
        }
    });
    @endif

    @if($categoryStats->isNotEmpty())
    // ── Doughnut Chart: Kategori Terbanyak ──────────────────────
    const categoryColors = {
        'SANGAT_LAYAK':  '#16a34a',
        'LAYAK':         '#2563eb',
        'KURANG_LAYAK':  '#d97706',
        'TIDAK_LAYAK':   '#dc2626',
    };

    const catLabels = {!! json_encode($categoryStats->pluck('category')) !!};
    const catValues = {!! json_encode($categoryStats->pluck('total')) !!};
    const catColors = catLabels.map(c => categoryColors[c] || '#9ca3af');

    new Chart(document.getElementById('categoryDoughnut').getContext('2d'), {
        type: 'doughnut',
        data: {
            labels: catLabels.map(c => c.replaceAll('_', ' ')),
            datasets: [{
                data: catValues,
                backgroundColor: catColors,
                borderWidth: 2,
                borderColor: '#fff',
            }]
        },
        options: {
            responsive: true,
            cutout: '65%',
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: { padding: 16, usePointStyle: true, pointStyle: 'circle' }
                },
                tooltip: {
                    callbacks: {
                        label: ctx => `${ctx.label}: ${ctx.parsed} (${((ctx.parsed / ctx.dataset.data.reduce((a,b)=>a+b,0)) * 100).toFixed(1)}%)`
                    }
                }
            }
        }
    });
    @endif
</script>
@endpush