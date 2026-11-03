<?php

namespace App\Support;

class Validasi
{
    /** Pesan error umum dalam Bahasa Indonesia (berlaku untuk semua field). */
    public static function pesan(): array
    {
        return [
            'required' => ':Attribute wajib diisi.',
            'string' => ':Attribute harus berupa teks.',
            'integer' => ':Attribute harus berupa angka bulat.',
            'numeric' => ':Attribute harus berupa angka.',
            'email' => 'Format :attribute belum benar.',
            'in' => ':Attribute tidak valid.',
            'exists' => ':Attribute tidak valid.',
            'unique' => ':Attribute sudah dipakai.',
            'regex' => 'Format :attribute belum benar.',
            'confirmed' => 'Konfirmasi :attribute tidak sama.',
            'image' => ':Attribute harus berupa gambar.',
            'mimes' => ':Attribute harus berformat :values.',
            'max.string' => ':Attribute maksimal :max karakter.',
            'max.numeric' => ':Attribute maksimal :max.',
            'max.file' => ':Attribute maksimal :max KB.',
            'min.string' => ':Attribute minimal :min karakter.',
            'min.numeric' => ':Attribute minimal :min.',
            'between.numeric' => ':Attribute harus antara :min dan :max.',
        ];
    }
}
