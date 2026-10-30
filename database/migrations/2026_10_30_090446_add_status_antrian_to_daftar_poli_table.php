
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('daftar_poli', function (Blueprint $table) {
            $table->enum('status_antrian', [
                'menunggu',
                'dipanggil',
                'diperiksa',
                'selesai',
            ])->default('menunggu')->after('no_antrian');

            $table->timestamp('waktu_dipanggil')
                ->nullable()
                ->after('status_antrian');

            $table->index(
                ['id_jadwal', 'status_antrian'],
                'daftar_poli_jadwal_status_index'
            );
        });
    }

    public function down(): void
    {
        Schema::table('daftar_poli', function (Blueprint $table) {
            $table->dropIndex('daftar_poli_jadwal_status_index');
            $table->dropColumn([
                'status_antrian',
                'waktu_dipanggil',
            ]);
        });
    }
};
