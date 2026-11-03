@extends('layouts.app')

@section('title', $tenant->nama_rental)

@section('content')
    <a class="back-link" href="{{ route('tenant.index') }}">← Kembali ke daftar mitra</a>

    <div class="page-head">
        <div>
            <h1>{{ $tenant->nama_rental }}</h1>
            <p>{{ $tenant->subdomain }} · dibuat {{ \App\Support\Format::tanggal($tenant->created_at) }}</p>
        </div>
        <div class="actions">
            <x-status :s="$tenant->status" />
            <a class="btn btn-line btn-sm" href="{{ route('tenant.edit', $tenant->id_tenant) }}">Ubah</a>
        </div>
    </div>

    <div class="stats">
        <div class="stat"><div class="n">{{ $tenant->mobil_count }}</div><div class="l">Mobil</div></div>
        <div class="stat"><div class="n">{{ $tenant->supir_count }}</div><div class="l">Supir</div></div>
        <div class="stat"><div class="n">{{ $tenant->pemesanan_count }}</div><div class="l">Pemesanan</div></div>
    </div>

    <div class="card">
        <h2>Profil</h2>
        <dl class="dl">
            <dt>Alamat</dt><dd>{{ $tenant->alamat ?: '-' }}</dd>
            <dt>Telepon</dt><dd>{{ $tenant->telepon ?: '-' }}</dd>
            <dt>Email</dt><dd>{{ $tenant->email ?: '-' }}</dd>
        </dl>
    </div>

    <h2 class="section-title">Akun mitra</h2>
    <div class="table-wrap">
        <table class="tbl">
            <thead><tr><th>Nama</th><th>Email</th><th>Peran</th><th>Status</th><th></th></tr></thead>
            <tbody>
                @forelse ($akun as $a)
                    <tr class="{{ $a->is_active ? '' : 'is-muted' }}">
                        <td><strong>{{ $a->nama }}</strong></td>
                        <td>{{ $a->email }}</td>
                        <td>{{ $a->roleLabel() }}</td>
                        <td><x-status :s="$a->is_active ? 'aktif' : 'nonaktif'" /></td>
                        <td>
                            <form method="POST" action="{{ route('tenant.akun.toggle', [$tenant->id_tenant, $a->id_user]) }}">
                                @csrf @method('PATCH')
                                <button type="submit" class="btn btn-line btn-sm">{{ $a->is_active ? 'Nonaktifkan' : 'Aktifkan' }}</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="empty-row">Belum ada akun untuk mitra ini.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <h2 class="section-title">Tambah akun mitra</h2>
    <form class="form-card" method="POST" action="{{ route('tenant.akun.store', $tenant->id_tenant) }}" novalidate>
        @csrf
        <div class="form-grid">
            <x-input name="nama" label="Nama" maxlength="100" required />
            <x-input name="email" label="Email" type="email" maxlength="100" required />
            <x-select name="role" label="Peran" :options="['admin_rental' => 'Admin rental', 'staff' => 'Staf']" value="admin_rental" />
            <x-input name="password" label="Kata sandi awal" type="password" autocomplete="new-password" hint="Minimal 8 karakter, huruf dan angka." required />
        </div>
        <div class="form-actions"><button type="submit" class="btn">Buat akun</button></div>
    </form>
@endsection
