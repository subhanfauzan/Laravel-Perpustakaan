<?php

namespace App\Http\Controllers;

use App\Models\Peminjaman;
use App\Models\Buku;
use App\Models\Denda;
use Illuminate\Http\Request;
use Carbon\Carbon;

class PeminjamanController extends Controller
{
    public function index()
    {
        return Peminjaman::with(['user', 'buku', 'denda'])->get();
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'book_id' => 'required|exists:buku,id',
            'tgl_pinjam' => 'required|date',
            'tgl_kembali' => 'required|date|after_or_equal:tgl_pinjam',
        ]);

        $buku = Buku::findOrFail($validated['book_id']);
        if ($buku->stok < 1) {
            return response()->json(['error' => 'Stok buku habis'], 400);
        }

        // Kurangi stok
        $buku->decrement('stok');

        $validated['status'] = 'dipinjam';
        return Peminjaman::create($validated);
    }

    public function show($id)
    {
        return Peminjaman::with(['user', 'buku', 'denda'])->findOrFail($id);
    }

    public function update(Request $request, $id)
    {
        $peminjaman = Peminjaman::findOrFail($id);

        $validated = $request->validate([
            'tgl_dikembalikan' => 'nullable|date',
            'status' => 'in:dipinjam,dikembalikan',
        ]);

        $peminjaman->update($validated);

        // Jika status dikembalikan → tambah stok buku + cek denda
        if ($validated['status'] === 'dikembalikan' && $peminjaman->tgl_dikembalikan) {
            $buku = Buku::find($peminjaman->book_id);
            if ($buku) {
                $buku->increment('stok');
            }

            $dueDate = Carbon::parse($peminjaman->tgl_kembali);
            $returnDate = Carbon::parse($peminjaman->tgl_dikembalikan);

            if ($returnDate->gt($dueDate)) {
                $daysLate = $dueDate->diffInDays($returnDate);
                $amount = $daysLate * 2000; // denda 2000/hari
                Denda::create([
                    'peminjaman_id' => $peminjaman->id,
                    'jumlah' => $amount,
                    'status_bayar' => 'belum',
                ]);
            }
        }

        return $peminjaman->load(['user', 'buku', 'denda']);
    }

    public function destroy($id)
    {
        return Peminjaman::destroy($id);
    }
}
