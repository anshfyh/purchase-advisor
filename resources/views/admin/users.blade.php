@extends('layouts.app')

@section('title', 'Manajemen User')

@section('content')
<div class="heading-row">
    <div>
        <p class="eyebrow">Admin</p>
        <h1>Manajemen User</h1>
        <p class="muted">Kelola akses pengguna dan lihat jumlah pertimbangan yang dibuat.</p>
    </div>
</div>

@if($errors->any())
    <div class="alert error-box">{{ $errors->first() }}</div>
@endif

<section class="panel">
    <table class="table admin-table">
        <thead>
            <tr>
                <th>Nama</th>
                <th>Email</th>
                <th>Role</th>
                <th>Jumlah Analisis</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($users as $user)
                <tr>
                    <td><strong>{{ $user->name }}</strong></td>
                    <td>{{ $user->email }}</td>
                    <td>
                        <form method="POST" action="{{ route('admin.users.role', $user) }}" class="inline-form">
                            @csrf
                            @method('PATCH')
                            <select name="role">
                                <option value="user" @selected($user->role === 'user')>User</option>
                                <option value="admin" @selected($user->role === 'admin')>Admin</option>
                            </select>
                            <button class="small-button" type="submit">Simpan</button>
                        </form>
                    </td>
                    <td>{{ $user->analyses_count }}</td>
                    <td>
                        @if(! $user->is(auth()->user()))
                            <form method="POST" action="{{ route('admin.users.destroy', $user) }}" onsubmit="return confirm('Hapus pengguna ini? Semua riwayatnya juga akan terhapus.');">
                                @csrf
                                @method('DELETE')
                                <button class="danger-button" type="submit">Hapus</button>
                            </form>
                        @else
                            <span class="muted">Akun aktif</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr><td colspan="5" class="empty">Belum ada pengguna.</td></tr>
            @endforelse
        </tbody>
    </table>
    {{ $users->links() }}
</section>
@endsection
