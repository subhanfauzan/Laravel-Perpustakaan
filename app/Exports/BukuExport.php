<?php

namespace App\Exports;

use App\Models\Buku;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class BukuExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize
{
    public function collection()
    {
        return Buku::select('judul','penulis','tahun','kategori','stok','deskripsi')->orderBy('judul')->get();
    }

    public function headings(): array
    {
        return ['judul','penulis','tahun','kategori','stok','deskripsi'];
    }

    public function map($buku): array
    {
        return [
            $buku->judul,
            $buku->penulis,
            (int) $buku->tahun,
            $buku->kategori,
            (int) $buku->stok,
            $buku->deskripsi,
        ];
    }
}
