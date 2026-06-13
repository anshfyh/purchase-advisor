@extends('layouts.app')

@section('title', 'Pengaturan Profil')

@section('content')
<div class="heading-row">
    <div>
        <p class="eyebrow">Akun Saya</p>
        <h1>Pengaturan Profil</h1>
        <p class="muted">Kelola informasi akun dan keamanan profilmu di satu tempat.</p>
    </div>
</div>

<div class="profile-layout profile-layout-single">
    <div class="profile-side">

        <section class="panel account-overview">
            <div class="profile-avatar">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</div>
            <h2>{{ auth()->user()->name }}</h2>
            <p>{{ auth()->user()->email }}</p>
            <span class="role-pill">{{ auth()->user()->isAdmin() ? 'Admin' : 'User' }}</span>

            <button type="button" class="profile-edit-toggle" id="openProfileModalFromPage">
                Edit Profil
            </button>
        </section>

        <section class="panel account-summary">
            <div>
                <span>Nama</span>
                <strong>{{ auth()->user()->name }}</strong>
            </div>
            <div>
                <span>Email</span>
                <strong>{{ auth()->user()->email }}</strong>
            </div>
            <div>
                <span>Peran</span>
                <strong>{{ auth()->user()->isAdmin() ? 'Admin' : 'User' }}</strong>
            </div>
        </section>

        <section class="panel danger-zone">
            <div>
                <h2>Keluar dari akun</h2>
                <p class="muted">Selesaikan sesi akunmu dengan aman.</p>
            </div>
            <form method="POST" action="{{ route('logout') }}" onsubmit="return confirm('Apakah Anda yakin akan keluar?');">
                @csrf
                <button type="submit" class="logout-profile-button"><span></span> Logout</button>
            </form>
        </section>

    </div>
</div>
@endsection

@push('scripts')
<script>
    document.getElementById('openProfileModalFromPage')?.addEventListener('click', () => {
        document.getElementById('profileModalOverlay')?.classList.add('active');
    });
</script>
@endpush