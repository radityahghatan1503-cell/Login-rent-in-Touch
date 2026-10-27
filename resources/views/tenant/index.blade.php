<!DOCTYPE html>
<html>
<head>
    <title>Data Tenant</title>
</head>
<body>

    <h1>Data Tenant Rent In Touch</h1>

    <a href="{{ route('tenant.create') }}">Tambah Tenant</a>

    <br><br>

    <table border="1" cellpadding="10">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nama Rental</th>
                <th>Subdomain</th>
                <th>Telepon</th>
                <th>Email</th>
                <th>Status</th>
                <th>Aksi</th>
            </tr>
        </thead>

        <tbody>

            @forelse ($tenants as $tenant)

                <tr>
                    <td>{{ $tenant->id_tenant }}</td>

                    <td>{{ $tenant->nama_rental }}</td>

                    <td>{{ $tenant->subdomain }}</td>

                    <td>{{ $tenant->telepon }}</td>

                    <td>{{ $tenant->email }}</td>

                    <td>{{ $tenant->status }}</td>

                  <td>
    <a href="{{ route('tenant.edit', $tenant->id_tenant) }}">
        Edit
    </a>

    <form action="{{ route('tenant.destroy', $tenant->id_tenant) }}"
          method="POST"
          style="display:inline;">

        @csrf
        @method('DELETE')

        <button type="submit"
                onclick="return confirm('yakin menghapusnya hayooo?')">
            Hapus
        </button>
    </form>
</td>