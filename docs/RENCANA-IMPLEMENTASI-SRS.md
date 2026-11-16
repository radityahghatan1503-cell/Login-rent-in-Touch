# Rencana Penyesuaian Rent In-Touch terhadap SRS

Dokumen ini menjadi rencana kerja untuk menyesuaikan aplikasi dengan SRS **Sistem Pengelolaan Rental Mobil Berbasis Web dengan Konsep Multi-Client**. Perubahan dikerjakan pada branch `feature/srs-compliance`; branch utama `main` tidak diubah.

## Prinsip perubahan

1. Mempertahankan desain, warna, layout, dan pola navigasi yang sudah ada. Form dan halaman baru mengikuti komponen serta gaya Blade yang sudah dipakai.
2. Tidak menghapus tabel, kolom, akun, transaksi, maupun data yang sudah ada.
3. Perubahan skema dilakukan dengan migration baru yang bersifat aditif. Migration tidak boleh mengosongkan tabel atau melakukan drop/rename kolom yang sedang dipakai.
4. Menjaga pemisahan data per `id_tenant` pada setiap query dan otorisasi mitra.
5. Rahasia payment gateway hanya disimpan pada environment server; tidak dimasukkan ke Git, source code, SQL dump, atau dokumentasi publik.
6. Fitur yang membutuhkan persetujuan operasional (misalnya payout ke rekening mitra dan kebijakan deposit/refund) tidak dijalankan otomatis sebelum konfigurasi dan aturan bisnis disediakan.

## Pemetaan pekerjaan terhadap SRS

| Area SRS | Kondisi awal yang ditemukan | Perubahan yang direncanakan |
|---|---|---|
| FR-01 Verifikasi mitra | Data tenant sudah ada, alur dokumen legalitas belum lengkap | Form registrasi/unggah dokumen KTP penanggung jawab dan NIB/SIUP; status verifikasi admin; mitra belum terverifikasi tidak dapat memasarkan unit |
| FR-02–04 Armada | CRUD mobil dan status perawatan sudah tersedia | Pertahankan CRUD; tambah validasi status/akses dan kalender ketersediaan per unit |
| FR-05 Layanan tambahan | Supir tersedia, layanan dan biaya antar-jemput belum menjadi katalog layanan | Tambah layanan tambahan per tenant/unit, harga, dan snapshot biaya pada booking |
| FR-06 Notifikasi booking | Belum ditemukan alur notifikasi lengkap | Tambah notifikasi dalam aplikasi untuk mitra dan pelanggan; siapkan kanal email bila konfigurasi tersedia |
| FR-07 Keputusan mitra | Perubahan status tersedia, alasan penolakan belum ada | Tambah alasan penolakan wajib ketika booking ditolak |
| FR-08 Dashboard mitra | Statistik dasar tersedia | Tambah ringkasan pendapatan, booking aktif, dan riwayat transaksi per tenant |
| FR-09–11 Pencarian/detail/filter | Pencarian dan detail mobil tersedia; rating belum tersedia dan filter perlu dilengkapi | Lengkapi filter lokasi/tanggal/harga/transmisi/kapasitas/sopir; tampilkan ulasan dan rating jika ada |
| FR-12–15 Booking dan identitas | Daftar/detail/status pesanan tersedia; tombol booking belum aktif; dokumen identitas belum diwajibkan pada alur booking | Aktifkan pengajuan booking, validasi tanggal dan dokumen KTP/SIM, penguncian jadwal untuk mencegah double booking, dan aturan pembatalan |
| FR-16 Ulasan | Belum ditemukan tabel/fitur ulasan | Tambah ulasan dan rating yang hanya dapat diberikan pelanggan setelah sewa selesai |
| Pembayaran, deposit, invoice, escrow, refund, payout | Tabel pembayaran dasar tersedia; gateway dan alur dana belum lengkap | Integrasi gateway, webhook terverifikasi, invoice, pencatatan deposit, ledger escrow, refund dan payout sesuai kemampuan API serta persetujuan konfigurasi |
| Admin platform | Kelola tenant dasar tersedia; verifikasi, komisi, monitoring, laporan, dispute, audit belum lengkap | Tambah halaman verifikasi mitra, konfigurasi komisi, laporan lintas tenant, pengelolaan keluhan, dan audit log |
| NFR keamanan dan multi-tenant | Role middleware dan beberapa filter tenant tersedia; konsistensi semua jalur perlu diaudit | Audit otorisasi setiap endpoint, validasi kepemilikan relasi lintas tenant, proteksi upload dokumen, CSRF, rate limiting, dan HTTPS saat deployment |
| NFR performa/kompatibilitas | Belum ada bukti load test/monitoring lintas browser | Tambah indeks yang relevan secara aditif dan susun pengujian performa, responsif, browser, serta sinkronisasi ketersediaan |

## Urutan pelaksanaan (disesuaikan dengan keputusan pemilik proyek)

**Prioritas pertama adalah menyelesaikan proses booking. Pembayaran dan integrasi Midtrans ditunda sampai alur booking selesai dan diuji.**

