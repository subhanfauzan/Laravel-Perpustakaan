<?php

namespace App\Http\Controllers;

use App\Models\Denda;
use Illuminate\Http\Request;

class DendaController extends Controller
{
    public function index()
    {
        return Denda::with('peminjaman')->get();
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'peminjaman_id' => 'required|exists:peminjaman,id',
            'jumlah' => 'required|numeric|min:0',
            'status_bayar' => 'required|in:belum,lunas',
        ]);

        return Denda::create($validated);
    }

    public function show($id)
    {
        return Denda::with('peminjaman')->findOrFail($id);
    }

    public function update(Request $request, $id)
    {
        $denda = Denda::findOrFail($id);

        $validated = $request->validate([
            'jumlah' => 'numeric|min:0',
            'status_bayar' => 'in:belum,lunas',
        ]);

        $denda->update($validated);
        return $denda;
    }

    public function destroy($id)
    {
        return Denda::destroy($id);
    }
}
