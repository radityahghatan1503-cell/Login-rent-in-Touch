@extends('layouts.app')

@section('title', $mobil->merek . ' ' . $mobil->model)

@section('content')
    @php
        $F = \App\Support\Format::class;
        $nama = $mobil->merek . ' ' . $mobil->model;
        $adaSopir = $supir->isNotEmpty();
        $total = $mobil->harga_per_hari * $lamaHari;
    @endphp

    <a class="back-link" href="{{ route('beranda', ['mulai' => $mulai, 'selesai' => $selesai]) }}">← Kembali ke pencarian</a>

    <div class="detail-grid">
        <div>
            <div class="hero-img">
                <span class="chip">{{ $mobil->tenant->nama_rental }}</span>
                @include('partials.car', ['m' => ['foto' => $mobil->foto, 'nama' => $nama]])
            </div>

            <h1>{{ $nama }}</h1>
            <p class="sub">{{ $mobil->kategori?->nama_kategori }} · {{ $mobil->tahun }}</p>

            <div class="card">
                <h2>Spesifikasi</h2>
                <dl class="dl">
                    <dt>Kategori</dt><dd>{{ $mobil->kategori?->nama_kategori ?? '-' }}</dd>
                    <dt>Tahun</dt><dd>{{ $mobil->tahun }}</dd>
                    <dt>Warna</dt><dd>{{ $mobil->warna ?: '-' }}</dd>
                    <dt>Transmisi</dt><dd>{{ ucfirst($mobil->transmisi) }}</dd>
                    <dt>Bahan bakar</dt><dd>{{ ucfirst($mobil->bahan_bakar) }}</dd>
                    <dt>Kapasitas</dt><dd>{{ $mobil->kapasitas_penumpang }} kursi</dd>
                </dl>
            </div>

            <div class="card">
                <h2>Layanan</h2>
                <p class="car-tags" style="min-height:0">
                    <span class="tag">Lepas kunci</span>
                    @if ($adaSopir) <span class="tag">Dengan sopir</span> @endif
                </p>
                @if ($adaSopir)
                    <div class="table-wrap" style="border:0">
                        <table class="tbl">
                            <thead><tr><th>Supir tersedia</th><th class="num">Tarif / hari</th></tr></thead>
                            <tbody>
                                @foreach ($supir as $s)
                                    <tr><td>{{ $s->nama_supir }}</td><td class="num">{{ $F::rupiah($s->tarif_per_hari) }}</td></tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p style="margin:0;color:var(--muted)">Mitra ini belum memiliki supir yang tersedia. Unit disewa lepas kunci.</p>
                @endif
            </div>

            <div class="card">
                <h2>Mitra rental</h2>
                <dl class="dl">
                    <dt>Nama</dt><dd>{{ $mobil->tenant->nama_rental }}</dd>
                    <dt>Alamat</dt><dd>{{ $mobil->tenant->alamat ?: '-' }}</dd>
                    <dt>Telepon</dt><dd>{{ $mobil->tenant->telepon ?: '-' }}</dd>
                </dl>
            </div>
        </div>

        <aside class="sticky">
            <div class="card">
                <p class="price-big">{{ $F::rupiah($mobil->harga_per_hari) }} <small>/ hari</small></p>

                <form method="GET" action="{{ route('mobil.detail', $mobil->id_mobil) }}" class="mini-form">
                    <div><label for="mulai">Mulai</label><input id="mulai" type="date" name="mulai" value="{{ $mulai }}" min="{{ now()->toDateString() }}"></div>
                    <div><label for="selesai">Selesai</label><input id="selesai" type="date" name="selesai" value="{{ $selesai }}" min="{{ $mulai }}"></div>
                    <button type="submit" class="btn btn-line btn-sm">Cek</button>
                </form>

                @if ($bentrok)
                    <div class="avail no" role="status">Sudah dipesan pada tanggal ini. Coba tanggal lain.</div>
                @else
                    <div class="avail ok" role="status">Tersedia pada tanggal ini</div>
                @endif

                <div class="sum-row"><span>{{ $F::rupiah($mobil->harga_per_hari) }} × {{ $lamaHari }} hari</span><span>{{ $F::rupiah($total) }}</span></div>
                @if ($adaSopir)
                    <div class="sum-row"><span>Sopir (opsional)</span><span>mulai {{ $F::rupiah($supir->first()->tarif_per_hari) }} / hari</span></div>
                @endif
                <div class="sum-row total"><span>Total sewa mobil</span><span>{{ $F::rupiah($total) }}</span></div>

                @if ($bentrok)
                    <button type="button" class="btn" style="margin-top:14px" disabled>Mobil tidak tersedia</button>
                @else
                    <form method="POST" action="{{ route('booking.store', $mobil->id_mobil) }}" enctype="multipart/form-data" class="booking-form" style="margin-top:16px">
                        @csrf
                        <h2>Ajukan pemesanan</h2>
                        <input type="hidden" name="tanggal_mulai" value="{{ $mulai }}">
                        <input type="hidden" name="tanggal_selesai" value="{{ $selesai }}">

                        <div class="field">
                            <label for="booking_nama">Nama lengkap sesuai identitas</label>
                            <input id="booking_nama" name="nama_lengkap" value="{{ old('nama_lengkap', auth()->user()->nama) }}" maxlength="100" required>
                            @error('nama_lengkap')<small class="error">{{ $message }}</small>@enderror
                        </div>
                        <div class="field">
                            <label for="booking_nik">NIK (16 digit)</label>
                            <input id="booking_nik" name="nik" value="{{ old('nik') }}" inputmode="numeric" maxlength="16" minlength="16" required>
                            @error('nik')<small class="error">{{ $message }}</small>@enderror
                        </div>
                        <div class="field">
                            <label for="booking_telepon">Nomor telepon</label>
                            <input id="booking_telepon" name="telepon" value="{{ old('telepon') }}" maxlength="20" required>
                            @error('telepon')<small class="error">{{ $message }}</small>@enderror
                        </div>
                        <div class="field">
                            <label for="booking_alamat">Alamat</label>
                            <textarea id="booking_alamat" name="alamat" rows="2">{{ old('alamat') }}</textarea>
                            @error('alamat')<small class="error">{{ $message }}</small>@enderror
                        </div>
                        <div class="field">
                            <label for="booking_sim">Nomor SIM</label>
                            <input id="booking_sim" name="no_sim" value="{{ old('no_sim') }}" maxlength="30" required>
                            @error('no_sim')<small class="error">{{ $message }}</small>@enderror
                        </div>
                        <div class="field">
                            <label for="booking_ktp">Unggah KTP (JPG/PNG/PDF, maks. 5 MB)</label>
                            <input id="booking_ktp" type="file" name="foto_ktp" accept=".jpg,.jpeg,.png,.pdf" required>
                            @error('foto_ktp')<small class="error">{{ $message }}</small>@enderror
                        </div>
                        <div class="field">
                            <label for="booking_foto_sim">Unggah SIM (JPG/PNG/PDF, maks. 5 MB)</label>
                            <input id="booking_foto_sim" type="file" name="foto_sim" accept=".jpg,.jpeg,.png,.pdf" required>
                            @error('foto_sim')<small class="error">{{ $message }}</small>@enderror
                        </div>
                        <div class="field">
                            <label for="booking_supir">Pilihan layanan</label>
                            <select id="booking_supir" name="id_supir">
                                <option value="">Lepas kunci</option>
                                @foreach ($supir as $s)
                                    <option value="{{ $s->id_supir }}" @selected(old('id_supir') == $s->id_supir)>
                                        Dengan sopir — {{ $s->nama_supir }} ({{ $F::rupiah($s->tarif_per_hari) }}/hari)
                                    </option>
                                @endforeach
                            </select>
                            @error('id_supir')<small class="error">{{ $message }}</small>@enderror
                        </div>
                        <div class="field">
                            <label for="booking_jemput">Lokasi penjemputan</label>
                            <input id="booking_jemput" name="lokasi_jemput" value="{{ old('lokasi_jemput') }}" maxlength="255">
                            @error('lokasi_jemput')<small class="error">{{ $message }}</small>@enderror
                        </div>
                        <div class="field">
                            <label for="booking_kembali">Lokasi pengembalian</label>
                            <input id="booking_kembali" name="lokasi_kembali" value="{{ old('lokasi_kembali') }}" maxlength="255">
                            @error('lokasi_kembali')<small class="error">{{ $message }}</small>@enderror
                        </div>
                        <p class="hint">Dokumen identitas disimpan secara privat. Pengajuan akan berstatus menunggu konfirmasi mitra. Pembayaran dilakukan pada tahap berikutnya.</p>
                        <button type="submit" class="btn" style="width:100%">Kirim pengajuan booking</button>
                    </form>
                @endif
            </div>
        </aside>
    </div>
@endsection
