<?php

namespace App\Support;

use Carbon\Carbon;

class Format
{
    public static function rupiah($angka): string
    {
        return 'Rp ' . number_format((float) $angka, 0, ',', '.');
    }

    public static function tanggal($tanggal, bool $denganJam = false): string
    {
        if (! $tanggal) {
            return '-';
        }

        $tanggal = $tanggal instanceof Carbon ? $tanggal : Carbon::parse($tanggal);

        return $tanggal->locale('id')->translatedFormat($denganJam ? 'd M Y, H:i' : 'd M Y');
    }
}
