<?php

namespace App\Imports;

use App\Models\Buku;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class BukuImport implements ToModel, WithHeadingRow, WithValidation
{
    public function model(array $row)
    {
        // Lewatkan baris kosong
        if (empty($row['judul'])) return null;

        return new Buku([
            'judul'     => trim((string)($row['judul'] ?? '')),
            'penulis'   => trim((string)($row['penulis'] ?? '')),
            'tahun'     => (int)($row['tahun'] ?? date('Y')),
            'kategori'  => trim((string)($row['kategori'] ?? 'Lainnya')),
            'stok'      => (int)($row['stok'] ?? 0),
            'deskripsi' => $row['deskripsi'] ?? null,
        ]);
    }

    public function rules(): array
    {
        return [
            '*.judul'    => ['required','string','max:150'],
            '*.penulis'  => ['required','string','max:100'],
            '*.tahun'    => ['required','integer','digits:4','min:1900','max:'.date('Y')],
            '*.kategori' => ['required','string','max:50'],
            '*.stok'     => ['required','integer','min:0'],
            '*.deskripsi'=> ['nullable','string'],
        ];
    }
}
