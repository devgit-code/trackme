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
        Schema::table('pings', function (Blueprint $table) {
            $table->float('lat', 10, 6)->nullable()->change();
            $table->float('lon', 10, 6)->nullable()->change();
            $table->integer('accuracy')->nullable()->change();
            $table->ipAddress('ip_address')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pings', function (Blueprint $table) {
            $table->float('lat', 10, 6)->change();
            $table->float('lon', 10, 6)->change();
            $table->integer('accuracy')->change();
            $table->string('ip_address', 64)->change();
        });
    }
};
