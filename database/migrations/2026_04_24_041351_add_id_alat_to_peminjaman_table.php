<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
   public function up(): void
{
    Schema::table('peminjaman', function (Blueprint $table) {
        $table->unsignedBigInteger('id_alat')->after('id_user')->nullable();
        $table->integer('jumlah')->default(1)->after('id_alat');
    });
}
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('peminjaman', function (Blueprint $table) {
            //
        });
    }
};
