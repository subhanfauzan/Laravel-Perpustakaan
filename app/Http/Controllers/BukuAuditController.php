<?php

namespace App\Http\Controllers;

use App\Models\Buku;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BukuAuditController extends Controller
{
    /**
     * GET /api/buku/{id}/audits
     * Query params opsional: page, per_page, event (created|updated|deleted), from (Y-m-d), to (Y-m-d)
     */
    public function index(Request $request, $id)
    {
        try {
            // TAMBAHKAN: Validasi user authenticated
            if (!Auth::check()) {
                return response()->json([
                    'message' => 'Unauthenticated.'
                ], 401);
            }

            $buku = Buku::findOrFail($id);

            $perPage = (int) $request->query('per_page', 10);
            if ($perPage < 1)   $perPage = 10;
            if ($perPage > 100) $perPage = 100;

            $query = $buku->audits()
                ->with('user:id,nama,email')   // join user agar bisa tampil nama & email
                ->orderByDesc('created_at');

            if ($event = $request->query('event')) {
                $query->where('event', $event); // created | updated | deleted
            }

            if ($request->filled('from')) {
                $query->whereDate('created_at', '>=', $request->query('from'));
            }
            if ($request->filled('to')) {
                $query->whereDate('created_at', '<=', $request->query('to'));
            }

            $audits = $query->paginate($perPage)->appends($request->query());

            return response()->json($audits);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Buku tidak ditemukan.'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }
}
