<?php

// app/Models/Traits/BukuAuditable.php
namespace App\Models\Traits;

use App\Models\AuditLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

trait BukuAuditable
{
    public static function bootBukuAuditable()
    {
        // CREATE
        static::created(function ($buku) {
            $buku->writeBukuAudit('created', null, $buku->getAttributes());
        });

        // UPDATE
        static::updating(function ($buku) {
            // hanya catat field yang benar-benar berubah
            $old = array_intersect_key($buku->getOriginal(), $buku->getDirty());
            $new = $buku->getDirty();

            if (!empty($new)) {
                $buku->writeBukuAudit('updated', $old, $new);
            }
        });

        // DELETE
        static::deleted(function ($buku) {
            $buku->writeBukuAudit('deleted', $buku->getOriginal(), null);
        });
    }

    protected function writeBukuAudit(string $event, $old, $new): void
    {
        try {
            $userId = Auth::id() ?? optional(request()->user())->id ?? optional(auth('sanctum')->user())->id;

            AuditLog::create([
                'buku_id'    => $this->getKey(),
                'user_id' => $userId,
                'event'      => $event,
                'old_values' => $old,
                'new_values' => $new,
                'ip'         => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);
        } catch (\Throwable $e) {
            Log::warning('Audit buku gagal: '.$e->getMessage());
        }
    }

    // Helper relasi audit untuk Buku
    public function audits()
    {
        return $this->hasMany(\App\Models\AuditLog::class, 'buku_id')->latest();
    }
}
