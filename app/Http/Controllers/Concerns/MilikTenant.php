<?php

namespace App\Http\Controllers\Concerns;

trait MilikTenant
{
    /** ID tenant milik akun mitra yang sedang login. */
    protected function idTenant(): int
    {
        $id = auth()->user()->id_tenant;

        abort_if(! $id, 403, 'Akun ini belum terhubung ke mitra rental.');

        return (int) $id;
    }
}
