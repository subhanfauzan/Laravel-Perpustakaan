<?php

namespace App\Http\Controllers;

use App\Exports\BukuExport;
use App\Imports\BukuImport;
use App\Models\Buku;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

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
            'deskripsi' => 'nullable|string',
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
            'deskripsi' => 'nullable|string', // tambahkan ini
        ]);

        $buku->update($validated);
        return $buku;
    }

    public function destroy($id)
    {
        return Buku::destroy($id);
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls,csv|max:5120', // max 5MB
        ]);

        try {
            Excel::import(new BukuImport(), $request->file('file'));
            return response()->json(['message' => 'Import berhasil'], 201);
        } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
            $failures = [];
            foreach ($e->failures() as $failure) {
                $failures[] = [
                    'row' => $failure->row(),
                    'attribute' => $failure->attribute(),
                    'errors' => $failure->errors(),
                    'values' => $failure->values(),
                ];
            }
            return response()->json(
                [
                    'message' => 'Validasi gagal',
                    'errors' => $failures,
                ],
                422,
            );
        } catch (\Throwable $e) {
            return response()->json(
                [
                    'message' => 'Terjadi kesalahan saat import',
                    'error' => $e->getMessage(),
                ],
                500,
            );
        }
    }

    public function export()
    {
        $filename = 'buku_' . now()->format('Ymd_His') . '.xlsx';
        return Excel::download(new BukuExport(), $filename);
    }
}
