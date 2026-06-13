@extends('layouts.app')

@section('title', 'Detail Pertimbangan')

@section('content')

<div class="heading-row">
    <div>
        <p class="eyebrow">Detail Pertimbangan</p>
        <h1>{{ $analysis->item_name }}</h1>
        <p class="muted">
            {{ $analysis->created_at->format('d M Y H:i') }}
        </p>
    </div>

    <a href="{{ route('history') }}" class="button secondary">
        ← Kembali
    </a>
</div>

<section class="panel">

    <div class="score-card">
        <div>
            <span>Tingkat Kelayakan</span>
            <strong>{{ $analysis->score }}/100</strong>
        </div>

        <b>{{ $analysis->category }}</b>
    </div>

    <p class="decision" style="margin-top:1rem;">
        {{ $analysis->decision }}
    </p>

</section>

<section class="panel">

    <h2>Ringkasan</h2>

    <div class="money-grid">

        <div>
            <span>Harga Barang</span>
            <strong>
                Rp {{ number_format($analysis->item_price, 0, ',', '.') }}
            </strong>
        </div>

        <div>
            <span>Tingkat Kebutuhan</span>
            <strong>
                {{ $analysis->need_level }}/10
            </strong>
        </div>

        <div>
            <span>Sisa Uang</span>
            <strong>
                {{ $analysis->remaining_percentage }}%
            </strong>
        </div>

        <div>
            <span>Sisa Hari Kiriman</span>
            <strong>
                {{ $analysis->days_until_allowance }} hari
            </strong>
        </div>

    </div>

</section>


<section class="panel">

    <h2>✨ Rekomendasi AI</h2>

    @if($analysis->ai_recommendation)

        <div style="
            background:#f0f7ff;
            border-left:4px solid #1a73e8;
            padding:1rem;
            border-radius:.5rem;
            line-height:1.8;
            margin-bottom:1rem;
        ">
            {{ $analysis->ai_recommendation }}
        </div>

        <button
            id="generate-ai-btn"
            class="button secondary"
            onclick="generateAI({{ $analysis->id }})">
            🔄 Generate Ulang AI
        </button>

    @else

        <div class="empty">
            Belum ada rekomendasi AI untuk pertimbangan ini.
        </div>

        <div style="margin-top:1rem;">
            <button
                id="generate-ai-btn"
                class="button"
                onclick="generateAI({{ $analysis->id }})">
                ✨ Buat Rekomendasi AI
            </button>
        </div>

    @endif

</section>

<section class="panel">

    <details>
        <summary style="cursor:pointer;font-weight:600;">
            Lihat Detail Perhitungan
        </summary>

        <div style="margin-top:1rem;">

            <div class="money-grid">

                <div>
                    <span>Uang Saku Bulanan</span>
                    <strong>
                        Rp {{ number_format($analysis->monthly_allowance, 0, ',', '.') }}
                    </strong>
                </div>

                <div>
                    <span>Uang Saat Ini</span>
                    <strong>
                        Rp {{ number_format($analysis->current_money, 0, ',', '.') }}
                    </strong>
                </div>

                <div>
                    <span>Skor Fuzzy</span>
                    <strong>
                        {{ $analysis->score }}
                    </strong>
                </div>

                <div>
                    <span>Kategori</span>
                    <strong>
                        {{ $analysis->category }}
                    </strong>
                </div>

            </div>

        </div>
    </details>

</section>

<section class="panel">

    <h2>Aksi</h2>

    <div style="display:flex;gap:.75rem;flex-wrap:wrap;">

        <button
            type="button"
            class="button secondary"
            onclick="openEditModal()">
            ✏ Edit
        </button>

        <form
            action="{{ route('analyses.destroy', $analysis) }}"
            method="POST"
            onsubmit="return confirm('Hapus riwayat ini?')"
        >
            @csrf
            @method('DELETE')

            <button type="submit" class="button">
                🗑 Hapus
            </button>
        </form>

    </div>

</section>

<div id="editModal"
     style="
        display:none;
        position:fixed;
        inset:0;
        background:rgba(0,0,0,.5);
        z-index:999;
        justify-content:center;
        align-items:center;
     ">

    <div style="
        background:white;
        width:90%;
        max-width:600px;
        padding:1.5rem;
        border-radius:12px;
    ">

        <h2>Edit Pertimbangan</h2>

        <form id="editForm">

            <label>Nama Barang</label>
            <input
                type="text"
                name="item_name"
                value="{{ $analysis->item_name }}"
                required>

            <label>Uang Saku Bulanan</label>
            <input
                type="number"
                name="monthly_allowance"
                value="{{ $analysis->monthly_allowance }}"
                required>

            <label>Uang Saat Ini</label>
            <input
                type="number"
                name="current_money"
                value="{{ $analysis->current_money }}"
                required>

            <label>Harga Barang</label>
            <input
                type="number"
                name="item_price"
                value="{{ $analysis->item_price }}"
                required>

            <label>Tingkat Kebutuhan (1-10)</label>
            <input
                type="number"
                min="1"
                max="10"
                name="need_level"
                value="{{ $analysis->need_level }}"
                required>

            <label>Sisa Hari Kiriman</label>
            <input
                type="number"
                min="0"
                max="30"
                name="days_until_allowance"
                value="{{ $analysis->days_until_allowance }}"
                required>

            <div style="margin-top:1rem;display:flex;gap:.5rem;">

                <button
                    type="button"
                    class="button secondary"
                    onclick="closeEditModal()">
                    Batal
                </button>

                <button
                    type="submit"
                    class="button">
                    Simpan
                </button>

            </div>

        </form>

    </div>

</div>

@push('scripts')
<script>

async function generateAI(id)
{
    const btn = document.getElementById('generate-ai-btn');

    btn.disabled = true;
    btn.textContent = 'Menghubungi AI...';

    try {

        const response = await fetch(`/analyses/${id}/generate-ai`, {
            method: 'POST',
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN':
                    document.querySelector('meta[name="csrf-token"]').content
            }
        });

        if (!response.ok) {
            throw new Error('Gagal membuat rekomendasi AI');
        }

        location.reload();

    } catch (error) {

        alert(error.message);

        btn.disabled = false;
        btn.textContent = 'Coba Lagi';
    }
}

function openEditModal()
{
    document.getElementById('editModal').style.display = 'flex';
}

function closeEditModal()
{
    document.getElementById('editModal').style.display = 'none';
}

document.getElementById('editForm')
.addEventListener('submit', async function(e)
{
    e.preventDefault();

    const formData = new FormData(this);
    formData.append('_method', 'PATCH');

    try {

        const response = await fetch(
            '{{ route('analyses.update', $analysis) }}',
            {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN':
                        document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                },
                body: formData
            }
        );

        if (!response.ok) {
            throw new Error('Gagal memperbarui data');
        }

        alert('Pertimbangan berhasil diperbarui');

        location.reload();

    } catch (err) {

        alert(err.message);

    }
});

</script>
@endpush

@endsection