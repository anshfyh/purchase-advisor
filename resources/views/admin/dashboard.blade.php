@extends('layouts.app')

@section('title', 'Panel Admin')

@section('content')
<p class="eyebrow">Admin</p>
<h1>Ringkasan Aktivitas</h1>

{{-- Stat Cards (ringkas, tanpa rata-rata & sangat sesuai) --}}
<div class="grid two stats">
    <article>
        <span>Mahasiswa Terdaftar</span>
        <strong>{{ $usersCount }}</strong>
    </article>
    <article>
        <span>Total Pertimbangan</span>
        <strong>{{ $analysesCount }}</strong>
    </article>
</div>

<div class="admin-grid">

    {{-- Aktivitas Terbaru (5 saja) --}}
    <section class="panel">
        <div class="heading-row compact">
            <h2>Aktivitas Terbaru</h2>
            <a href="{{ route('admin.analyses') }}">Lihat semua</a>
        </div>

        @forelse($analyses as $analysis)
            <div class="history-row admin-row">
                <div>
                    <strong>{{ $analysis->item_name }}</strong>
                    <small>{{ $analysis->user->name }} · {{ $analysis->created_at->format('d/m/Y H:i') }}</small>
                </div>
                <span class="badge">{{ str_replace('_', ' ', $analysis->category) }}</span>
                <strong>{{ $analysis->score }}</strong>
            </div>
        @empty
            <p class="empty">Belum ada aktivitas pembelian.</p>
        @endforelse
    </section>

    {{-- Kategori Terbanyak — Line Chart --}}
    <section class="panel">
        <div class="heading-row compact">
            <h2>Kategori Terbanyak</h2>
        </div>

        @if($categoryStats->isEmpty())
            <p class="empty">Belum ada data kategori.</p>
        @else
            <canvas id="categoryChart" height="220"></canvas>
        @endif
    </section>

</div>
@endsection

@push('scripts')
@if($categoryStats->isNotEmpty())
<script src="https://cdn.jsdelivr.net/npm/chart.js@4"></script>
<script>
    const categoryData = {
        labels: {!! json_encode(
            $categoryStats->keys()->map(fn($c) => str_replace('_', ' ', $c))->values()
        ) !!},
        values: {!! json_encode($categoryStats->values()) !!}
    };

    const ctx = document.getElementById('categoryChart').getContext('2d');

    new Chart(ctx, {
        type: 'line',
        data: {
            labels: categoryData.labels,
            datasets: [{
                label: 'Jumlah Pertimbangan',
                data: categoryData.values,
                borderColor: '#2563eb',
                backgroundColor: 'rgba(37, 99, 235, 0.1)',
                borderWidth: 2,
                tension: 0.35,
                fill: true,
                pointRadius: 5,
                pointBackgroundColor: '#2563eb',
                pointHoverRadius: 7,
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        label: ctx => `${ctx.parsed.y} pertimbangan`
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: { stepSize: 1, precision: 0 }
                }
            }
        }
    });
</script>
@endif
@endpush