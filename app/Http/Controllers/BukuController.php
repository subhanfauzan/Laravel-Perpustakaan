<?php

namespace App\Http\Controllers;

use App\Models\Buku;
use Illuminate\Http\Request;

class BukuController extends Controller
{
    public function index()
    {
        return Buku::all();
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'judul' => 'required|string|max:150',
            'penulis' => 'required|string|max:100',
            'tahun' => 'required|digits:4|integer',
            'kategori' => 'required|string|max:50',
            'stok' => 'required|integer|min:0',
        ]);

        Buku::create($validated);
        return response()->json(['message' => 'Buku berhasil ditambahkan'], 201);
    }

    public function show($id)
    {
        return Buku::findOrFail($id);
    }

    public function update(Request $request, $id)
    {
        $buku = Buku::findOrFail($id);

        $validated = $request->validate([
            'judul' => 'string|max:150',
            'penulis' => 'string|max:100',
            'tahun' => 'digits:4|integer',
            'kategori' => 'string|max:50',
            'stok' => 'integer|min:0',
        ]);

        $buku->update($validated);
        return $buku;
    }

    public function destroy($id)
    {
        return Buku::destroy($id);
    }
}
