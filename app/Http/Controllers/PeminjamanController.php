<?php

namespace App\Http\Controllers;

use App\Models\Peminjaman;
use App\Models\Buku;
use App\Models\Denda;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Validator;

class PeminjamanController extends Controller
{
    public function index()
    {
        return Peminjaman::with(['user', 'buku', 'denda'])->get();
    }

    public function store(Request $request)
    {
        try {
            $validated = Validator::make($request->all(), [
                'user_id' => 'required|exists:users,id',
                'book_id' => 'required|exists:buku,id',
                'tgl_pinjam' => 'required|date',
                'tgl_kembali' => 'required|date|after_or_equal:tgl_pinjam',
            ]);

            if($validated->fails()) {
                return response()->json(['error' => $validated->errors()], 422);
            }

            $buku = Buku::findOrFail($validated['book_id']);
            if ($buku->stok < 1) {
                return response()->json(['error' => 'Stok buku habis'], 400);
            }

            // Kurangi stok
            $buku->decrement('stok');

            $validated['status'] = 'dipinjam';
            Peminjaman::create($validated);
            return response()->json(['message' => 'Buku berhasil ditambahkan'], 201);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }

    }

    public function show($id)
    {
        return Peminjaman::with(['user', 'buku', 'denda'])->findOrFail($id);
    }

    public function update(Request $request, $id)
    {
        try {
            $peminjaman = Peminjaman::findOrFail($id);

            $validated = $request->validate([
                'tgl_pinjam' => 'date',
                'tgl_kembali' => 'date|after_or_equal:tgl_pinjam',
                'status' => 'in:dipinjam,dikembalikan',
            ]);

            if (isset($validated['tgl_dikembalikan'])) {
                $validated['tgl_dikembalikan'] = Carbon::parse($validated['tgl_dikembalikan']);
            }

            $peminjaman->update($validated);

            // Jika status dikembalikan, tambahkan denda jika ada keterlambatan
            if ($peminjaman->status === 'dikembalikan') {
                $denda = Denda::where('peminjaman_id', $id)->first();
                if (!$denda) {
                    $denda = new Denda();
                    $denda->peminjaman_id = $id;
                }
                // Hitung denda jika ada keterlambatan
                if ($peminjaman->tgl_dikembalikan && Carbon::parse($peminjaman->tgl_dikembalikan)->gt(Carbon::parse($peminjaman->tgl_kembali))) {
                    $lateDays = Carbon::parse($peminjaman->tgl_dikembalikan)->diffInDays(Carbon::parse($peminjaman->tgl_kembali));
                    $denda->jumlah = $lateDays * 2000; // Contoh denda Rp2000 per hari
                    $denda->status_bayar = 'belum';
                    $denda->save();
                } else {
                    // Jika tidak ada keterlambatan, hapus denda
                    if ($denda->exists) {
                        $denda->delete();
                    }
                }
            }

            return response()->json(['message' => 'Data peminjaman berhasil diperbarui'], 200);
        } catch (Exception $e) {
            return response()->json(['error' => 'Terjadi kesalahan'], 500);
        }
    }

    public function destroy($id)
    {
        try {
            Peminjaman::destroy($id);
            return response()->json(['message' => 'Data peminjaman berhasil dihapus'], 200);
        } catch (Exception $e) {
            //throw $th;
            return response()->json(['error' => 'Terjadi kesalahan'], 500);
        }
    }
}
