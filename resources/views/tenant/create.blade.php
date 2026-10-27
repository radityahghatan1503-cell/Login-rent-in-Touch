<!DOCTYPE html>
<html>
<head>
    <title>Tambah Tenant</title>
</head>
<body>

    <h1>Tambah Tenant</h1>

    <form action="{{ route('tenant.store') }}" method="POST">
        @csrf

        <p>
            <label>Nama Rental</label><br>
            <input type="text" name="nama_rental">
        </p>

        <p>
            <label>Subdomain</label><br>
            <input type="text" name="subdomain">
        </p>

        <p>
            <label>Alamat</label><br>
            <textarea name="alamat"></textarea>
        </p>

        <p>
            <label>Telepon</label><br>
            <input type="text" name="telepon">
        </p>

        <p>
            <label>Email</label><br>
            <input type="email" name="email">
        </p>

        <p>
            <label>Status</label><br>
            <select name="status">
                <option value="aktif">Aktif</option>
                <option value="nonaktif">Nonaktif</option>
            </select>
        </p>

        <button type="submit">Simpan</button>

    </form>

    <br>

    <a href="{{ route('tenant.index') }}">Kembali</a>

</body>
</html>