<?php

namespace App\Models;

use App\Models\Traits\BukuAuditable;
use Illuminate\Database\Eloquent\Model;

class Buku extends Model
{
    use BukuAuditable;

    protected $table = 'buku';
    protected $fillable = ['judul', 'penulis', 'tahun', 'kategori', 'stok', 'deskripsi'];

    public function peminjaman()
    {
        return $this->hasMany(Peminjaman::class, 'book_id');
    }
}