### Tahap 1 — Booking pelanggan sampai keputusan mitra (sedang dikerjakan)
- Form pengajuan booking dari halaman detail mobil.
- Validasi tanggal, data pelanggan, unggahan KTP/SIM ke penyimpanan privat.
- Pemeriksaan ketersediaan mobil dan sopir di dalam transaksi database dengan row locking.
- Pembuatan kode booking dan perhitungan biaya sewa serta sopir.
- Notifikasi database kepada admin rental/staf tenant terkait.
- Halaman mitra untuk menerima/menolak pengajuan; alasan penolakan wajib diisi.
- Halaman status pelanggan dan pembatalan sebelum mitra mengonfirmasi.
- Akses dokumen identitas dibatasi pada tenant pemilik booking.

### Tahap 2 — Penyelesaian alur operasional dan keamanan
- Uji seluruh alur booking pelanggan, notifikasi, keputusan mitra, pembatalan, dan isolasi tenant.
- Lengkapi kalender ketersediaan, layanan tambahan, ulasan, dan checklist/foto serah-terima serta pengembalian.
- Audit relasi tenant dan seluruh jalur akses data.
- Pastikan migration aditif dapat dijalankan pada database baru maupun database yang sudah berisi tabel dari dump SQL.

### Tahap 3 — Modul mitra dan back office
- Registrasi/verifikasi mitra dan dokumen legalitas.
- Dashboard pendapatan, booking aktif, dan riwayat transaksi.
- Komisi platform, laporan, keluhan/dispute, dan audit log.

### Tahap 4 — Pembayaran dan transaksi (ditunda sampai booking selesai)
- Integrasi Midtrans sandbox/production setelah akun dan kredensial tersedia.
- Invoice, deposit, escrow/ledger, refund, dan payout sesuai kemampuan API provider.

### Tahap 5 — Pengujian non-fungsional dan penerimaan
- Uji kriteria SRS: pencarian <2 detik pada beban uji yang disepakati, sinkronisasi ketersediaan <=5 detik, dan pengujian kompatibilitas browser/responsif.

### Rencana pembayaran setelah tahap booking selesai
- Gunakan Midtrans Snap sebagai kandidat integrasi awal karena dokumentasi resminya menyediakan alur pembuatan transaksi dari backend dan notifikasi status pembayaran.
- Implementasi hanya dapat diaktifkan setelah tersedia Server Key, Client Key, akun sandbox, dan URL publik HTTPS untuk webhook. Kunci tidak boleh dikirim melalui chat atau commit Git; masukkan langsung ke file `.env` lokal/server.
- Status lunas hanya diperbarui dari webhook yang signature-nya valid dan status transaksi gateway yang memenuhi ketentuan provider; redirect browser tidak menjadi bukti pembayaran.
- Catat pembayaran, deposit, invoice, komisi, escrow/ledger, refund, dan payout secara terpisah serta idempotent.
- Payout ke rekening mitra membutuhkan akses produk payout/disbursement dan persetujuan/aktivasi provider. Jika API payout belum tersedia pada akun, dana tidak boleh dianggap sudah dicairkan.

### Tahap 4 — Back office, laporan, dan pengujian
- Dashboard admin lintas tenant, komisi, transaksi, keluhan/dispute, audit log.
- Dashboard pendapatan dan transaksi mitra.
- Pengujian fungsional semua FR, otorisasi antar tenant, double booking, webhook duplikat/tidak valid, serta alur pembatalan/refund.
- Uji kriteria SRS: pencarian <2 detik pada beban uji yang disepakati, sinkronisasi ketersediaan <=5 detik, dan pengujian kompatibilitas browser/responsif.

## Catatan payment gateway

Dokumentasi resmi Midtrans menjelaskan pembuatan Snap transaction token dari backend dan pengiriman notifikasi server-to-server. Status pembayaran harus divalidasi pada sisi server menggunakan signature/validasi status; webhook dapat dikirim ulang sehingga pemrosesan perlu idempotent. URL notifikasi harus dapat diakses dari internet dan menggunakan HTTPS.

Dokumentasi:
- https://docs.midtrans.com/docs/snap-snap-integration-guide
- https://docs.midtrans.com/docs/https-notification-webhooks

Integrasi gateway tidak berarti dana otomatis menjadi escrow atau payout. Mekanisme penahanan dana, refund deposit, dan pencairan ke mitra harus sesuai produk/kontrak provider yang aktif dan aturan operasional platform.

## Hal yang tidak akan dilakukan tanpa persetujuan terpisah

- Menghapus atau menggabungkan tabel `users` dan `tb_users`.
- Menghapus atau mengubah data contoh/transaksi yang sudah ada.
- Mengubah tampilan utama secara menyeluruh.
- Mengaktifkan transaksi produksi, payout, atau refund sungguhan tanpa kredensial dan persetujuan konfigurasi.
- Mengubah fitur di luar ruang lingkup SRS.
