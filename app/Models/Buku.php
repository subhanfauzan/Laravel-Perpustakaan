<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Buku extends Model
{
    protected $table = 'buku';
    protected $fillable = ['judul', 'penulis', 'tahun', 'kategori', 'stok'];

    public function peminjaman()
    {
        return $this->hasMany(Peminjaman::class, 'book_id');
    }
}
