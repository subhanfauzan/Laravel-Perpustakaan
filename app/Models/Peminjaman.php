<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Peminjaman extends Model
{
    use SoftDeletes;

    protected $table = 'peminjaman';
    protected $fillable = ['user_id', 'book_id', 'tgl_pinjam', 'tgl_kembali', 'tgl_dikembalikan', 'status'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function buku()
    {
        return $this->belongsTo(Buku::class, 'book_id');
    }

    public function denda()
    {
        return $this->hasOne(Denda::class);
    }
}
