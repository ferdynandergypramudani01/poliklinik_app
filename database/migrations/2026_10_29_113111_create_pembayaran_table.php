
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pembayaran', function (Blueprint $table) {
            $table->id();

            $table->foreignId('id_periksa')
                ->unique()
                ->constrained('periksa')
                ->cascadeOnDelete();

            $table->string('bukti_pembayaran')->nullable();

            $table->enum('status', [
                'menunggu_pembayaran',
                'menunggu_verifikasi',
                'lunas',
                'ditolak',
            ])->default('menunggu_pembayaran');

            $table->text('catatan_admin')->nullable();

            $table->foreignId('verified_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamp('verified_at')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pembayaran');
    }
};
