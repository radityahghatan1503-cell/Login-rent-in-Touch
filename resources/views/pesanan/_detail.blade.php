@php
    $urut = ['menunggu', 'dikonfirmasi', 'berjalan', 'selesai'];
    $idx = array_search($p->status, $urut, true);
    $dibayar = $p->pembayaran->where('status', 'lunas')->sum('jumlah');
    $sisa = max(0, $p->total_biaya - $dibayar);
    $F = \App\Support\Format::class;
@endphp

<div class="card">
    <h2>Status pesanan</h2>
    @if ($p->status === 'dibatalkan')
        <p style="margin:0"><x-status s="dibatalkan" /> Pesanan ini dibatalkan.</p>
    @else
        <ol class="steps">
            @foreach ($urut as $i => $s)
                <li class="{{ $idx !== false && $i < $idx ? 'done' : '' }} {{ $i === $idx ? 'now' : '' }}">{{ ucfirst($s) }}</li>
            @endforeach
        </ol>
    @endif
</div>

<div class="detail-grid">
    <div>
        <div class="card">
            <h2>Unit</h2>
            <dl class="dl">
                <dt>Mobil</dt><dd>{{ $p->mobil->merek }} {{ $p->mobil->model }} ({{ $p->mobil->tahun }})</dd>
                <dt>Kategori</dt><dd>{{ $p->mobil->kategori?->nama_kategori ?? '-' }}</dd>
                <dt>Plat nomor</dt><dd>{{ $p->mobil->plat_nomor }}</dd>
                <dt>Spesifikasi</dt><dd>{{ ucfirst($p->mobil->transmisi) }} · {{ ucfirst($p->mobil->bahan_bakar) }} · {{ $p->mobil->kapasitas_penumpang }} kursi</dd>
                <dt>Sopir</dt>
                <dd>
                    @if ($p->supir)
                        {{ $p->supir->nama_supir }}@if ($mitra) · {{ $p->supir->telepon }}@endif
                    @else
                        Tanpa sopir (lepas kunci)
                    @endif
                </dd>
            </dl>
        </div>

        <div class="card">
            <h2>Jadwal &amp; lokasi</h2>
            <dl class="dl">
                <dt>Mulai</dt><dd>{{ $F::tanggal($p->tanggal_mulai, true) }}</dd>
                <dt>Selesai</dt><dd>{{ $F::tanggal($p->tanggal_selesai, true) }}</dd>
                <dt>Lama sewa</dt><dd>{{ $p->lama_sewa_hari }} hari</dd>
                <dt>Lokasi jemput</dt><dd>{{ $p->lokasi_jemput ?: '-' }}</dd>
                <dt>Lokasi kembali</dt><dd>{{ $p->lokasi_kembali ?: '-' }}</dd>
            </dl>
        </div>

        <div class="card">
            <h2>Pembayaran</h2>
            @if ($p->pembayaran->isEmpty())
                <p style="margin:0;color:var(--muted)">Belum ada pembayaran tercatat.</p>
            @else
                <div class="table-wrap" style="border:0">
                    <table class="tbl">
                        <thead><tr><th>Jenis</th><th>Metode</th><th class="num">Jumlah</th><th>Tanggal</th><th>Status</th></tr></thead>
                        <tbody>
                            @foreach ($p->pembayaran as $b)
                                <tr>
                                    <td>{{ strtoupper($b->jenis) === 'DP' ? 'DP' : ucfirst($b->jenis) }}</td>
                                    <td>{{ ucfirst($b->metode) }}</td>
                                    <td class="num">{{ $F::rupiah($b->jumlah) }}</td>
                                    <td>{{ $F::tanggal($b->tanggal_bayar) }}</td>
                                    <td><x-status :s="$b->status" /></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>

        @if ($p->pengembalian)
            <div class="card">
                <h2>Pengembalian unit</h2>
                <dl class="dl">
                    <dt>Dikembalikan</dt><dd>{{ $F::tanggal($p->pengembalian->tanggal_kembali, true) }}</dd>
                    <dt>Kondisi mobil</dt><dd><x-status :s="$p->pengembalian->kondisi_mobil" /></dd>
                    <dt>Keterlambatan</dt><dd>{{ $p->pengembalian->keterlambatan_jam }} jam</dd>
                    <dt>Denda</dt><dd>{{ $F::rupiah($p->pengembalian->denda) }}</dd>
                    @if ($p->pengembalian->catatan)
                        <dt>Catatan</dt><dd>{{ $p->pengembalian->catatan }}</dd>
                    @endif
                </dl>
            </div>
        @endif
    </div>

    <div>
        <div class="card">
            @if ($mitra)
                <h2>Pelanggan</h2>
                <dl class="dl">
                    <dt>Nama</dt><dd>{{ $p->pelanggan->nama_lengkap }}</dd>
                    <dt>NIK</dt><dd>{{ $p->pelanggan->nik }}</dd>
                    <dt>Telepon</dt><dd>{{ $p->pelanggan->telepon }}</dd>
                    <dt>No. SIM</dt><dd>{{ $p->pelanggan->no_sim ?: '-' }}</dd>
                    <dt>Alamat</dt><dd>{{ $p->pelanggan->alamat ?: '-' }}</dd>
                    @if ($p->staff)
                        <dt>Diproses oleh</dt><dd>{{ $p->staff->nama }}</dd>
                    @endif
                </dl>
            @else
                <h2>Mitra rental</h2>
                <dl class="dl">
                    <dt>Nama</dt><dd>{{ $p->tenant->nama_rental }}</dd>
                    <dt>Alamat</dt><dd>{{ $p->tenant->alamat ?: '-' }}</dd>
                    <dt>Telepon</dt><dd>{{ $p->tenant->telepon ?: '-' }}</dd>
                </dl>
            @endif
        </div>

        <div class="card">
            <h2>Biaya</h2>
            <div class="sum-row"><span>Total biaya</span><span>{{ $F::rupiah($p->total_biaya) }}</span></div>
            <div class="sum-row"><span>Sudah dibayar (lunas)</span><span>{{ $F::rupiah($dibayar) }}</span></div>
            <div class="sum-row total"><span>Sisa</span><span>{{ $F::rupiah($sisa) }}</span></div>
        </div>
    </div>
</div>
