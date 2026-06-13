@extends('layouts.app')

@section('title', 'Pertimbangan Pembelian')

@section('content')

<div class="heading-row">
    <div>
        <p class="eyebrow">Pertimbangan Baru</p>
        <h1>Analisis Pembelian</h1>
        <p class="muted">
            Masukkan informasi pembelian untuk melihat tingkat kelayakannya.
        </p>
    </div>

</div>

<div class="dashboard-grid">

    <section class="panel">

        <h2>Form Pertimbangan</h2>

        <form id="analysis-form" class="form-grid">
            @csrf

            <label class="wide">
                Nama barang
                <input
                    name="item_name"
                    placeholder="Contoh: Charger laptop"
                    required>
            </label>

            <label>
                Total uang saku bulanan (Rp)
                <input
                    type="number"
                    name="monthly_allowance"
                    min="1"
                    value="1500000"
                    required>
            </label>

            <label>
                Uang saat ini (Rp)
                <input
                    type="number"
                    name="current_money"
                    min="0"
                    value="1000000"
                    required>
            </label>

            <label>
                Harga barang (Rp)
                <input
                    type="number"
                    name="item_price"
                    min="0"
                    value="250000"
                    required>
            </label>

            <label>
                Seberapa dibutuhkan? (1-10)
                <input
                    type="number"
                    name="need_level"
                    min="1"
                    max="10"
                    value="8"
                    required>
            </label>

            <label class="wide">
                Hari sampai uang saku berikutnya (0-30)
                <input
                    type="number"
                    name="days_until_allowance"
                    min="0"
                    max="30"
                    value="20"
                    required>
            </label>

            <button
                id="submit-button"
                class="button wide"
                type="submit">
                Lihat Rekomendasi
            </button>

        </form>

        <div id="form-message" class="alert hidden"></div>

    </section>

    <section class="panel result-panel">

        <h2>Pertimbangan Untukmu</h2>

        <div id="empty-result" class="empty">
            Masukkan rencana pembelian untuk mendapatkan saran yang sesuai.
        </div>

        <div id="analysis-result" class="hidden">

            <div class="score-card">
                <div>
                    <span>Tingkat Kesesuaian</span>
                    <strong id="score"></strong>
                </div>

                <b id="category"></b>
            </div>


            <div class="money-grid">
                <div>
                    <span>Sisa setelah beli</span>
                    <strong id="remaining"></strong>
                </div>

                <div>
                    <span>Dana harian tersisa</span>
                    <strong id="daily"></strong>
                </div>
            </div>

<div style="margin-top:1.25rem;display:flex;gap:.75rem;flex-wrap:wrap;">
    <button id="btn-ai" type="button" onclick="mintaAI()" class="button">✨ Rekomendasi AI</button>
   <a href="{{ route('dashboard') }}" class="button secondary" style="display:inline-flex;align-items:center;gap:.4rem;"><i data-lucide="arrow-left" style="width:16px;height:16px;"></i> Kembali ke Dashboard</a>
</div>

            <p
                id="ai-loading"
                class="hidden"
                style="margin-top:.75rem;color:#6b7280;font-size:.875rem;font-style:italic;">
                Menghubungi Gemini AI...
            </p>

            <div
                id="ai-result"
                class="hidden"
                style="margin-top:.75rem;background:#f0f7ff;border-left:3px solid #1a73e8;padding:.875rem 1rem;border-radius:.375rem;">
                <p id="ai-text"></p>
            </div>

            <div
                id="ai-error"
                class="hidden"
                style="margin-top:.75rem;background:#fff0f0;border-left:3px solid #ef4444;padding:.875rem 1rem;border-radius:.375rem;color:#b91c1c;">
            </div>

        </div>

    </section>

</div>

@endsection

@push('scripts')
<script>
const form    = document.getElementById('analysis-form');
const message = document.getElementById('form-message');
const rupiah  = v => new Intl.NumberFormat('id-ID', {
    style: 'currency', currency: 'IDR', maximumFractionDigits: 0
}).format(v);

