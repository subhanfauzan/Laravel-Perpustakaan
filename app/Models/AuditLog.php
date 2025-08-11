<?php

// app/Models/AuditLog.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AuditLog extends Model
{
    protected $table = 'audit_logs';
    protected $fillable = ['buku_id','user_id','event','old_values','new_values','ip','user_agent'];
    protected $casts = ['old_values' => 'array', 'new_values' => 'array'];

    public function buku() { return $this->belongsTo(Buku::class, 'buku_id'); }
    public function user() { return $this->belongsTo(User::class, 'user_id'); }
}
