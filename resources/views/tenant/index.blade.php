@extends('layouts.app')

@section('title', 'Mitra Rental')

@section('content')
    <div class="page-head">
        <div>
            <h1>Mitra rental</h1>
            <p>Kelola mitra yang terdaftar di platform.</p>
        </div>
        <a class="btn btn-inline" href="{{ route('tenant.create') }}">+ Tambah mitra</a>
    </div>

    <div class="table-wrap">
        <table class="tbl">
            <thead><tr><th>Mitra</th><th>Kontak</th><th class="num">Mobil</th><th class="num">Supir</th><th>Status</th><th>Aksi</th></tr></thead>
            <tbody>
                @forelse ($tenants as $t)
                    <tr class="{{ $t->status === 'nonaktif' ? 'is-muted' : '' }}">
                        <td><strong>{{ $t->nama_rental }}</strong><span class="sub">{{ $t->subdomain }} · {{ $t->alamat ?: '-' }}</span></td>
                        <td>{{ $t->telepon ?: '-' }}<span class="sub">{{ $t->email ?: '-' }}</span></td>
                        <td class="num">{{ $t->mobil_count }}</td>
                        <td class="num">{{ $t->supir_count }}</td>
                        <td><x-status :s="$t->status" /></td>
                        <td>
                            <div class="actions">
                                <a class="btn btn-line btn-sm" href="{{ route('tenant.show', $t->id_tenant) }}">Detail</a>
                                <a class="btn btn-line btn-sm" href="{{ route('tenant.edit', $t->id_tenant) }}">Ubah</a>
                                @if ($t->status !== 'nonaktif')
                                    <form method="POST" action="{{ route('tenant.destroy', $t->id_tenant) }}"
                                          onsubmit="return confirm('Nonaktifkan {{ $t->nama_rental }}? Mobilnya tidak akan tampil di beranda pelanggan.')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm">Nonaktifkan</button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="empty-row">Belum ada mitra.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{ $tenants->links('partials.pagination') }}
@endsection