let lastData = null;

// ── AJAX 1: Hitung Fuzzy & Simpan ────────────────────────────────
form.addEventListener('submit', async e => {
    e.preventDefault();

    const btn = document.getElementById('submit-button');
    btn.disabled    = true;
    btn.textContent = 'Menghitung...';
    message.classList.add('hidden');

    // Reset area AI setiap submit baru
    ['ai-result', 'ai-error', 'ai-loading'].forEach(id =>
        document.getElementById(id).classList.add('hidden')
    );
    const btnAi = document.getElementById('btn-ai');
    btnAi.textContent = '✨ Rekomendasi AI';
    btnAi.disabled    = false;
    btnAi.classList.remove('hidden');

    try {
        const res  = await fetch('{{ route('analyses.store') }}', {
            method: 'POST',
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: new FormData(form)
        });
        const data = await res.json();

        if (!res.ok) {
            const err = data.errors
                ? Object.values(data.errors).flat().join(' ')
                : 'Rekomendasi belum dapat diproses.';
            throw new Error(err);
        }

        const r = data.result;

        // Tampilkan hasil
        document.getElementById('empty-result').classList.add('hidden');
        document.getElementById('analysis-result').classList.remove('hidden');
        document.getElementById('score').textContent    = r.score + '/100';
        document.getElementById('category').textContent = r.category.replaceAll('_', ' ');
        document.getElementById('remaining').textContent = rupiah(r.money_after_purchase);
        document.getElementById('daily').textContent     = rupiah(r.daily_budget_after_purchase);

        message.textContent = data.message;
        message.className   = 'alert success';

        // Render ulang icon lucide (kalau ada icon di tombol kembali)
        if (window.lucide) lucide.createIcons();

        // Simpan untuk dikirim ke AI
        const fd = new FormData(form);
        lastData = {
            analysis_id: data.analysis_id,
            item_name: fd.get('item_name'),
            item_price: fd.get('item_price'),
            score: r.score,
            category: r.category,
            need_level: fd.get('need_level'),
            days_until_allowance: fd.get('days_until_allowance'),
            remaining_percentage: Math.round((fd.get('current_money') / fd.get('monthly_allowance')) * 100),
        };

    } catch (err) {
        message.textContent = err.message;
        message.className   = 'alert error-box';
    } finally {
        btn.disabled    = false;
        btn.textContent = 'Lihat Rekomendasi';
    }
});

// ── AJAX 2: Minta Gemini AI ─────────────────────────────────────
async function mintaAI() {
    if (!lastData) return;

    const btn     = document.getElementById('btn-ai');
    const loading = document.getElementById('ai-loading');
    const result  = document.getElementById('ai-result');
    const error   = document.getElementById('ai-error');

    result.classList.add('hidden');
    error.classList.add('hidden');
    loading.classList.remove('hidden');
    btn.disabled    = true;
    btn.textContent = 'Memuat...';

    try {
        const res  = await fetch('{{ route('analyses.ai') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept':       'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            },
            body: JSON.stringify(lastData),
        });
        const data = await res.json();

        loading.classList.add('hidden');

        if (data.source === 'error' || data.source === 'fallback') {
            error.textContent = data.recommendation;
            error.classList.remove('hidden');
            btn.textContent = '🔁 Coba Lagi';
            btn.disabled    = false;
            return;
        }

        document.getElementById('ai-text').textContent = data.recommendation;
        result.classList.remove('hidden');
        btn.classList.add('hidden');

    } catch (e) {
        loading.classList.add('hidden');
        error.textContent = 'Gagal menghubungi AI. Periksa koneksi internetmu.';
        error.classList.remove('hidden');
        btn.textContent = '🔁 Coba Lagi';
        btn.disabled    = false;
    }
}
</script>
@endpush