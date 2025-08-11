<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('audit_logs', function (Blueprint $t) {
            $t->id();
            // khusus Buku saja → tidak perlu morphs
            $t->unsignedBigInteger('buku_id');      // refer ke tabel buku
            $t->foreign('buku_id')->references('id')->on('buku')->cascadeOnDelete();

            $t->unsignedBigInteger('user_id')->nullable(); // pelaku (opsional)
            $t->string('event');                // created | updated | deleted
            $t->json('old_values')->nullable(); // nilai sebelum
            $t->json('new_values')->nullable(); // nilai sesudah
            $t->string('ip')->nullable();
            $t->string('user_agent')->nullable();
            $t->timestamps();

            $t->index(['buku_id','event']);
        });
    }
    public function down(): void {
        Schema::dropIfExists('audit_logs');
    }
};
